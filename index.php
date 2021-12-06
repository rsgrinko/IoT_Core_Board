<?php
require_once __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/header.php';

$arSensors = CIoT::getDallasArrData($USER['deviceId']);

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
                        <h4><i class="<?php if (stristr($arSensor['sensor'], 'dht_h')) {
                                echo 'fa fa-flask';
                            } else {
                                echo 'ion-thermometer';
                            } ?>"></i>
                            Датчик <?php echo strtoupper(str_replace('ds', 'DS18B20_', $arSensor['sensor'])); ?></h4>
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
                        <div class="monitoring_value"><span
                                    id="<?php echo $arSensor['sensor']; ?>"><?php echo $arSensor['value']; ?></span> <?php if (stristr($arSensor['sensor'], 'dht_h')) {
                                echo '%';
                            } else {
                                echo 'C';
                            } ?></div>
                        </p>
                    </div>
                </div>
            </div>
            <!-- .col-sm-6 -->
            <script>
                var <?php echo $arSensor['sensor'];?> = <?php echo $arSensor['value'];?>;
                $(document).ready(function () {
                    setInterval(function () {
                        $.ajax({
                            url: "ajax/getDallasData.php",
                            data: {
                                deviceId: "<?php echo $USER['deviceId']; ?>",
                                sensor: "<?php echo $arSensor['sensor']; ?>"
                            },
                            success: function (data) {
                                $('#<?php echo $arSensor['sensor'];?>').text(data.value);
                                <?php echo $arSensor['sensor'];?> = data.value;
                            },
                            dataType: "json"
                        });
                    }, 3000);
                });
            </script>
        <?php endforeach; ?>
    </div>
    <!------>

    <div class="test-draggable-items row">
        <?php
        CIoT::initHighcharts();
        //pre($arSensors);
        foreach ($arSensors as $arSensor):
            $cacheId = md5('CIoT::getPlotDallasValues_'.$USER['deviceId'].'_'.$arSensor['sensor']);
            if(CCache::checkCache($cacheId) and CCache::ageOfCache($cacheId) < 300) {
                $arValues = CCache::getCache($cacheId);
            } else {
                $arValues = CIoT::getPlotDallasValues($USER['deviceId'], $arSensor['sensor']);
                CCache::writeCache($cacheId, $arValues);
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
                CIoT::drawHighcharts($arSensor['sensor'], $arValues);
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
require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/footer.php';
?>