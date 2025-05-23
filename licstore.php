<?php

require_once dirname(__DIR__, 3) . '/wp-load.php';

if (!session_id()) {
  session_start();
}

$license_cache_key = isset($_GET['license_cache_key']) ? sanitize_text_field($_GET['license_cache_key']) : 'license_temp_params_' . session_id();
wp_cache_delete($license_cache_key, 'options');
$license_params = get_option($license_cache_key);

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<!--The title tag of licstore.php must be set to Return_URL, otherwise the video will not be able to return to the original playback page after obtain the license-->
<title><?php echo $license_params["return_url"]; ?></title>
<link rel="stylesheet" type="text/css" href="public/css/login-style.css" />
</head>

<body>
<?php
  echo $license_params["license"];
?>
  <div class="login-wrap" id="content">
    <div class="login-head">
      <div align="center"><img src="public/images/logo.png" alt=""></div>
    </div>
    <form name="postForm" action="login.php" method="post">
      <div class="login-cont">
        <div class="black">
          <?php 
            echo $license_params["message"];
          ?>
        </div>
        <div class="login-foot">
          <div class="foot-acts">
            <input id="openFile" name="openFile" class="btn-login" type="button" value="Open Course" onclick="window.location.href = '<?php echo $license_params['return_url'] ?>';" />
          </div>
        </div>
      </div>
    </form>
  </div>
  <script>
    document.onreadystatechange = subSomething; 
      function subSomething() {
          if (document.readyState == "complete") {
              var varURL = '<?php echo $license_params['return_url']; ?>';
              if(varURL != 'ios_x'){
                  document.getElementById("openFile").click();
              }else{
                document.getElementById("content").style.display="none";
              }
          }
      }
  </script>
</body>
</html>
