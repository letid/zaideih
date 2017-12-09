<?php
session_start();
//header("Content-Type:text/plain");
/*
$app['i'] = array('name'=>'ZOTUNE.developer', 'version'=>'1.5.4', 'product'=>'2.0.140317', 'site'=>'//www.zotune.com', '_'=>'zaideih');
$app['p'] = array('dir'=>'project', 'name'=>NULL, 'init'=>'index.php');
$app['db'] = array('con'=>'class.db.php', 'sql'=>'class.db.sql.php');
*/

$app[1] = array(1=>'ZOTUNE.developer', 2=>'1.5.4', 3=>'2.0.140317', 4=>'//www.zotune.com', 5=>'zotune');
$app[2] = array(1=>'project', 2=>NULL, 3=>'index.php');
$app[3] = array(1=>'class.db.php', 2=>'class.db.sql.php');


$REQUEST_URI 					= rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$URI 							= explode("/", $REQUEST_URI);
$HOST							= $_SERVER['HTTP_HOST'];
$init['app.name'] 				= $app[1][1];
$init['app.version'] 			= $app[1][2];
$init['app.site'] 				= $app[1][4];
$init['www'] 					= "//$HOST/";
$init['path'] 					= $init['www'];
/*
	CHECK ZOTUNE PROJECT CONFIGURATION
*/
if ($_SESSION[$HOST]):
	$app[2][2] = $_SESSION[$HOST];
	echo 'adf';
elseif(!$app[2][2]):
	$h = explode('.', $HOST);
	$domain_extension= end($h);
	if(count($h) > 3):
		$app[2][2] 			= isset($URI[1])?$URI[1]:$app[1][5];
		if($app[2][2]!=0) $_SESSION[$HOST]=$app[2][2];
			else $ProUnique = 1;
	elseif(count($h) > 2 and $h[0] !='www'):
		$domain_name=prev($h);
		$_SESSION[$HOST]	=$app[2][2]=prev($h);
	elseif(count($h) > 1):
		$_SESSION[$HOST]	=$app[2][2]=prev($h);
	else:
		$app[2][2] 			= isset($URI[1])?$URI[1]:$app[1][5];
		$ProUnique = 1;
	endif;
endif;
/*
	BASIC IDENTIFICATION FOR PROJECT CONFIGURATION AND NAME, PATH,
*/
$Initiate 						= implode("/",$app[2]);
$init['dir.project.main'] 		= $app[2][1];
$init['pro.name'] 				= $app[2][2];
$init['dir']['project'] 		= str_replace($app[2][3],'',$Initiate);

echo $Initiate."\n";