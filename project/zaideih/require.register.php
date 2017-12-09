<?php
class register extends zotune
{
	public function home()
	{
		self::$info['qno'] 	= array('album','artist','track');
		self::$data['qn'] 	= in_array($_GET['qn'], self::$info['qno'])?$_GET['qn']:'avekpi';
		self::$data['qn.'.self::$data['qn'].'.check'] = 'checked="checked"';
	}
	protected static function current_user_info()
	{
		self::$Meta['meta']['uid'] = array('name'=>'z:uid', 'content'=>self::$user['id']);
		self::$Meta['meta']['unm'] = array('name'=>'z:unm', 'content'=>self::$user['fullname']);

	}
}