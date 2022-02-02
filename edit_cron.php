<?php
	require_once __DIR__ . '/inc/bootstrap.php';
	if(!User::isAdmin()) {
		die('403 - Access denied');
	}
	require_once __DIR__ . '/inc/header.php';
?>	

<?php
if (isset($_REQUEST['save']) and $_REQUEST['save'] == 'Y'):
    Cron::updateTask(
        $_REQUEST['id'],
        [
            'active' => $_REQUEST['active'] ? 'Y' : 'N',
            'name' => $_REQUEST['name'],
            'period' => $_REQUEST['period'],
            'command' => $_REQUEST['command'],
            'description' => $_REQUEST['description']
        ]);
?>
<script>
	alert('Задание успешно обновлено');
</script>
<?php	
	endif;
	
	$task = Cron::getTask($_REQUEST['id']);
?>
<form action="edit_cron.php" method="POST">
<input type="hidden" name="save" value="Y">
<input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>">
<div class="row">
							<div class="col-md-12">
								<div class="card">
									<div class="card-header">
										<h4>Редактирование периодического задания #<?php echo $_REQUEST['id'];?></h4>
									</div>
									<div class="card-block">
										<div class="form-check">
											
											<label class="css-input switch switch-primary">
												<input type="checkbox" name="active" <?php if($task['active']=='Y') { echo 'checked'; } ?>><span></span> Активность
											</label>
											
											
										</div>
										
										<div class="form-group">
											<label>Название задания</label>
											<input type="text" class="form-control" placeholder="Введите название задания" name="name" value="<?php echo $task['name'];?>">
										</div>
										
										<div class="form-group">
											<label>Периодичность выполнения</label>
											<input type="number" min="1" step="1" name="period" class="form-control" value="<?php echo $task['period'];?>">
											<small class="form-text text-muted">Введите значение периодичности выполнения в секундах. Желательно указывать значение, кратное 60</small>
										</div>
										
										<div class="form-group">
											<div class="form-group">
												<label>Команда</label>
												<textarea class="form-control code" name="command" rows="5"><?php echo htmlspecialchars(base64_decode($task['command']));?></textarea>
											</div>
										</div>
										
										
										<div class="form-group">
											<label for="comment">Описание задания</label>
											<textarea class="form-control" name="description" rows="5"><?php echo htmlspecialchars($task['description']);?></textarea>
										</div>
										<div class="card-action">
											<button type="submit" class="btn btn-success">Сохранить</button>
											<a href="index.php?act=cron" class="btn btn-danger">Отмена</a>
										</div>
									</div>
									
								</div>
							</div>
</div>
</form>



<?php
	require_once __DIR__ . '/inc/footer.php';
?>