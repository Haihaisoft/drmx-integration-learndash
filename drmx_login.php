<?php
require_once dirname(__DIR__, 3) . '/wp-load.php';
error_reporting(E_ALL & ~E_DEPRECATED); 

$userid = get_current_user_id();

$user 		= get_user_by( 'id', $userid );
$username 	= $user->user_login;
$userEmail 	= $user->user_email;

if (!session_id()) {
    session_start();
}

$cache_key = isset($_GET['cache_key']) ? sanitize_text_field($_GET['cache_key']) : 'drmx_temp_params_' . session_id();
wp_cache_delete($cache_key, 'options');
$drmx_params = get_option($cache_key);

$flag	= 0;
// Get the yourproductid set in the license Profile.
$PIDs	= explode('-', $drmx_params["yourproductid"]);

// Get integration parameters
define( 'DRMX_ACCOUNT', 		get_option('drmx_account'));
define( 'DRMX_AUTHENTICATION', 		get_option('drmx_authentication'));
define( 'DRMX_GROUPID', 		get_option('drmx_groupid'));
define( 'DRMX_RIGHTSID', 		get_option('drmx_rightsid'));
define( 'WSDL', 			get_option('drmx_wsdl'));
define( 'DRMX_BINDING', 		get_option('drmx_binding'));

$client = new SoapClient(WSDL, array('trace' => false));


/********* Business logic verification ***********/

foreach ($PIDs as $Pid) {
	if (!isset($Pid) || empty($Pid)) {
		$info = "Invalid Course ID";
	} else {
		// Get course ID
		$course_id = $Pid;
	
		$isEnrolled = is_user_enrolled_in_course_alt($userid, $course_id);

		// Get course access expiration settings
		$course_details = get_course_access_details($course_id);
		// if Course Access Expiration is false, set expire_days to -1
		if ($course_details['expire_enabled']) {
			$rights_expire_days = $course_details['expire_days'];
		} else {
			$rights_expire_days = "-1";
		}
		// Get start and end date
		$rights_start_date = date('Y/m/d', $course_details['start_date']);
		$rights_end_date = date('Y/m/d', $course_details['end_date']);
		// Set the Rights duration
		$rights_duration = [
			'BeginDate' => $rights_start_date,
			'ExpirationDate' => $rights_end_date,
			'ExpirationAfterFirstUse' => $rights_expire_days,
		];

		if($isEnrolled){
			// If the username is not exists, call 'AddNewUser' to add user.
			if(checkUserExists($client, $username) == "False"){
				$addNewUserResult = addNewUser($client, $username, $userEmail);
				$info = $addNewUserResult;
			}
			// check user is revoked
			/*if(checkUserIsRevoked($client, $username)){
				$errorInfo = 'Username: '.$username.' is revoked.';
				header("location:drmx_LicError.php?error=".$errorInfo."&message=".$message);
				exit;
			}*/
	
			/*** Automatically update license permissions for users based on duration of the course ****/
			$updateRightResult = updateRight($client, $rights_duration, $userEmail);
	
			if($updateRightResult == '1'){
				
				/*****After the License Rights is updated, perform the method of obtaining the license****/
				$licenseResult = getLicense($client, $username);
				$license = $licenseResult->getLicenseRemoteToTableWithVersionWithMacResult;
				$message = $licenseResult->Message;
	
				if(stripos($license, '<div id="License_table_DRM-x4" style="display:none;">' )  === false ){
					header('location: drmx_LicError.php?error='.$license.'&message='.$message);
					exit;
				}
				/*****After obtaining the license, store the license and message through the session, and then direct to the licstore page******/
				$license_params = [
					'license'    	=> isset($license) ? $license : '',
					'message'   	=> isset($message) ? $message : '',
					'return_url'   	=> $drmx_params["return_url"],
				];
				$license_cache_key = 'license_temp_params_' . session_id();
				wp_cache_delete($license_cache_key, 'options');
				update_option($license_cache_key, $license_params, false);
				
				$flag = 1;
				header('location: licstore.php');
				$info = "Getting license...";
				exit;
			}
		}
	}
}

if($flag == 0){
	$info = "You have not enrolled for this course.";
}

/***** End of business logic verification ******/
// Check if the user is enrolled in the course
function is_user_enrolled_in_course_alt($userid, $course_id) {
    $enrolled_courses = learndash_user_get_enrolled_courses($userid);
    return in_array($course_id, $enrolled_courses);
}

// Get course access expiration settings
function get_course_access_details($course_id) {
    $raw = get_post_meta($course_id, '_sfwd-courses', true);
	$data = maybe_unserialize($raw);

	$start_timestamp = isset($data['sfwd-courses_course_start_date']) ? intval($data['sfwd-courses_course_start_date']) : null;
	$end_timestamp = isset($data['sfwd-courses_course_end_date']) ? intval($data['sfwd-courses_course_end_date']) : null;
	$expire_enabled = isset($data['sfwd-courses_expire_access']) && $data['sfwd-courses_expire_access'] === 'on';
	$expire_days = isset($data['sfwd-courses_expire_access_days']) ? intval($data['sfwd-courses_expire_access_days']) : 0;

    return [
        'expire_enabled' => $expire_enabled ? true : false,
        'expire_days' => intval($expire_days),
        'start_date' => $start_timestamp,
        'end_date' => $end_timestamp,
    ];
}

/********DRM-X 4.0 functions********/
function getIP(){
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}

function checkUserExists($client, $username) {
    $CheckUser_param = array(
        'UserName' 				=> $username,
        'AdminEmail' 			=> DRMX_ACCOUNT,
        'WebServiceAuthStr' 	=> DRMX_AUTHENTICATION,
    );

    $CheckUser = $client->__soapCall('CheckUserExists', array('parameters' => $CheckUser_param));
    return $CheckUser->CheckUserExistsResult;
}

function addNewUser($client, $username, $userEmail){
	$add_user_param = array(
		'AdminEmail' 		=> DRMX_ACCOUNT,
		'WebServiceAuthStr' 	=> DRMX_AUTHENTICATION,
		'GroupID' 		=> DRMX_GROUPID,
		'UserLoginName' 	=> $username,
		'UserPassword' 		=> 'N/A',
		'UserEmail' 		=> $userEmail,
		'UserFullName' 		=> 'N/A',
		'Title' 		=> 'N/A',
		'Company' 		=> 'N/A',
		'Address' 		=> 'N/A',
		'City' 			=> 'N/A',
		'Province' 		=> 'N/A',
		'ZipCode' 		=> 'N/A',
		'Phone' 		=> 'N/A',
		'CompanyURL' 		=> 'N/A',
		'SecurityQuestion' 	=> 'N/A',
		'SecurityAnswer' 	=> 'N/A',
		'IP' 			=> getIP(),
		'Money' 		=> '0',
		'BindNumber' 		=> DRMX_BINDING,
		'IsApproved' 		=> 'yes',
		'IsLockedOut' 		=> 'no',
	);

	$add_user = $client->__soapCall('AddNewUser', array('parameters' => $add_user_param));
	return $add_user->AddNewUserResult;
}

function updateRight($client, $lp_duration, $userEmail){

	$updateRight_param = array(
		'AdminEmail'			=> DRMX_ACCOUNT,
		'WebServiceAuthStr'		=> DRMX_AUTHENTICATION,
		'RightsID'			=> DRMX_RIGHTSID,
		'Description' 			=> "Courses Rights (Please don't delete)",
		'PlayCount' 			=> "-1",
		'BeginDate' 			=> $lp_duration['BeginDate'],
		'ExpirationDate' 		=> $lp_duration['ExpirationDate'],
		'ExpirationAfterFirstUse'	=> $lp_duration['ExpirationAfterFirstUse'],
		'RightsPrice' 			=> "0",
		'AllowPrint' 			=> "False",
		'AllowClipBoard' 		=> "False",
		'AllowDoc'			=> "False",
		'EnableWatermark' 		=> "True",
		'WatermarkText' 		=> $userEmail." ++username",
		'WatermarkArea' 		=> "1,2,3,4,5,",
		'RandomChangeArea' 		=> "True",
		'RandomFrquency' 		=> "12",
		'EnableBlacklist' 		=> "True",
		'EnableWhitelist' 		=> "True",
		'ExpireTimeUnit' 		=> "Day",
		'PreviewTime' 			=> 3,
		'PreviewTimeUnit' 		=> "Day",
		'PreviewPage' 			=> 3,
		'DisableVirtualMachine'		=> 'True',
	);
	
	$update_Right = $client->__soapCall('UpdateRightWithDisableVirtualMachine', array('parameters' => $updateRight_param));
	return $update_Right->UpdateRightWithDisableVirtualMachineResult;
}

function checkUserIsRevoked($client, $username){
	$CheckUserIsRevoked = array(
		'AdminEmail'         => DRMX_ACCOUNT,
		'WebServiceAuthStr'  => DRMX_AUTHENTICATION,
		'UserLoginName'      => $username,
	);
	
	$CheckUserIsRevokedResult = $client->__soapCall('CheckUserIsRevoked', array('parameters' => $CheckUserIsRevoked));
	return $CheckUserIsRevokedResult->CheckUserIsRevokedResult;
}


function getLicense($client, $username){
	global $drmx_params;
	
	$param = array(
		'AdminEmail'         => DRMX_ACCOUNT,
		'WebServiceAuthStr'  => DRMX_AUTHENTICATION,
		'ProfileID'          => $drmx_params["profileid"],
		'ClientInfo'         => $drmx_params["clientinfo"],
		'RightsID'           => DRMX_RIGHTSID,
		'UserLoginName'      => $username,
		'UserFullName'       => 'N/A',
		'GroupID'            => DRMX_GROUPID,
		'Message'            => 'N/A',
		'IP'                 => getIP(),
		'Platform'           => $drmx_params["platform"],
		'ContentType'        => $drmx_params["contenttype"],
		'Version'            => $drmx_params["version"],
		'Mac'                => $drmx_params["mac"],
	);
	
	/*****Obtain license by calling getLicenseRemoteToTableWithVersion******/
	$result = $client->__soapCall('getLicenseRemoteToTableWithVersionWithMac', array('parameters' => $param));
	return $result;
}

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
						echo esc_attr($info);
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
