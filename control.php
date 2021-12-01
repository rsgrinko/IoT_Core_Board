<?php
	require_once __DIR__ . '/inc/bootstrap.php';
	require_once __DIR__ . '/inc/header.php';
	
	/*$userDevices = getUserDevices($USER['id']);
	if($userDevices) {
		$deviceId = $userDevices[0]['id']; // мониторим только первое устройство пользователя при наличии
	} else {
		$deviceId = 9;						// иначе показываем демо плату
	}*/
	$arSensors = CIoT::getDallasArrData($USER['deviceId']);

?>	

<div class="row maxheight test-draggable-items control_block">
	
<?php for($i=1; $i<5; $i++): ?>
<div class="col-sm-6 col-lg-3">
	<div class="card" id="card_for_relay_<?php echo $i; ?>">
		<div class="card-header bg-teal bg-inverse sensor_card_header">
			<h4 style="position:relative;">
				<span class="onoff" id="state_relay_<?php echo $i; ?>"></span>
				<i class="fa fa-lightbulb-o	"></i>
				Реле #<?php echo $i; ?>
			</h4>
			
			<ul class="card-actions">
                <li>
                    <button type="button" onclick="App.cards('#card_for_relay_<?php echo $i; ?>', 'content_toggle' );"><i class="ion-ios-toggle"></i></button>
                </li>
            </ul>
            
		</div>
		<div class="card-block">
			<?/*<p>
				<div id="state_relay_<?php echo $i; ?>" class="control_relay_state relay_state_disabled">ВЫКЛЮЧЕНО</div>
			</p>*/?>
			<p>
				<button class="btn btn-app-cyan btn-block" type="button" onClick="changeRelayState(<?php echo $i; ?>, 1);">Включить</button>
				<button class="btn btn-app-light btn-block" type="button" onClick="changeRelayState(<?php echo $i; ?>, 0);">Выключить</button>
			</p>
		</div>
	</div>
</div>
<?php endfor; ?>

</div>
<!------>
<script>
	function changeRelayState(num, state){
		
		
		$.ajax({
				url: "ajax/changeRelayState.php",
				data: {deviceId: "<?php echo $USER['deviceId']; ?>", relay: num, state: state},
				success: function(data){/*
						//$('#state_relay_' + num).text(data.state);
						//$('#state_relay_' + num).removeClass("relay_state_disabled");
						//$('#state_relay_' + num).removeClass("relay_state_enabled");
						if(date.stateCode == "1") {
							//$('#state_relay_' + num).addClass("relay_state_enabled");
							$('#state_relay_' + num).addClass("relayon");
						} else {
							//$('#state_relay_' + num).addClass("relay_state_disabled");
							$('#state_relay_' + num).addClass("relayoff");
						}
					*/	
				},
				dataType: "json"
				});
		
		}
		
		
		$(document).ready(function(){
			setInterval(function(){
				$.ajax({
					url: "ajax/getRelayState.php",
					data: {deviceId: "<?php echo $USER['deviceId']; ?>"},
					success: function(data){
							if(data.status == "ok"){
/*								
								$('#state_relay_1').text(data.state.relay1);
								$('#state_relay_2').text(data.state.relay2);
								$('#state_relay_3').text(data.state.relay3);
								$('#state_relay_4').text(data.state.relay4);
								

								$('#state_relay_1').removeClass("relay_state_disabled").removeClass("relay_state_enabled");
								$('#state_relay_2').removeClass("relay_state_disabled").removeClass("relay_state_enabled");
								$('#state_relay_3').removeClass("relay_state_disabled").removeClass("relay_state_enabled");
								$('#state_relay_4').removeClass("relay_state_disabled").removeClass("relay_state_enabled");
*/
								$('#state_relay_1').removeClass("relayoff").removeClass("relayon");
								$('#state_relay_2').removeClass("relayoff").removeClass("relayon");
								$('#state_relay_3').removeClass("relayoff").removeClass("relayon");
								$('#state_relay_4').removeClass("relayoff").removeClass("relayon");
								
								$('#state_relay_1').addClass(data.classname.relay1);
								$('#state_relay_2').addClass(data.classname.relay2);
								$('#state_relay_3').addClass(data.classname.relay3);
								$('#state_relay_4').addClass(data.classname.relay4);
							} else {
								console.log('Ошибка обновления данных: ' + data.message);
							}
							
					},
					dataType: "json"
					});
			}, 1000);
			
			$( ".test-draggable-items.row" ).sortable();
			
			
		});
		
		

</script>


	
<?php	
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/footer.php';
?>