<?php
/*
	Страница авторизации
	Данный файл входит в состав системы IoT Core System
	Разработчик: Роман Сергеевич Гринько
	E-mail: rsgrinko@gmail.com
	Сайт: https://it-stories.ru
*/
require_once __DIR__ . '/inc/bootstrap.php';

if(CUser::is_user()) {
	          $auth = true;
	         } else {
	          $auth = false;	
	          if($_REQUEST['login']!=='' and $_REQUEST['pass']!=='' and !empty($_REQUEST['login'] and !empty($_REQUEST['pass']))){
		         if(CUser::SecurityAuthorize($_REQUEST['login'], $_REQUEST['pass'])) {
			         $auth = true;
			         } else {
				         $auth = false;
				         CEvents::add('Неудачная попытка авториации в системе (IP: '.getIp().', '.$_REQUEST['login'].', OS: '.getOS().')', 'warning', 'panel');
						 adminSendMail('Попытка вторжения', 'Неудачная попытка авториации в системе (IP: '.getIp().', '.$_REQUEST['login'].', OS: '.getOS().')', 'warning', 'panel');
				         }
	            } else {
		            $auth = false;
		            }

	}
if($auth == false and isset($_REQUEST['login']) and $_REQUEST['login']!=='') {
	$err_mess = true;
} else {
	$err_mess = false;
}

if($auth==true) {
	if(isset($_REQUEST['login']) and $_REQUEST['login'] !== ''){
		CEvents::add('Пользователь '.$_REQUEST['login'].' авторизировался в системе (IP: '.getClientInfo()['ip'].', OS: '.getOS().', UA: '.getClientInfo()['name'].')', 'info', 'panel');
	}
	header('Location: index.php?');
} else {
?>
<html>
	<head>
		<title>IoT Core System - Авторизация</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style>
			@import url(https://fonts.googleapis.com/css?family=Roboto:300);
			.login-page {
			  width: 360px;
			  padding: 8% 0 0;
			  margin: auto;
			}
			.form {
			  position: relative;
			  z-index: 1;
			  background: #FFFFFF;
			  max-width: 360px;
			  margin: 0 auto 100px;
			  padding: 45px;
			  text-align: center;
			  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
			}
			.form input {
			  font-family: "Roboto", sans-serif;
			  outline: 0;
			  background: #f2f2f2;
			  width: 100%;
			  border: 0;
			  margin: 0 0 15px;
			  padding: 15px;
			  box-sizing: border-box;
			  font-size: 14px;
			}
			.form button {
			  font-family: "Roboto", sans-serif;
			  text-transform: uppercase;
			  outline: 0;
			  background: #4CAF50;
			  width: 100%;
			  border: 0;
			  padding: 15px;
			  color: #FFFFFF;
			  font-size: 14px;
			  -webkit-transition: all 0.3 ease;
			  transition: all 0.3 ease;
			  cursor: pointer;
			}
			.form button:hover,.form button:active,.form button:focus {
			  background: #43A047;
			}
			.form .message {
			  margin: 15px 0 0;
			  color: #b3b3b3;
			  font-size: 12px;
			}
			.form .message a {
			  color: #4CAF50;
			  text-decoration: none;
			}
			.form .register-form {
			  display: none;
			}
			.container {
			  position: relative;
			  z-index: 1;
			  max-width: 300px;
			  margin: 0 auto;
			}
			.container:before, .container:after {
			  content: "";
			  display: block;
			  clear: both;
			}
			.container .info {
			  margin: 50px auto;
			  text-align: center;
			}
			.container .info h1 {
			  margin: 0 0 15px;
			  padding: 0;
			  font-size: 36px;
			  font-weight: 300;
			  color: #1a1a1a;
			}
			.container .info span {
			  color: #4d4d4d;
			  font-size: 12px;
			}
			.container .info span a {
			  color: #000000;
			  text-decoration: none;
			}
			.container .info span .fa {
			  color: #EF3B3A;
			}
			.logo {
				width:100%;
			}
			.error_text {
			    color: red;
			    font-weight: bold;
			    margin-bottom: 20px;
			    padding: 10px;
			    display: block;
			    font-size: 14px;
			    background: #ffdcdc;
			    border: 1px solid red;
			    text-align: center;
			}

			body {
				height: 100%;
			  background: #76b852; /* fallback for old browsers */
			  background: -webkit-linear-gradient(right, #76b852, #8DC26F);
			  background: -moz-linear-gradient(right, #76b852, #8DC26F);
			  background: -o-linear-gradient(right, #76b852, #8DC26F);
			  background: linear-gradient(to left, #76b852, #8DC26F);
			  font-family: "Roboto", sans-serif;
			  -webkit-font-smoothing: antialiased;
			  -moz-osx-font-smoothing: grayscale;    
			  
			  
background-color: #d8dbe2;
	background-image: 
		repeating-radial-gradient(circle at 0 100%, rgba(27,27,30, 0.1), rgba(88,164,176, 0.15) 1px, rgba(216,219,226, 0.2) 2px, rgba(88,164,176, 0.15) 3px, rgba(27,27,30, 0.1) 4px), 
		radial-gradient(circle at 0 100%, #1b1b1e, #373f51, #58a4b0, #a9bcd0, #d8dbe2);

			  
			  
			}
		</style>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	</head>
	<body>
		<div class="login-page">
		  <div class="form">
			<img class="logo" src="https://new.it-stories.ru/assets/img/logo/logo-login.png">
			<?php if($err_mess): ?>
				<span class="error_text">Ошибка авторизации</span>
			<?php endif;?>
		    <form class="register-form">
		      <input type="text" placeholder="name"/>
		      <input type="password" placeholder="password"/>
		      <input type="text" placeholder="email address"/>
		      <button>Создать</button>
		      <p class="message">Уже зарегистрированы? <a href="#">Войти</a></p>
		    </form>
		    <form class="login-form">
		      <input type="text" name="login" placeholder="Имя пользователя"/>
		      <input type="password" name="pass" placeholder="Пароль"/>
		      <button>Войти</button>
		      <?/*<p class="message">Новый пользователь? <a href="#">Создать аккаунт</a></p>*/?>
		    </form>
		  </div>
		</div>
		<script>
			$('.message a').click(function(){
			   $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
			});
		</script>
	</body>
</html>

<?php
} ?>