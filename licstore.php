<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<!--The title tag of licstore.php must be set to Return_URL, otherwise the video will not be able to return to the original playback page after obtain the license-->
<title><?php echo $_SESSION["Return_URL"]; ?></title>
<link rel="stylesheet" type="text/css" href="public/css/login-style.css" />
</head>

<body>
<?php
  echo $_SESSION["license"];
?>
  <div class="login-wrap" id="content">
    <div class="login-head">
      <div align="center"><img src="public/images/logo.png" alt=""></div>
    </div>
    <form name="postForm" action="login.php" method="post">
      <div class="login-cont">
        <div class="black">
          <?php 
            echo $_SESSION["message"];
          ?>
        </div>
        <div class="login-foot">
          <div class="foot-acts">
            <!--<a class="link-reg" id="openFile" href="<?php echo $_SESSION["Return_URL"]; ?>" onclick="window.location.href = '<?php echo $_REQUEST["Return_URL"] ?>';" target="_blank">Open File</a>-->
            <input id="openFile" name="openFile" class="btn-login" type="button" value="Open Course" onclick="window.location.href = '<?php echo $_SESSION['Return_URL'] ?>';" />
          </div>
        </div>
      </div>
    </form>
  </div>
  <script>
    document.onreadystatechange = subSomething; 
      function subSomething() {
          if (document.readyState == "complete") {
              var varURL = '<?php echo $_SESSION['Return_URL']; ?>';
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