<?php
session_start();

require '../../../wp-load.php';

$_SESSION['ProfileID'] = $_REQUEST["profileid"];
$_SESSION['ClientInfo'] = $_REQUEST["clientinfo"];
$_SESSION['Platform'] = $_REQUEST["platform"];
$_SESSION['ContentType'] = $_REQUEST["contenttype"];
$_SESSION['ProductID'] = $_REQUEST["yourproductid"];
$_SESSION['RightsID'] = $_REQUEST["rightsid"];
$_SESSION['Version'] = $_REQUEST["version"];
$_SESSION['Return_URL'] = $_REQUEST["return_url"];

if (is_user_logged_in()){
	echo "<SCRIPT language=JavaScript>location='drmx_login.php';</SCRIPT>";
}

/*When the user opens your protected content, DRM-X will provide the above value.*/
?>

<!DOCTYPE>
<HTML>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <title>Login and obtain license</title>
   <link rel="stylesheet" type="text/css" href="public/css/login-style.css" />
</head>

<body>
	<div class="login-wrap">
      <div class="login-head">
         <div align="center"><img src="public/images/logo.png" alt=""></div>
      </div>
      <form name="postForm" action="drmx_login.php" method="post">
         <div class="login-cont">
            <!-- <div id="btl-login-error" class="btl-error" style="display:none;"></div> -->
            <ul>
               <li class="login-item">
                  <div class="login-ipt">
                     <span class="icon-user-name"><b></b></span>
                     <input type="text" name="username" id="btl-input-username" placeholder="Username">
                  </div>
               </li>
               <li class="login-item">
                  <div class="login-ipt">
                     <span class="icon-password"><b></b></span>
                     <input type="password" name="password" id="btl-input-password" placeholder="Password">
                  </div>
               </li>
               <li class="login-item">
                  <input class="btn-login" type="submit" value="Login">
               </li>
            </ul>
            <div class="login-foot">
               <div class="foot-acts">
                  Please login to your account and get a license. If you do not buy a course, please visit our website. <a href="<?php echo site_url() ?>" target="_blank"><?php echo site_url() ?></a>
               </div>
            </div>
         </div>
	   </form>
   </div>
</body>
</html>