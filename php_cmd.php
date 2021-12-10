<?php
	require_once __DIR__.'/inc/bootstrap.php';
	if(!CUSer::isAdmin()) {
		die('403 - Access denied');
	}
	require_once __DIR__.'/inc/header.php';
	
?>
<div class="row maxheight">
<div class="col-md-12">
<!-- Floating Labels -->
<div class="card">
		<div class="card-header">
            <h4>PHP командная строка</h4>
            <div class="card-actions">
            	<code>Eval</code>
        	</div>
        </div>
        <div class="card-block">
        <form class="form-horizontal m-t-sm" action="" method="post">
	    <input type="hidden" name="execute" value="Y">
        <div class="form-group">
                <div class="col-xs-12">
                    <div class="form-material floating">
                        <textarea class="form-control" id="query" name="query" rows="8"><?php echo isset($_REQUEST['query']) ? $_REQUEST['query'] : ''; ?></textarea>
                        <label for="query">Введите PHP код</label>
                    </div>
                </div>
            </div>
                            <div class="form-group m-b-0">
                <div class="col-sm-9">
                    <button class="btn btn-app" type="submit">Выполнить</button>
                </div>
            </div>
        </form>
    </div>
    <!-- .card-block -->
</div>
<!-- .card -->
<!-- End Floating Labels -->
</div>

<?php if(isset($_REQUEST['execute']) and $_REQUEST['execute'] == 'Y'):?>

<div class="col-md-12">
<!-- Floating Labels -->
<div class="card">
    <div class="card-block">
	<?php
			eval($_REQUEST['query']);
		
	?>
    </div>
    <!-- .card-block -->
</div>
<!-- .card -->
<!-- End Floating Labels -->
</div>
<?php endif; ?>

</div>

<?php	
	require_once __DIR__.'/inc/footer.php';
?>