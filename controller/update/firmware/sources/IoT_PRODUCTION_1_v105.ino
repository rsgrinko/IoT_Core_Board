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
#include <ArduinoJson.h>

const int FW_VER = 115;              // Версия прошивки
#define HW_VER "PRODUCTION_1"               // Аппаратная версия устройства

#define CONFIG_AP_NAME "IoT_Board_v1.1.5"  // название сети WiFi для настройки устройства

#define UPDATE_INTERVAL 60000           //Каждый 60 сек проверяем обновления
#define SEND_DELAY 2000                 //Задержка между запросами к серверу (для снижения нагрузки на гребаный хостинг)
#define POOLING_SENSORS_DELAY 3000      //Задержка между опросами датчиков ds18b20
#define CHECK_CONFIG_INTERVAL 10000     //Интервал получения конфигурации с сервера
#define MQTT_SEND_INTERVAL 3000         //Интервал отправки показаний на mqtt сервер

#define CHECK_UPDATE_URL "http://new-dev.it-stories.ru/controller/update/check.php?"                 // адрес проверки обновлений
#define GET_UPDATE_URL   "http://new-dev.it-stories.ru/controller/update/check.php?act=getupdate&"   // адрес получения обновлений
#define IOT_HANDLER_URL  "http://new-dev.it-stories.ru/controller/handler.php?"                      // главный обработчик
#define GET_CONFIG_URL   "http://new-dev.it-stories.ru/controller/getConfig.php?"                    // адрес получения конфигуркции контроллера

#define GREEN_LED D6        //зеленый индикатор
#define YELLOW_LED D5       //желтый индикатор
#define ONE_WIRE_BUS D7     // шина для датчиков температуры

//Сопостовляем пины esp с релюшками
const int out1 = D1;
const int out2 = D2;
const int out3 = D3;
const int out4 = D4;

// забиваем константы данными о плате
const String boardMacAddress = WiFi.macAddress();
const String boardChipID = (String)ESP.getChipId();
const String boardFlashChipID = (String)ESP.getFlashChipId();
const String boardFlashChipFrequency = (String)ESP.getFlashChipSpeed();
const String boardFlashChipSize = (String)ESP.getFlashChipSize();
const String boardFreeHeap = (String)ESP.getFreeHeap();

int dallas_resolution = 9; // разрешения датчиков DALLAS по умолчанию


StaticJsonDocument<400> doc; // определяем буфер для json
WiFiManager wm;

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

float ds1, ds2, ds3, ds4, ds5, ds6, ds7, ds8, ds9, ds10; // создаем глобальные температурные переменные

/*
  Тут определяем переменные для параметров, полученных с сервера
*/
/*bool use_mqtt = false;      // использовать ли MQTT сервер
String mqtt_server = "";    // MQTT сервер
int mqtt_port = 1883;       // порт MQTT сервера
String mqtt_topic;     // MQTT топик для публикации показаний
*/


uint32_t mainTimer;             // главный таймер для отправки телеметрии (unsigned long)
uint32_t poolingSensorsTimer;   // таймер для опроса датчиков
uint32_t updateTimer;           // таймер проверки обновлений
uint32_t checkConfigTimer;      // таймер получения конфигурации с сервера

void setup() {
  Serial.begin(115200);
  digitalWrite(YELLOW_LED, HIGH);
  sensors.begin(); //стартуем для датчика
  delay(10);
  sensors.setResolution(dallas_resolution); //Устанавливаем разрешение датчиков температуры по умолчанию

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

  digitalWrite(YELLOW_LED, LOW);
  ledCode(4);                         // мигаем 4 раза, типа инициализация пройдена

  ota_update();                       // проверим обновления при включении и обновимся по возможности
  poolingSensors();                   // опрашиваем датчики в первый раз для заполнения переменных
  getJsonFromServer();                // получаем конфигурацию с сервера
  Serial.println("=> Setup success");
}




void loop() {

  // получение конфигурации с сервера
  if(millis() - checkConfigTimer >= CHECK_CONFIG_INTERVAL){
    checkConfigTimer = millis();
    getJsonFromServer();
  }

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
    sendRequest();
 }/////


}//loop


/*
  Функция получения конфигурации с сервера
*/
void getJsonFromServer(){
  String url = GET_CONFIG_URL"mac=" + boardMacAddress;
  if(http.begin(client, url)){
    int httpCode = http.GET();
    if (httpCode > 0) {
      if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
        String payload = http.getString(); // получили ответ
        DeserializationError error = deserializeJson(doc, payload); // парсим json
        if (error) {
          Serial.print("=> Error load config from server: ");
          Serial.println(error.f_str());
          dallas_resolution = 9; // при ошибке устанавливаем разрешение по умолчанию
          return;
        }

        dallas_resolution = (int)doc["dallas_resolution"];
        /*
        use_mqtt = (bool)doc["use_mqtt"];
        mqtt_server = (String)doc["mqtt_server"];
        mqtt_port = (int)doc["mqtt_port"];
        mqtt_topic = (String)doc["mqtt_topic"];
        */
        Serial.println("* * * * * LOADED CONFIG FROM SERVER * * * * *");
        Serial.println(payload);
        Serial.println("* * * * * * * * * * * * * * * * * * * * * * *");
      } else {
        Serial.println("=> Impossible to get the configuration from the server. HTTP code other than 200");
      }
    } else {
      Serial.println("=> Impossible to get the configuration from the server. Incorrect HTTP code");
    }
  }
}////

/*
  Функция отправки данных на сервер и получения управляющих комманд
*/
void sendRequest(){
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
}

/*
  Функция отключающая все реле (SAFETY)
*/
void relaysOff() {
  digitalWrite(out1, HIGH); //выключаем по умолчанию
  digitalWrite(out2, HIGH);
  digitalWrite(out3, HIGH);
  digitalWrite(out4, HIGH);
}

/*
  // Функция моргания кода ошибки
*/
void ledCode(int num) {
  for (int i = 0; i < num; i++) {

    digitalWrite(YELLOW_LED, HIGH);
    delay(250);
    digitalWrite(YELLOW_LED, LOW);
    delay(250);
  }
  delay(500);
}

/*
  Инициализация портов ввода/вывода
*/
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
  if (http.begin(client, CHECK_UPDATE_URL"hw="HW_VER"&fw=" + String(FW_VER) + "&mac=" + boardMacAddress + "&chipId=" + boardChipID)) {
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

          if (http.begin(client, GET_UPDATE_URL"hw="HW_VER)) {
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

    Serial.println("* * * * ** * E N D  D E B U G  D A L L A S * * * * * * *");
}

void printBoardInfo(){
    Serial.println("\n\n\n");
    Serial.println("=> Loading kernel...");
    Serial.println("*** IoT Core System");
    Serial.println("*** HW: "HW_VER", FW: "+String(FW_VER));
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


/*
  Функция опроса датчиков температуры
*/
void poolingSensors(){
    sensors.setResolution(dallas_resolution);
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
}

/*
   Функция формирует адрес запроса к серверу
*/
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
    url += "&mac=";
    url += boardMacAddress;
    url += "&chipid=";
    url += boardChipID;
    url += "&analog=";
    url += analogValue;

    return url;
}

/*
  Функция-обработчик комманд включения/выключения реле, полученных от сервера
*/
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