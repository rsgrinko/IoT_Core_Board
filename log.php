<?php
require_once __DIR__ . '/inc/bootstrap.php';
if (!User::isAdmin()) {
    die('403 - Access denied');
}
require_once __DIR__ . '/inc/header.php';

?>

    <div class="row">
        <div class="col-lg-12">
            <!-- Header BG Table -->
            <div class="card">
                <div class="card-header">
                    <h4>Журнал событий</h4>
                    <div class="card-actions">
                        <code>CEvents</code>
                    </div>
                </div>
                <div class="card-block">
                    <div class="table-responsive">
                        <table class="table table-striped table-borderless table-header-bg">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 74px;">ID <i class="fa fa-arrow-down"></i></th>
                                <th style="width: 220px;">Дата</th>
                                <th>Событие</th>
                                <th style="width: 130px;">Тип события</th>
                                <th>Модуль</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            Pagination::execute($_REQUEST['page'], Log::getCount(), PAGINATION_LIMIT);
                            $limit = Pagination::getLimit();
                            ?>
                            <?php foreach (Log::getEvents('', $limit, 'DESC') as $event): ?>
                                <tr>
                                    <td class="text-center"><?php echo $event['id']; ?></td>
                                    <td><?php echo date("d.m.Y H:i:s", $event['time']); ?></td>
                                    <td><?php echo $event['message']; ?></td>
                                    <td>
                                        <span class="<?php echo getClassNameByEventType($event['type']); ?>"><?php echo $event['type']; ?></span>
                                    </td>
                                    <td><?php echo $event['module']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php Pagination::show('page'); ?>
                </div>
                <!-- .card-block -->
            </div>
            <!-- .card -->
            <!-- End Header BG Table -->
        </div>
    </div>

<?php
require_once __DIR__ . '/inc/footer.php';
?>