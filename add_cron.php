<?php
	require_once __DIR__ . '/inc/bootstrap.php';
	if(!CUSer::is_admin()) {
		die('403 - Access denied');
	}
	require_once __DIR__ . '/inc/header.php';
?>	
<?php
if(isset($_REQUEST['save']) and $_REQUEST['save'] == 'Y'):
		CCron::addTask(array(
							'active' =>$_REQUEST['active'] ? 'Y' : 'N',
							'name' => $_REQUEST['name'],
							'period' => $_REQUEST['period'],
							'command' => $_REQUEST['command'],
							'description' => $_REQUEST['description']
							)
						);
?>
<script>
jQuery(document).ready(function(){
	$.notify({
		icon: 'la la-bell',
		title: 'Уведомление',
		message: 'Задание успешно создано',
	},{
		type: 'success',
		placement: {
			from: "bottom",
			align: "right"
		},
		time: 1000,
	});
});
setTimeout(function(){
	//window.location.href = 'index.php?act=cron';
}, 1000);
</script>

<div class="row">
<div class="col-md-12">
	<div class="card">
		<div class="card-header">
			<h4>Добавление периодического задания</h4>
		</div>
		<div class="card-block">
			<div class="alert alert-success">
                <p>Задание успешно создано</p>
            </div>
		</div>
	</div>
</div>
</div>
<?php else: ?>

	
	
<form action="add_cron.php" method="POST">
<input type="hidden" name="save" value="Y">
<div class="row">
			<div class="col-md-12">
								<div class="card">
									<div class="card-header"><h4>Добавление периодического задания</h4></div>
									
									<div class="card-block">
										<div class="form-check">
											
											<label class="css-input switch switch-primary">
												<input type="checkbox" name="active" checked><span></span> Активность
											</label>
											
											
										</div>
										
										<div class="form-group">
											<label>Название задания</label>
											<input type="text" class="form-control" placeholder="Введите название задания" name="name">
										</div>
										
										<div class="form-group">
											<label>Периодичность выполнения</label>
											<input type="number" min="1" step="1" name="period" class="form-control" value="60">
											<small class="form-text text-muted">Введите значение периодичности выполнения в секундах. Желательно указывать значение, кратное 60</small>
										</div>
										
										<div class="form-group">
											<div class="form-group">
												<label>Команда</label>
												<textarea class="form-control code" name="command" rows="5" placeholder="Введите PHP код для выполнения"></textarea>
											</div>
										</div>
										
										
										<div class="form-group">
											<label for="comment">Описание задания</label>
											<textarea class="form-control" name="description" rows="5"></textarea>
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

<?php endif; ?>
<?php
	require_once __DIR__ . '/inc/footer.php';
?>	