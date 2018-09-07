<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>dva.pet</title>
		<link href="templates/css/bootstrap.min.css" rel="stylesheet">
		<link href="templates/css/style.css" rel="stylesheet">

		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script type='text/javascript'>
			function refreshCaptcha() {
				var img = document.images['captchaimg'];
				img.src = img.src.substring(0, img.src.lastIndexOf("?")) + "?rand=" + Math.random() * 1000;
			}
		</script>
		<style>
			.alert{margin-top: 15px;}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4" style="text-align: center;">
					<img style="max-width: 100%;" src="templates/images/logo2.png" alt="dva.pet zombie.studio">
					<?=$this -> por ?>
					<form id="form1" name="form1" method="post" action="./">
						<div>
							<div class="input-group input-group-xs" style="margin:5px 0px;">
								<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
								<input type="text" class="form-control" placeholder="<?=t("Username") ?>" name="username">
							</div>
							<div class="input-group input-group-xs" style="margin:5px 0px;">
								<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
								<input type="password" class="form-control" placeholder="<?=t("Password") ?>" name="password">
							</div>
							<div class="input-group input-group-lg" style="margin:5px 0px;">
								<input type="text" class="form-control" placeholder="<?=t("Captcha") ?>" name="captcha_code">
								<span class="input-group-addon" style="padding: 0px;"><img src="captcha/captcha.php?rand=<?php echo rand(); ?>" id='captchaimg'></span>
								<span class="input-group-addon" style="padding: 0px;"><a class="btn" href='javascript: refreshCaptcha();'><span class="glyphicon glyphicon-refresh"></span></a></span>
							</div>
						</div>
						<button style="width: 100%;" type="submit" class="btn btn-default btn-lg" name="Submit" id="Submit"><span class="glyphicon glyphicon-log-in"></span> <?=t("Login") ?></button>
					</form>
				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
	</body>
</html>
