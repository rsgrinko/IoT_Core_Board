<?php
/*
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/

/**
 * Защита от запуска файла без подключения ядра
 */
if(!defined('CORE_LOADED') or CORE_LOADED !== true) {
    die();
}

$finish_time = microtime(true);
$delta = round($finish_time - START_TIME, 3);
if ($delta < 0.001) {
	$delta = 0.001;
}
echo '<span class="footer_debug">Использовано памяти: '.round(memory_get_usage() / 1024 / 1024, 2).' МБ / Время обращений к базе: '.round($DB::$workingTime, 3).' сек ('.$DB::$quantity.' шт.) / Сгенерировано за '.$delta.' сек</span>';
?>
            </div>
        </main>
    </div>
</div>

        <div id="apps-modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-sm modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <!-- Apps card -->
                    <div class="card m-b-0">
                        <div class="card-header bg-app bg-inverse">
                            <h4>Основное</h4>
                            <ul class="card-actions">
                                <li>
                                    <button data-dismiss="modal" type="button"><i class="ion-close"></i></button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-block">
                            <div class="row text-center">
                                <div class="col-xs-6">
                                    <a class="card card-block m-b-0 bg-app-secondary bg-inverse" href="index.html">
                                        <i class="ion-speedometer fa-4x"></i>
                                        <p>Admin</p>
                                    </a>
                                </div>
                                <div class="col-xs-6">
                                    <a class="card card-block m-b-0 bg-app-tertiary bg-inverse" href="frontend_home.html">
                                        <i class="ion-laptop fa-4x"></i>
                                        <p>Frontend</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- .card-block -->
                    </div>
                    <!-- End Apps card -->
                </div>
            </div>
            
        </div>
        <!-- End Apps Modal -->

        <div class="app-ui-mask-modal"></div>

        
        <script>
            $(function()
            {
                App.initHelpers('slick');
            });
        </script>

    </body>

</html>