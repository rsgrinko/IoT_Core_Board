<?php
require_once __DIR__ . '/inc/bootstrap.php';
if (!CUSer::is_admin()) {
    die('403 - Access denied');
}
require_once __DIR__ . '/inc/header.php';
?>
<?php
if (
    isset($_REQUEST['run_task']) and $_REQUEST['run_task'] == 'Y' and
    isset($_REQUEST['task_id']) and !empty($_REQUEST['task_id'])
):
    CCron::runTask($_REQUEST['task_id']);
    ?>
    <script>
        $(document).ready(function () {
            alert("Задание выполнено");
        });
    </script>
<?php endif; ?>

<?php
if (
    isset($_REQUEST['remove_task']) and $_REQUEST['remove_task'] == 'Y' and
    isset($_REQUEST['task_id']) and !empty($_REQUEST['task_id'])
):
    CCron::removeTask($_REQUEST['task_id']);
    ?>
    <script>
        $(document).ready(function () {
            alert("Задание удалено");
        });
    </script>
<?php endif; ?>

<div class="row maxheight">
    <div class="col-lg-12">
        <!-- Header BG Table -->
        <div class="card">
            <div class="card-header">
                <h4>Планировщик</h4>
                <div class="card-actions">

                    <code>CCron <a href="add_cron.php" class="btn btn-success">Добавить</a></code>
                </div>
            </div>

            <div class="card-block">
                <?php
                CPagination::execute($_REQUEST['page'], CCron::count_tasks(), PAGINATION_LIMIT);
                $limit = CPagination::getLimit();
                ?>
                <div class="table-responsive">
                    <table class="table table-striped table-borderless table-header-bg">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">ID</th>
                            <th>Название</th>
                            <th>Описание</th>
                            <th>Интервал</th>
                            <th style="width: 116px;">Был запуск</th>
                            <th>Активность</th>
                            <th>Команда</th>
                            <th class="text-center" style="width: 110px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach (CCron::getTasks($limit, 'ASC') as $task): ?>
                            <tr data-name="<?php echo htmlspecialchars($task['name']); ?>"
                                data-id="<?php echo $task['id']; ?>">
                                <td class="mob_hide"><?php echo $task['id']; ?></td>
                                <td><?php echo $task['name']; ?></td>
                                <td class="mob_hide"><?php echo $task['description']; ?></td>
                                <td class="mob_hide"><?php echo $task['period']; ?></td>
                                <td class="mob_hide"><?php echo date("d.m.y H:i:s", $task['last_run']); ?></td>
                                <td class="mob_hide"><?php echo $task['active'] == 'Y' ? '<button class="btn btn-app btn-block">Да</button>' : '<button class="btn btn-app-red btn-block">Нет</button>'; ?></td>
                                <td class="mob_hide">
                                    <code><?php echo htmlspecialchars(base64_decode($task['command'])); ?></code></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button onClick="javascript:window.location.href='cron.php?run_task=Y&task_id=<?php echo $task['id']; ?>'"
                                                class="btn btn-xs btn-default" type="button" data-toggle="tooltip"
                                                title="Запустить задание"><i class="fa fa-power-off"></i></button>
                                        <button onClick="javascript:window.location.href='edit_cron.php?id=<?php echo $task['id']; ?>'"
                                                class="btn btn-xs btn-default" type="button" data-toggle="tooltip"
                                                title="Редактировать задание"><i class="ion-edit"></i></button>
                                        <button onClick="javascript:window.location.href='cron.php?remove_task=Y&task_id=<?php echo $task['id']; ?>'"
                                                class="btn btn-xs btn-default" type="button" data-toggle="tooltip"
                                                title="Удалить задание"><i class="ion-close"></i></button>
                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php CPagination::show('page'); ?>
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