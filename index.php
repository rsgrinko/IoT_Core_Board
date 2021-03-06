<?php
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/header.php';

$cacheId = md5('CIoT::getSensorsArrData_'.$USER['deviceId']);

if(Cache::check($cacheId) and Cache::getAge($cacheId) < 60) {
    $arSensors = Cache::get($cacheId);
} else {
    $arSensors = IoT::getSensorsArrData($USER['deviceId']);
    Cache::write($cacheId, $arSensors);
}


?>
    <!-- Page JS Code -->
    <script src="assets/js/pages/index.js"></script>
    <div class="test-draggable-items row">

        <?php if(empty($arSensors)): ?>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-block">
                        <div class="alert alert-danger"><p>Нет данных для отображения. Проверьте контроллер на наличие доступа к сети интернет.</p></div>    </div>
                </div>
            </div>
        <?php endif; ?>


        <?php foreach ($arSensors as $arSensor): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card" id="displaySensor_<?php echo $arSensor['sensor']; ?>">
                    <div class="card-header bg-cyan bg-inverse custom_text_center">
                        <h4><i class="<?php echo IoT::getHumanSensorName($arSensor['sensor'])['icon']; ?>"></i>
                               <?php echo IoT::getHumanSensorName($arSensor['sensor'])['name'].' ('.$arSensor['sensor'].')'; ?>
                        </h4>
                        <ul class="card-actions">
                            <li>
                                <button type="button"
                                        onclick="App.cards('#displaySensor_<?php echo $arSensor['sensor']; ?>', 'content_toggle' );">
                                    <i class="ion-ios-toggle"></i></button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-block">
                        <p>
                        <div class="monitoring_value">
                            <span id="<?php echo $arSensor['sensor']; ?>"><?php echo $arSensor['value']; ?></span>
                            <?php echo IoT::getHumanSensorName($arSensor['sensor'])['unit'] ?></div>
                        </p>
                    </div>
                </div>
            </div>
            <!-- .col-sm-6 -->
        <?php endforeach; ?>
        <script>
            $(document).ready(function () {
                setInterval(function () {
                    $.ajax({
                        url: "ajax/getSensorsData.php",
                        data: {
                            deviceId: "<?php echo $USER['deviceId']; ?>"
                        },
                        success: function (data) {
                            for (var key in data) {
                                //$('#' + key).parent().slideUp('fast')
                                $('#' + key).text(data[key].value);
                                //$('#' + key).parent().slideDown('fast')
                            }
                        },
                        dataType: "json"
                    });
                }, 30000);
            });
        </script>

    </div>
    <!------>

    <div class="test-draggable-items row">
        <?php
        IoT::initHighcharts();
        //pre($arSensors);
        foreach ($arSensors as $arSensor):
            $cacheId = md5('CIoT::getPlotDallasValues_'.$USER['deviceId'].'_'.$arSensor['sensor']);
            if(Cache::check($cacheId) and Cache::getAge($cacheId) < 300) {
                $arValues = Cache::get($cacheId);
            } else {
                $arValues = IoT::getPlotDallasValues($USER['deviceId'], $arSensor['sensor']);
                Cache::write($cacheId, $arValues);
            }

            
            ?>
            <div class="col-sm-6 col-lg-6">
                <div class="card" id="graphCard_<?php echo $arSensor['sensor']; ?>">
                    <div class="bg-inverse bg-purple card-header">
                        <h4><i class="ion-stats-bars"></i> История показаний
                            датчика <?php echo strtoupper(str_replace('ds', 'DS18B20_', $arSensor['sensor'])); ?></h4>
                        <ul class="card-actions">
                            <li>
                                <button type="button"
                                        onclick="App.cards('#graphCard_<?php echo $arSensor['sensor']; ?>', 'content_toggle' );">
                                    <i class="ion-ios-toggle"></i></button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-block">
                        <p>
                            <?php if (count($arValues) > 1): ?>
                        <div id="plotContainer_<?php echo $arSensor['sensor']; ?>"></div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p>Недостаточно показаний для отрисовки графика</p>
                            </div>
                        <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php


            if (count($arValues) > 1) {
                IoT::drawHighcharts($arSensor['sensor'], $arValues);
            }

            ?>
        <?php endforeach; ?>
    </div>
    <!-------------->

    <script>
        $(document).ready(function () {
            $(".test-draggable-items.row").sortable();
        });
    </script>

<?php
require_once __DIR__ . '/inc/footer.php';
?>