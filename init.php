<?php
//error_reporting(E_ERROR);
//error_reporting(E_ALL & ~(E_WARNING|E_STRICT|E_NOTICE));
/*
	APP_NAME,APP_VERSION, PRO_NAME, PRO_VERSION -> E_ERROR, E_ALL
	v.v.yy.mmm.dd (upgrade,change,year,month,day)
*/
$app[1] = array(1=>'ZOTUNE.developer', 2=>'1.6.6', 3=>'2.3.150116', 4=>'//www.zotune.com', 5=>'zaideih');
$app[2] = array(1=>'project', 2=>'', 3=>'index.php');
$app[3] = array(1=>'class.sql.php');
/*
	TEMPLATE
*/
define('tpl_error_pro_config','msg.error.project.configuration');
define('tpl_maintaining_pro','msg.project.maintaining');
define('tpl_error_db','msg.error.db');
/*
	TEMPLATE PATTERN
define('tpl_pattern_sign','/[$](.*?)[;]/');
define('tpl_pattern_brace','/[{](.*?)[}]/');
*/
define('pro_method_is_private','method in class is private!');
define('pro_method_not_exists','method is not exists in class!');
define('pro_method_return_array','method in class return (num) array!');
/*
	MSG
*/
define('no_device_detection_running','No device detection is running!');

/*
	PROJECT DEFAULT SETTING
*/
$init[0] 							= 0;
$init[1] 							= array('home','privacy','register','login','profile','api');
$init[2] 							= '/[{](.*?)[}]/';
$init[3] 							= '.html';
/*
	DIRECTORY
*/
$init['dir']['common'] 				= 'common/';
$init['dir']['class'] 				= 'class/';
$init['dir']['fonts'] 				= 'fonts/';

$init['dir']['common.tpl'] 			= '{dir.common}tpl/';
$init['dir']['common.css']			= '{dir.common}css/';
$init['dir']['common.img']			= '{dir.common}img/';
$init['dir']['common.js']			= '{dir.common}js/';
$init['dir']['common.language'] 	= '{dir.common}language/';

$init['dir']['project'] 			= 'project/';
$init['dir']['content'] 			= '{dir.project}content/';
$init['dir']['template'] 			= '{dir.project}template/';
$init['dir']['language'] 			= '{dir.project}language/';
$init['dir']['page'] 				= '{dir.project}page/';
/*
	PAGE CONFIG
*/
$pages								= array();
/*
	META FILE NAME
*/
$init['file']['script'] 			= 'script';
$init['file']['style'] 				= 'style';
/*
	META -> CONTENT
*/
$meta['meta']['zotune'] 			= array('name'=>'generator', 'content'=>$app[1][1].' v'.$app[1][2]);
//$meta['meta']['viewport:m'] 		= array('name'=>'viewport', 'content'=>'initial-scale=1.0, user-scalable=no');
/*
	META -> SCRIPT
*/
$meta['script']['jq']				= array('src'=>'{dir.common.js}jquery-1.11.1.min', 'type'=>'text/javascript');
$meta['script']['ui']				= array('src'=>'{dir.common.js}jquery-ui.1.11.0.min', 'type'=>'text/javascript');
$meta['script']['zj']				= array('src'=>'{dir.common.js}zj', 'type'=>'text/javascript');
$meta['script']['common'] 			= array('src'=>'{dir.template}js/common');
/*
	META -> CSS
*/
$meta['link']['favicon'] 			= array('href'=>'{dir.template}images/favicon', 'type'=>'image/png', 'rel'=>'icon');
$meta['link']['font']				= array('href'=>'{dir.template}css/font', 'type'=>'text/css');
$meta['link']['common']				= array('href'=>'{dir.template}css/common', 'type'=>'text/css');
$meta['link']['layout']				= array('href'=>'{dir.template}css/layout', 'type'=>'text/css');
/*
	SQL
*/
$database							= NULL;
/*
	DEVICE
*/
$init_device_check	 				= array('tab'=>'t','mob'=>'m','desk'=>'d');
$init_device_index					= array('device','name','os','browser');
$init_device_tpl 					= array('.html','.device.html','.device.name.html','.device.name.browser.html');
/*
	CURRENCY
*/
$init['currency'] 					= array('is'=>'USD','symbol','&#36;');
/*
	SIL
*/
$init['sil.require'] 				= array('main'=>'language.php', 'common'=>'common.php');
$init['sil.list'] 					= array('en'=>'English','no'=>'Norwegian','zo'=>'Zolai');
$init['sil.default'] 				= 'en';
/*
	SETTING
*/
$init['iprecord'] 					= true;
$init['pro.released.year'] 			= true;//2008-2012
$init['pwd.encryption'] 			= 'sha1';//sha1, md5
$init['msg.maintaining'] 			= NULL;
$init['msg.db.error'] 				= 'SQL -> DABABASE connection.....';
$init['ads']						= array();
$init['prefix']						= 'db_';
/*
	NAME
*/
$init['nametitle'] 					= array(1=>'Pu.',2=>'Pi.',3=>'Pa.',4=>'Nu.',5=>'Gang.',6=>'Ni.',7=>'U.',8=>'Nau.',9=>'Sia.',10=>'Lawm.',11=>'Taang.',12=>'Lia.');
$init['gender'] 					= array(1=>'Male',2=>'Female');
/*
	ESSENTIAL
*/
$init_require_islogged				= NULL;
$init_require_essential				= NULL;
$init['require.supports']			= NULL;
$init['require.consigns']			= NULL;
/*
	ADMINISTRATOR -> delete, edit
*/
$init['admin']['level']				= array(0=>'Visitor',1=>'Member',2=>'Approval Member',3=>'Supervisor',4=>'Manager',5=>'Administrator');
$init['blog']['catalog']			= array(0=>'none',1=>'FAQ');
/*
$init['admin']['comment']			= array('1');
$init['admin']['lyric']				= array('1');
$init['admin']['tag']				= array('level'=>4,'userid'=>array(1));
*/
/*
	MANAGEMENTS

$pages['-managements'] = array(
	"Method"=>"none",
	"page_class"=>"zotune", "page_id"=>"zotune",
	"including"=>"info.zotune",
	"menu"=>"ZOTUNE",
	"page_link"=>"-managements",
	"page_type"=>"system",

		"authorization" => array(
			"user_title" => array("1")
		)
	);
$pages['-managements']['zip'] = array(
	"including"=>"msg",
	"Method"=>"zipProject",
	"menu"=>"ZIP Project",
	"page_link"=>"-managements/zip"
	);
*/