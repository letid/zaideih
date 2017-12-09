<?php
// exit(PHP_SAPI);
// error_reporting(E_ALL);
// error_reporting(-1);
// ini_set('error_reporting', E_ALL);
// error_reporting(E_ALL); ini_set('display_errors', '1');
// error_reporting(E_ERROR | E_WARNING | E_PARSE); ini_set('display_errors', '1');
session_start();
require_once('init.php');
require_once('function.php');
require_once('zotune.php');
require_once($init['dir']['class'].'class.html.php');
$REQUEST_URI 					= rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
zotune::$z['URI'] 		= explode("/", $REQUEST_URI);
$HOST									= $_SERVER['HTTP_HOST'];
$init['app.name'] 		= $app[1][1];
$init['app.version'] 	= $app[1][2];
$init['app.site'] 		= $app[1][4];
$init['www'] 					= "//$HOST/";
$init['path'] 				= $init['www'];
/*
	CHECK ZOTUNE PROJECT CONFIGURATION
*/
if (isset($_SESSION[$HOST])):
	$app[2][2] = $_SESSION[$HOST];
elseif(!$app[2][2]):
	$URI=zotune::$z['URI'];
	$h = explode('.', $HOST);
	$domain_extension= end($h);
	if(count($h) > 3):
		$app[2][2] 				= isset($URI[1])?$URI[1]:$app[1][5];
		if($app[2][2]!=0) $_SESSION[$HOST]=$app[2][2];
			else $ProUnique = 1;
	elseif(count($h) > 2 and $h[0] !='www'):
		$domain_name=prev($h);
		$_SESSION[$HOST]		=$app[2][2]=prev($h);
	elseif(count($h) > 1):
		$_SESSION[$HOST]		=$app[2][2]=prev($h);
	else:
		$app[2][2] 				= isset($URI[1])?$URI[1]:$app[1][5];
		$ProUnique = 1;
	endif;
endif;
/*
	BASIC IDENTIFICATION FOR PROJECT CONFIGURATION AND NAME, PATH,
*/
$Initiate 								= implode("/",$app[2]);
$init['dir.project.main'] = $app[2][1];
$init['pro.name'] 				= $app[2][2];
$init['dir']['project'] 	= str_replace($app[2][3],'',$Initiate);

//$init['dir']['project']		= NULL;
/*
	CHECK IF PROJECT HAS CONFIGURATION FILE, IF FOUND THEN REQUIRE
*/
if(file_exists($Initiate)):
	require_once($Initiate);
	$init[0] 					= $init[0]?$init[0]:1;
	if(isset($ProUnique)):
		$init['www'] 			= $init['www'].$app[2][2].'/';
		$init[0]				= $init[0] + $ProUnique;
	endif;
else:
	$init_file = strtoupper($app[2][3]);
	list($init['index'])		= explode(".",$init_file);
	call_user_func_array(array(new zotune, 'zExecution_error'), array(tpl_error_pro_config, $init));
endif;
/*
	USER COOKIE NAME
*/
$user_cookie_name				= md5($app[1][3].$init['pro.version']);
$user_speed_name				= md5($app[1][3].$init['pro.product']);
$init['user.cookie.name'] 		= $user_cookie_name;
$init['user.speed.name'] 		= $user_speed_name;
//$init['user.speed.name'] 		= date('mdYhis');
/*
	USER LOGOUT!
*/
if(isset($_GET['logout'])):
	setcookie($user_speed_name, false);
	setcookie($user_cookie_name, false);
	session_destroy();
	header("Location: {$init['www']}");
endif;
/*
	DEVICE DETECTION
*/
if(is_array($init_device_check)):
	require_once($init['dir']['class'].'class.device.php');
	$device 					= new device($init_device_check,$init_device_index,$init_device_tpl);
	$device->check();
endif;
/*
	CHECK PROJECT HAS CONFIGURED FOR MYSQL, IF FOUND, THEN REQUIRE NECESSARIES FILE AND CONNECT
*/
if(is_array($database)):
	require_once($init['dir']['class'].$app[3][1]);
	sql::connection($database['mysql']['host'],$database['mysql']['username'],$database['mysql']['password'],$database['mysql']['database']);
	if(sql::$db->connect_errno):
		$init['pro.msg.no']		= sql::$db->connect_errno;
		$init['pro.msg'] 		= sql::$db->connect_error;
		call_user_func_array(array(new zotune, 'zExecution_error'), array(tpl_error_db, $init));
	endif;
	zotune::$db=$database['mysql_table'];
endif;
/*
	CHECK IF PROJECT IS UNDER-CONSTRUCTION
*/
if($init['msg.maintaining'])call_user_func_array(array(new zotune, 'zExecution_error'), array(tpl_maintaining_pro, $init));
/*
	CHECK AND SET VARIABLES FOR SMART ACCESS PROPOSE
$user_cookie_name = $init['user.cookie.name'];
$user_speed_name  = $init['user.speed.name'];
*/
zotune::$init = $init;
/*
	ZOTUNE IS BEGIN HERE
*/
$zotune = new zotune;
/*
	CHECK USER IS LOGGED AND COOKIE IS DECENTLY, CONFIRM BY GETTING IT'S DATA
*/
if(isset($_COOKIE[$user_cookie_name])):
	$u=unserialize($_COOKIE[$user_cookie_name]);
	if(isset($u[0]) and isset($u[1])):
		if ($table=$zotune->db_users and is_array($init_require_islogged)):
			if(!$zotune->isLogged($table,$init_require_islogged,$u[0],$u[1])):
				unset($_SESSION[$user_cookie_name]); setcookie($user_cookie_name, false);
			endif;
		endif;
	else:
		unset($_SESSION[$user_cookie_name]); unset($_SESSION[$user_speed_name]); setcookie($user_cookie_name, false);
	endif;
endif;
/*
	CHECK PROJECT PAGES, AND REMOVE ANY UNNECESSARY PAGES ACCORDING TO AUTHORIZATION, THEN ADD SYSTEM PAGES
*/
$zotune->zPageInitiate($pages);
/*
	GET ALL NECESSARY REGISTRATIONS
*/
$zotune->zPathInitiate();
$zotune->zReg('register',$init_require_essential);
/*
	BUILD PROJECT PAGES
	Initiate,initiation
*/
$zotune->zPage();
/*
	SCRIPT GENERATOR
*/
$zotune->RequireMetaInitiate($meta);
/*
	LET'S EXECUTE
*/
$zotune->zExecution();
/*
	CHECK MEMORY USAGE
*/
/*
echo get::size(memory_get_usage(true));
echo memory_get_peak_usage();
*/
