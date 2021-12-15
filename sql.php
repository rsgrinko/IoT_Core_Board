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
            <h4>SQL запросы</h4>
            <div class="card-actions">
            	<code>SQL</code>
        	</div>
        </div>
        <div class="card-block">
        <form class="form-horizontal m-t-sm" action="" method="post">
	    <input type="hidden" name="execute" value="Y">
        <div class="form-group">
                <div class="col-xs-12">
                    <div class="form-material floating">
                        <textarea class="form-control" id="query" name="query" rows="8"><?php echo isset($_REQUEST['query']) ? $_REQUEST['query'] : ''; ?></textarea>
                        <label for="query">Введите SQL запрос</label>
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
    if(isGod($USER['id'])) {
        $res = $DB->query($_REQUEST['query']);

        if ($res) {

            echo '<div class="table-responsive"><table class="table table-bordered table-striped table-vcenter table-hover-custom"><tr class="sql-query-result-table-header">';

            foreach ($res[0] as $key => $value) {
                echo '<td style="font-weight: bold; text-align:center;">' . $key . '</td>';
            }
            echo '</tr>';


            foreach ($res as $item) {
                echo '<tr>';

                foreach ($item as $index => $value) {
                    echo '<td>' . $value . '</td>';
                }


                echo '</tr>';


            }


            echo '</table></div>';

        } else {
            echo '<div class="alert alert-danger"><p><strong>MySQL</strong> вернула пустой результат</p></div>';
        }


    } else {
        echo '<div class="alert alert-danger"><p>Вашей учетной записи не назначена роль "Администратор сервера" - вы не можете использовать данный функционал.</div>';
    }
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