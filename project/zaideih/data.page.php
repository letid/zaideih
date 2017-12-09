<?php
/*
type -> content, api, page, guest, user, system

home -> VALUES CAN NOT BE EMPTY, AS IT USED TO LOAD WHEN OTHER PAGE ARE NOT PROPERLY DEFINED.
Bar,Header, Content, Footer, Layout -> 'none' MEANING NOT TO LOAD, BUT IF NOT DEFINED USED home VALUE

navigator -> EMPTY OR NOT DEFINED, it's NULL
authorization=>array('user'=>'member','age'=>18,'country'=>'NO','ethnic'=>'zomi','level'=>'1','id'=>'1','code'=>'454345MJ')
authorization->user=>all,visitor,member,manager,administrator

		'authorization'=>array(
			'title'=>array('1'=>array()),
			'age'=>18,
			'age'=>array('30','operator'=>'>='),
			'country'=>array('NO'),
			'ethnic'=>array('zomi'),
			'level'=>array('1'),
			'id'=>array('1'),
			'code'=>array('KDHIE3454')
		)

		'RequirePage'=>array(
			'class.definition.php',
			'class.math.evalmath.php',
			'class.n2en.php',
			'class.n2my.php',
			'class.mobythesaurus.php'
		),

		'RequireMeta'=>array(
			'link'=>array(
				'css'=>array('href'=>'url', 'type'=>'text/css', 'rel'=>'stylesheets')
			),
			'script'=>array(
				'js'=>array('src'=>'url', 'type'=>'text/javascript'),
				'jquery'=>null
			),
			'meta'=>array(
				'icon'=>array('name'=>'c', 'content'=>'c')
			),
		),


	'hotlink'=>array('page.link_text_goes_here'=>'page.id_goes_here'),

	'require.supports'=>array('http_referer','etc','link_site','link_privacy_policy'),
	'require.consigns'=>array('mainMenu','silMenu','solMenu','copyright','visited'),
SELECTABLE -> ADD OR REMOVE
	'require.supports'=>array('+'=>array('etc'),'-'=>array('link_privacy_policy'));
	'require.consigns'=>array('+'=>array('etc'),'-'=>array('link_privacy_policy'));
MODIFY DEFAULT essential
	'require.supports'=>array('current_dic_info')
COMPLETELY TRUN OFF from page data
	'require.supports'=>false,
	'require.consigns'=>true,
DEACTIVATE ALL PLUGIN
	'require.supports'=>false,
	'require.consigns'=>false,

DYNAMIC FILE REQUIRE
	page/currentpage/lang_{sil}.php
	language file and page id are the same

	META css file and page template are the same
*/
$pages['home']=array(
	'page.id'=>'zaideih', 'page.class'=>'home',
	'Class'=>'page', 'Method'=>'home',
	'menu'=>'Home',
	'page.link'=>NULL,
	'page.type'=>'page',
	//'page.current'=>NULL,
	'RequireTemplate'=>array(
		'page.data'=>'template',
		'page.bar'=>'bar',
		'page.board'=>'board',
		'page.content'=>'content',
		'page.footer'=>'footer',
		'layout.main'=>'layout'
	)
);
$pages['deploy']=array(
	'Class'=>false,
	'page.including'=>'page.deploy.php',
	'menu'=>'Deploy',
	'page.link'=>'deploy',
	'page.type'=>'page',
	'Deploy'=>true,
	'require.supports'=>false,
	'require.consigns'=>false,
	'RequireTemplate'=>false,
	'RequireLanguage'=>false,
	'RequireMeta'=>false
);
$pages['music']=array(
	'Class'=>'music',
	'page.class'=>'music',
	'navigator'=>true,
	'menu'=>'Music',
	'page.link'=>'music',
	'RequirePage'=>array(
		'class.music.php',
		'class.db.pagination.php',
		'class.counthour.php'
	)
);
$pages['artist']=array(
	'Class'=>'music',
	'page.class'=>'artist',
	'navigator'=>true,
	'menu'=>'Artist',
	'page.link'=>'artist',
	'page.current'=>'music',
	'RequirePage'=>array(
		'class.music.php',
		'class.db.pagination.php',
		'class.counthour.php'
	),
	'RequireTemplate'=>array('page.data'=>NULL)
);
$pages['album']=array(
	'Class'=>'music',
	'page.class'=>'album',
	'navigator'=>true,
	'menu'=>'Album',
	'page.link'=>'album',
	'page.current'=>'music',
	'RequirePage'=>array(
		'class.music.php',
		'class.db.pagination.php',
		'class.counthour.php'
	),
	'RequireTemplate'=>array('page.data'=>NULL)
);
$pages['lyric']=array(
	'Method'=>'lyric',
	'page.class'=>'lyric',
	'menu'=>'Lyric',
	'page.link'=>'lyric'
);
$pages['comment']=array(
	'Method'=>'comment',
	'page.class'=>'comment',
	'menu'=>'Comment',
	'page.link'=>'comment'
);
$pages['contact-us']=array(
	'Method'=>'contact-us',
	'page.class'=>'contact-us',
	'menu'=>'Contact us',
	'page.link'=>'contact-us'
);
$pages['about-us']=array(
	'Method'=>'about_us',
	'page.class'=>'about-us',
	'navigator'=>true,
	'menu'=>'About us',
	'page.link'=>'about-us'
);
$pages['terms']=array(
	'Method'=>'contents',
	'page.class'=>'terms',
	'navigator'=>true,
	'menu'=>'Terms',
	'page.link'=>'terms'
);
$pages['privacy']=array(
	'Method'=>'contents',
	'page.class'=>'privacy',
	'navigator'=>true,
	'menu'=>'Privacy',
	'page.link'=>'privacy',
	'RequireTemplate'=>array(
		'page.board'=>false
	),
	'require.supports'=>array(
		'-'=>array('ads')
	),
);
$pages['faq']=array(
	'Method'=>'faq',
	'page.class'=>'faq',
	'navigator'=>true,
	'menu'=>'FAQ',
	'page.link'=>'faq',
	'RequirePage'=>array(
		'class.db.pagination.php'
	),
);
$pages['register']=array(
	'page.class'=>'register', 'Class'=>'registration', 'Method'=>'home',
	'navigator'=>true,
	'menu'=>'Register',
	'page.link'=>'register',
	'page.type'=>'guest',
	'RequireTemplate'=>array(
		'page.board'=>false
	),
	'RequirePage'=>array(
		'class.user.registration.php','class.info.php'
	)
);
$pages['login']=array(
	'page.class'=>'login', 'Class'=>'login',
	'navigator'=>true,
	'menu'=>'Login',
	'page.link'=>'login',
	'page.type'=>'guest',
	'RequireTemplate'=>array(
		'page.board'=>false
	),
	'RequirePage'=>array(
		'class.user.login.php','class.info.php'
	)
);
$pages['store']=array(
	'Class'=>'store',
	'page.class'=>'store',
	'navigator'=>true,
	'menu'=>'store',
	'page.link'=>'store',
	'page.type'=>'user'
);
$pages['profile']=array(
	'Class'=>'profile',
	'page.class'=>'profile',
	'navigator'=>true,
	'menu'=>'Profile',
	'page.link'=>'profile',
	'page.type'=>'user',
	'RequireTemplate'=>array(
		'page.board'=>false
	),
	'RequirePage'=>array(
		'class.user.profile.php','class.info.php'
	)
);
$pages['profile']['update']=array(
	'Method'=>'profile_update',
	'navigator'=>true,
	'menu'=>'Update Profile!',
	'page.link'=>'profile/update',
	'page.type'=>'user'
);
$pages['profile']['cheml']=array(
	'navigator'=>true,
	'menu'=>'Change Email!',
	'page.link'=>'profile/update?cheml',
	'page.type'=>'user'
);
$pages['profile']['churn']=array(
	'navigator'=>true,
	'menu'=>'Change Username!',
	'page.link'=>'profile/update?churn',
	'page.type'=>'user'
);
$pages['profile']['chpwd']=array(
	'navigator'=>true,
	'menu'=>'Change password!',
	'page.link'=>'profile/update?chpwd',
	'page.type'=>'user'
);
$pages['profile']['logout']=array(
	'navigator'=>true,
	'menu'=>'Logout',
	'page.link'=>'?logout',
	'page.type'=>'user'
);
$pages['api']=array(
	'Class'=>'api',
	'menu'=>'API',
	'page.link'=>'api',
	'page.type'=>'json',
	'RequirePage'=>array('class.api.php','class.db.pagination.php'),//,'class.zawgyi2unicode.php'
	'require.supports'=>array(
		'-'=>array('ads','hits')
	),
	'require.consigns'=>false,
	'RequireTemplate'=>false,
	'RequireLanguage'=>false,
	'RequireMeta'=>false
);
$pages['sitemap']=array(
	'Class'=>'sitemap',
	'menu'=>'Site map',
	'page.link'=>'sitemap',
	'page.type'=>'api',
	'RequirePage'=>array('class.sitemap.php','class.db.pagination.php'),
	'require.supports'=>array(
		'-'=>array('ads','hits')
	),
	'require.consigns'=>false,
	'RequireTemplate'=>false,
	'RequireLanguage'=>false,
	'RequireMeta'=>false
);
$pages['storage']=array(
	'Class'=>'storage',
	'menu'=>'Storage',
	'page.link'=>'storage', 'page.class'=>'storage', 'page.type'=>'user', 'navigator'=>true,
	'authorization'=>array(
		'id'=>array('1')
	),
	'RequirePage'=>array(
		'class.storage.php',
		'getid3/getid3.php',
		'getid3/write.php'
	),
	'require.supports'=>array(
		'-'=>array('ads','hits')
	),
	'require.consigns'=>false,
	'RequireMeta'=>array(
		'script'=>array('common'=>false)
	)
);
$pages['contents-editor']=array(
	'Method'=>'contents_editor',
	'page.class'=>'contents-editor',
	'menu'=>'Contents editor',
	'page.link'=>'contents-editor',
	'require.supports'=>false,
	'require.consigns'=>false,
	'RequireTemplate'=>false,
	'RequireLanguage'=>false,
	'RequireMeta'=>false
);