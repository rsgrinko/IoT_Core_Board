///////////////////////////////////////////////
//           Прошивка для NodeMCU            //
//    Для системы nmcu-garage-controller     //
//              гаражный контроллер          //
//-------------------------------------------//
//        Автор: Гринько Роман Сергеевич     //
//             rsgrinko@gmail.com            //
///////////////////////////////////////////////
#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <OneWire.h>
#include <DallasTemperature.h>
#include <ESP8266httpUpdate.h>
#include <WiFiManager.h>
WiFiManager wm;

const int FW_VER = 113;               // Версия прошивки
#define HW_VER "DEV_1"         // Аппаратная версия устройства
#define DEVID "1-0002"                // ID Устройства

#define CONFIG_AP_NAME "IoT_Board_v1.1.3"  // название сети WiFi для настройки устройства

#define UPDATE_INTERVAL 60000           //Каждый 60 сек проверяем обновления
#define SEND_DELAY 2000                 //Задержка между запросами к серверу (для снижения нагрузки на гребаный хостинг)
#define POOLING_SENSORS_DELAY 3000      //Задержка между запросами к серверу (для снижения нагрузки на гребаный хостинг)#define SEND_DELAY 2000          //Задержка между запросами к серверу (для снижения нагрузки на гребаный хостинг)

#define CHECK_UPDATE_URL "http://new.it-stories.ru/controller/update/check.php?"                 // адрес проверки обновлений
#define GET_UPDATE_URL   "http://new.it-stories.ru/controller/update/check.php?act=getupdate&"   // адрес получения обновлений
#define IOT_HANDLER_URL "http://new.it-stories.ru/controller/handler.php?"                       // главный обработчик

#define GREEN_LED D6        //зеленый индикатор
#define YELLOW_LED D5       //желтый индикатор
#define ONE_WIRE_BUS D7     // шина для датчиков температуры


int update_counter = UPDATE_INTERVAL;  //Счетчик пакетов (для обновлений). Для проверки обновлений при включении приравниваем ее к константе

WiFiClient client;
HTTPClient http;

OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

//Задаем буфера каналов реле в ноль
int buff1 = 0;
int buff2 = 0;
int buff3 = 0;
int buff4 = 0;

int analogValue;

//Сопостовляем пины esp с релюшками
const int out1 = D1;
const int out2 = D2;
const int out3 = D3;
const int out4 = D4;
   
const String boardMacAddress = WiFi.macAddress();
const String boardChipID = (String)ESP.getChipId();
const String boardFlashChipID = (String)ESP.getFlashChipId();
const String boardFlashChipFrequency = (String)ESP.getFlashChipSpeed();
const String boardFlashChipSize = (String)ESP.getFlashChipSize();
const String boardFreeHeap = (String)ESP.getFreeHeap();

float ds1, ds2, ds3, ds4, ds5, ds6, ds7, ds8, ds9, ds10; // создаем глобальные температурные переменные

uint32_t mainTimer;             // главный таймер для отправки телеметрии (unsigned long)
uint32_t poolingSensorsTimer;   // таймер для опроса датчиков
uint32_t updateTimer;           // таймер проверки обновлений

void setup() {
  Serial.begin(115200);
  digitalWrite(YELLOW_LED, HIGH);
  sensors.begin(); //стартуем для датчика
  delay(10);
  sensors.setResolution(9); //Устанавливаем разрешение датчиков температуры

  inittialPins();     // определяем пины
  relaysOff();        // выключаем все каналы реле
  printBoardInfo();   // выводим информацию о контроллере
  
  WiFi.mode(WIFI_STA);
  
  bool res;
  res = wm.autoConnect(CONFIG_AP_NAME);
  
  if(!res) {
    Serial.println("=> WM: Failed to connect. Restart board...");
    ESP.restart();
  } else { 
    Serial.println("=> WM: Connected success");
  }
  mainTimer = 0; // обнуляем главный таймер
  
  digitalWrite(YELLOW_LED, LOW);
  ledCode(4);

  ota_update(); // проверим обновления при включении и обновимся по возможности
  poolingSensors(); // опрашиваем датчики в первый раз для заполнения переменных
  Serial.println("=> Setup success");
}




void loop() {
 analogValue = map(analogRead(A0), 0, 1024, 0, 255); // измеряем аналоговое значение пина
  
 // опрос датчиков
 if(millis() - poolingSensorsTimer >= POOLING_SENSORS_DELAY){
    poolingSensorsTimer = millis();
    poolingSensors(); // опрашиваем датчики
 }

 // проверка обновлений
 if(millis() - updateTimer >= UPDATE_INTERVAL){
      Serial.println("OTA RUN");
      updateTimer = millis();
      ota_update();
 }

 // отправка телеметрии и получение комманд
 if(millis() - mainTimer >= SEND_DELAY){
    mainTimer = millis();
    
    //Здесь происходит формирование запроса для 10 (или меньше 10) подключенных датчиков температуры DS18B20, которые висят на линии
    String url = createQueryString(IOT_HANDLER_URL);
    Serial.println("=> Request url: " + url);
    printDebugDallasToSerial(); // выводим в консоль показания с датчиков
    Serial.print("=> [HTTP] begin...\n");

    if (http.begin(client, url)) {  // HTTP
      Serial.print("=> [HTTP] GET...\n");
      int httpCode = http.GET();
      if (httpCode > 0) {
        Serial.printf("=> [HTTP] GET... code: %d\n", httpCode);
        if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
          update_counter++;// Счетчик успешных запросов к серверу (для цикла обновления)
          digitalWrite(GREEN_LED, HIGH); //Запросили,все ок, читаем, зажигаем зеленый светодиод
          String payload = http.getString(); // Получили ответ от сервера в строку

          char charPayload[5];
          payload.toCharArray(charPayload, 5); // Разбиваем ответ на символы
          
          //Обработка реле. Включаем или выключаем
          relayHandler(charPayload);
          delay(100); // иначе диодика не видно
          digitalWrite(GREEN_LED, LOW); //Запрос выполнен, гасим зеленый светодиод
        }
      } else {
        relaysOff();
        Serial.printf("=> [HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
        ledCode(5); // Моргаем 5 раз (код ответа отличен от 200)
      }

      http.end();
     
    } else {//http
      relaysOff();
      Serial.printf("=> [HTTP} Unable to connect\n");
      ledCode(2); // Моргаем 2 раза (невозможно подключиться к серверу)
      Serial.println("=> No wifi connect. Reconnecting...");
    }
}/////



}//loop





void relaysOff() { //Функция отключающая все реле (SAFETY)
  digitalWrite(out1, HIGH); //выключаем по умолчанию
  digitalWrite(out2, HIGH);
  digitalWrite(out3, HIGH);
  digitalWrite(out4, HIGH);
}

void ledCode(int num) { // Функция моргания кода ошибки
  for (int i = 0; i < num; i++) {

    digitalWrite(YELLOW_LED, HIGH);
    delay(250);
    digitalWrite(YELLOW_LED, LOW);
    delay(250);
  }
  delay(500);
}

void inittialPins(){
  //Задаем пины контроллера на выход для управления реле
  pinMode(out1, OUTPUT);
  pinMode(out2, OUTPUT);
  pinMode(out3, OUTPUT);
  pinMode(out4, OUTPUT);

  pinMode(YELLOW_LED, OUTPUT); //Желтый диод - выход
  pinMode(GREEN_LED, OUTPUT);  //Зеленый диод - выход
}

/* ----- OTA UPDATE  ----------*/
void update_started() {
  relaysOff();
  Serial.println("=> CALLBACK:  HTTP update process started");
}

void update_finished() {
  Serial.println("=> CALLBACK:  HTTP update process finished");
}

void update_progress(int cur, int total) {
  Serial.printf("=> CALLBACK:  HTTP update process at %d of %d bytes...\n", cur, total);
}

void update_error(int err) {
  Serial.printf("=> CALLBACK:  HTTP update fatal error code %d\n", err);
}

void ota_update() {


  
  String update_file = "";
  delay(50);
  Serial.println("=> Проверка обновлений...");
  if (http.begin(client, CHECK_UPDATE_URL"hw="HW_VER"&devid="DEVID)) {
    int httpCode = http.GET();
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
        String payload = http.getString();
        Serial.print("=> Последняя доступная версия: ");
        Serial.println(payload);
        if (payload.toInt() > FW_VER) {
          Serial.println("!!! Доступно обновление !!!");
          http.end();
          delay(100);
          //////////////////////////////////////////////////////
          //GET UPDATE FILE URL

          if (http.begin(client, GET_UPDATE_URL"hw="HW_VER"&devid="DEVID)) {
            int httpCode = http.GET();
            if (httpCode > 0) {
              if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
                String update_file = http.getString();
                http.end();
                Serial.println(update_file);
                //UPDATE
                /*******************************************************/
                ESPhttpUpdate.setLedPin(GREEN_LED, LOW);
                ESPhttpUpdate.onStart(update_started);
                ESPhttpUpdate.onEnd(update_finished);
                ESPhttpUpdate.onProgress(update_progress);
                ESPhttpUpdate.onError(update_error);

                t_httpUpdate_return ret = ESPhttpUpdate.update(client, update_file);
                switch (ret) {
                  case HTTP_UPDATE_FAILED:
                    Serial.printf("HTTP_UPDATE_FAILD Error (%d): %s\n", ESPhttpUpdate.getLastError(), ESPhttpUpdate.getLastErrorString().c_str());
                    break;

                  case HTTP_UPDATE_NO_UPDATES:
                    Serial.println("HTTP_UPDATE_NO_UPDATES");
                    break;

                  case HTTP_UPDATE_OK:
                    Serial.println("HTTP_UPDATE_OK");
                    break;
                }
                /*******************************************************/
              }
            }
          }
          //////////////////////////////////////////////////////
        } else {
          Serial.println("=> У вас актуальная версия прошивки.");
        }

      }
      //http.end();
    }
  }
}

void printDebugDallasToSerial(){
    Serial.println("* * * * * * * * * D E B U G  D A L L A S * * * * * * * * *");
    Serial.print("* * * DS1: ");
    Serial.print(ds1);
    Serial.print(" * * ");

    Serial.print("DS2: ");
    Serial.print(ds2);
    Serial.print(" * * ");

    Serial.print("DS3: ");
    Serial.print(ds3);
    Serial.println(" * * *");

    Serial.print("* * * DS4: ");
    Serial.print(ds4);
    Serial.print(" * * ");

    Serial.print("DS5: ");
    Serial.print(ds5);
    Serial.print(" * * ");

    Serial.print("DS6: ");
    Serial.print(ds6);
    Serial.println(" * * *");

    Serial.print("* * * DS7: ");
    Serial.print(ds7);
    Serial.print(" * * ");

    Serial.print("DS8: ");
    Serial.print(ds8);
    Serial.print(" * * ");

    Serial.print("DS9: ");
    Serial.print(ds9);
    Serial.println(" * * *");

    Serial.print("* * * DS10: ");
    Serial.print(ds10);
    Serial.println();


//
    /*Serial.print("DS2:  ");
    Serial.println(ds2);

    Serial.print("DS3:  ");
    Serial.println(ds3);

    Serial.print("DS4:  ");
    Serial.println(ds4);

    Serial.print("DS5:  ");
    Serial.println(ds5);

    Serial.print("DS6:  ");
    Serial.println(ds6);

    Serial.print("DS7:  ");
    Serial.println(ds7);


    Serial.print("DS8:  ");
    Serial.println(ds8);

    Serial.print("DS9:  ");
    Serial.println(ds9);

    Serial.print("DS10:  ");
    Serial.println(ds10);*/
    Serial.println("* * * * ** * E N D  D E B U G  D A L L A S * * * * * * *");
}

void printBoardInfo(){
    Serial.println("\n\n\n");
    Serial.println("=> Loading kernel...");
    Serial.println("*** IoT Core System");
    Serial.println("*** HW: "HW_VER", FW: "+String(FW_VER));
    Serial.println("*** Device ID: "DEVID);
    Serial.println("*** ChipID: " + boardChipID);
    Serial.println("*** Flash ChipID: " + boardFlashChipID);
    Serial.println("*** Flash Chip Frequency: " + boardFlashChipFrequency);
    Serial.println("*** Flash Chip Size: " + boardFlashChipSize);
    Serial.println("*** Free Heap: " + boardFreeHeap);
    Serial.println("*** MAC: " + boardMacAddress);
    Serial.print("*** Build: ");
    Serial.print(__DATE__);
    Serial.print(" ");
    Serial.println(__TIME__);
    Serial.println("*** Made by Roman Sergevitch Grinko <rsgrinko@gmail.com>");
    Serial.println("=> System starting...");
}


// функция опроса датчиков температуры
void poolingSensors(){
    sensors.requestTemperatures(); //получаем температуру с датчика
    delay(100);
    
    ds1 = sensors.getTempCByIndex(0);
    ds2 = sensors.getTempCByIndex(1);
    ds3 = sensors.getTempCByIndex(2);
    ds4 = sensors.getTempCByIndex(3);
    ds5 = sensors.getTempCByIndex(4);
    ds6 = sensors.getTempCByIndex(5);
    ds7 = sensors.getTempCByIndex(6);
    ds8 = sensors.getTempCByIndex(7);
    ds9 = sensors.getTempCByIndex(8);
    ds10 = sensors.getTempCByIndex(9);

    /*ds1 = random(0, 40);
    ds2 = random(0, 40);
    ds3 = random(0, 40);
    ds4 = random(0, 40);
    ds5 = random(0, 40);
    ds6 = random(0, 40);
    ds7 = random(0, 40);
    ds8 = random(0, 40);
    ds9 = random(0, 40);
    ds10 = random(0, 40);*/
}

// функция формирует адрес запроса к серверу
String createQueryString(String start_url){
    String url = start_url;
    url += "ds[]=";
    url += ds1;
    url += "&ds[]=";
    url += ds2;
    url += "&ds[]=";
    url += ds3;
    url += "&ds[]=";
    url += ds4;
    url += "&ds[]=";
    url += ds5;
    url += "&ds[]=";
    url += ds6;
    url += "&ds[]=";
    url += ds7;
    url += "&ds[]=";
    url += ds8;
    url += "&ds[]=";
    url += ds9;
    url += "&ds[]=";
    url += ds10;
    url += "&hw=";
    url += HW_VER;
    url += "&fw=";
    url += FW_VER;
    url += "&devid=";
    url += DEVID;
    url += "&mac=";
    url += boardMacAddress;
    url += "&chipid=";
    url += boardChipID;
    url += "&analog=";
    url += analogValue;

    return url;
}


void relayHandler(char charPayload[]){
          for (int i = 0; i < 4; i = i + 1) {
            Serial.println("=> Command " +(String)i + ": " + charPayload[i]);
          }
          if (charPayload[0] == '1') {
            digitalWrite (out1, LOW);
            Serial.println("=> R1 is ON");
          } else {
            Serial.println("=> R1 is OFF");
            digitalWrite(out1, HIGH);
          }
          if (charPayload[1] == '1') {
            Serial.println("=> R2 is ON");
            digitalWrite (out2, LOW);
          } else {
            Serial.println("=> R2 is OFF");
            digitalWrite(out2, HIGH);
          }
          if (charPayload[2] == '1') {
            Serial.println("=> R3 is ON");
            digitalWrite (out3, LOW);
          } else {
            Serial.println("=> R3 is OFF");
            digitalWrite(out3, HIGH);
          }
          if (charPayload[3] == '1') {
            Serial.println("=> R4 is ON");
            digitalWrite (out4, LOW);
          } else {
            Serial.println("=> R4 is OFF");
            digitalWrite(out4, HIGH);
          }
}