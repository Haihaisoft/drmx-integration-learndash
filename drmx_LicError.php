<?php 
  require_once dirname(__DIR__, 3) . '/wp-load.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Login and obtain license</title>
<link rel="stylesheet" type="text/css" href="public/css/login-style.css" />
</head>

<body>
  <div class="login-wrap">
    <div class="login-head">
      <div align="center"><img src="public/images/logo.png" alt=""></div>
    </div>
    <div class="login-cont">
      <div id="btl-login-error" class="btl-error">
        <div class="black">
          <?php
            $LicError = isset( $_GET['error'] ) ? $_GET['error'] : '';
            $MesError = isset( $_GET['message'] ) ? $_GET['message'] : '';
          
            if($LicError == "ERROR:EXCEED_BIND"){
              echo esc_attr("ERROR:EXCEED_BIND, if in doubt please contact the administrator.");
            }else if ($LicError == "ERROR:ADMIN_NO_MONEY") {
              echo esc_attr("ERROR:ADMIN_NO_MONEY, please contact the administrator.");
            }else if ($MesError == "ERROR:RIGHTS EXPIRED") {
              echo esc_attr("ERROR:RIGHTS EXPIRED, please contact the administrator..");
            }else {
              echo esc_attr($LicError);
              echo "<br>";
              echo esc_attr($MesError);
            }
          ?>
        </div>
      </div>
      <div class="login-foot">
	<div class="foot-tit">Other options</div>
	<div class="foot-acts">
	    <a class="link-reg" href="<?php echo esc_attr(site_url()); ?>" target="_blank">Help?</a>
	    <a class="link-get-pwd" href="<?php echo esc_attr(site_url()); ?>" target="_blank">Buy Course</a>
	</div>
      </div>
    </div>
  </div>
</body>
</html>
