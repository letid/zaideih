<?php
//date_default_timezone_set('Europe/Oslo');
$database= parse_ini_file('environment.ini',true);
$init['music.server']				= $database['music.server'];

/*
	TABLE
*/
$database['mysql_table'] 			= array('users'=>'zu_users', 'visited'=>'zu_visited', 'log'=>'zu_log', 'beh'=>'zu_beh', 'zogam'=>'zu_zogam','countries'=>'zu_countries','blog'=>'zu_blog',
										'track'=>'zd_track', 'album'=>'zd_album',
										'comment'=>'zd_comment', 'favorite'=>'zd_favorite', 'lyric'=>'zd_lyric',
										'playlist'=>'zd_playlist', 'suggestion'=>'zd_suggestion',
										'image'=>'zd_image',
										'download'=>'ze_download', 'financial'=>'ze_financial', 'paypal'=>'ze_paypal'
									);
/*
	ESSENTIAL
*/
$init_require_islogged				= array('register','current_user_info');
$init_require_essential				= array('home','essential','SIL','links');
$init['require.supports']			= array('IP','http_referer','ads','hits');
$init['require.consigns']			= array('MenuMain','MenuLanguage','Copyright');
$init[1] 							= array('home','privacy','register','login','profile','api','terms','contact-us','about-us','music','album','artist');
/*
	INFORMATIONS
*/
$init['pro.released'] 				= '2008';
$init['pro.version'] 				= '4.4.8';
$init['pro.product'] 				= '6.11.150107';
$init['pro.name'] 					= 'Zaideih';//Zaideih Music Station
$init['pro.description'] 			= 'ZOTUNE.developer';
$init['pro.email.noreply'] 			= 'noreply@zaideih.com';
$init['pro.author'] 				= 'Khen Solomon, Lethil';
$init['pro.author.email'] 			= 'khensolomon@gmail.com';
$init['pro.developer'] 				= 'Khen Solomon, Lethil';
$init['pro.developer.email'] 		= 'khensolomon@gmail.com';
/*
	SIL
*/
//$init['sil.list'] 					= array('en'=>'English','zo'=>'Zolai');
/*
	CONFIGURATION
*/
//$init['msg.maintaining'] 			= 'We are moving to a new server, better, faster and reliable! DNS propagation could take upto 72 hours.<br> <strong>ZOTUNE.developer</strong> beta and <strong>Zadeih 4.4.8</strong> is running at <a href="http://zaideih.zomi.today">http://zaideih.zomi.today</a>!';

$init['tracklanguages'] 			= array(0=>'untitle',1=>'zola',2=>'myanmar',3=>'mizo',4=>'english',5=>'chin',6=>'haka',7=>'falam',8=>'korea',9=>'norwegian',10=>'collection');
$init['tracklanguages_des'] 		= array(
									0=>'WORKING',
									1=>'Zola, Zolapi, Laipian late',
									2=>'Myanmar Christian Musics',
									3=>'Interpret Mizo Musics',
									4=>'Hymns, Praise and Worship, etc.',
									5=>'Collection of tribal songs within India, Myanmar, Thai...',
									6=>'Hymns, Praise and Worship, etc.',
									7=>'Hymns, Praise and Worship, etc.',
									8=>'Hymns, Praise and Worship, etc.',
									9=>'Hymns, Praise and Worship, etc.',
									10=>'the best 50s,60s,70s,80s,90s collection'
									);
$init['trackcost'] 					= array(1=>'3.2',2=>'3',3=>'2.8',4=>'1.2',5=>'1',6=>'1',7=>'1',8=>'1',9=>'1');
$init['trackfavorite'] 				= array(0=>'no',1=>'like',2=>'top',3=>'allthetime',4=>'note');
/*
	ADMINISTRATOR -> delete, edit
*/
$init['admin']['comment']			= array('level'=>4,'userid'=>array(1));
$init['admin']['lyric']				= array('level'=>4,'userid'=>array(1));
$init['admin']['tag']				= array('level'=>4,'userid'=>array(1));
$init['admin']['privacy']			= array('level'=>4,'userid'=>array(1));
$init['admin']['faq']				= array('level'=>4,'userid'=>array(1));
/*
	META -> CONTENT
*/
$meta['meta']['gsv'] 				= array('name'=>'google-site-verification','content'=>'XgR11ECQAh8qYpej1TCvYEBX8LioEnTBEfn0FedmNZA');
/*
	META -> SCRIPT
*/
$meta['script']['zs']				= array('src'=>'{dir.project}player/zaideih');
/*
	META -> CSS
*/
$meta['link']['d:firefox']			= array('href'=>'{dir.template}css/layout.firefox', 'type'=>'text/css');
$meta['link']['d:ie']				= array('href'=>'{dir.template}css/layout.ie', 'type'=>'text/css');
