<?php
class storage extends zotune
{
	private $type = array('main','love');
	public $sql_task=array();
	public $album_data=array();
	public $album_requirement=array(
			'PATH'=>array('text'=>'(url)'),
			'UNIQUEID'=>array('text'=>'(id)'),
			'TRACKS'=>array('text'=>'(num)'),
			'PLAYS'=>array('text'=>'(num)'),
			'GENRE'=>array('text'=>'(varchar)'),
			'LANG'=>array('text'=>'(num)'),
			'SERVER'=>array('text'=>'(num)'),
			'COMMENT'=>array('text'=>'(text)','type'=>'textarea'),
			'STATUS'=>array('text'=>'(num)'),
			'YEAR'=>array('text'=>'(yyyy)'),
			'STUDIO'=>array('text'=>'(varchar) STUDIO'),
			'ALBUM'=>array('text'=>'(varchar) ALBUM'),
			'COPYRIGHT'=>array('text'=>'(varchar) COPYRIGHT'),
			'ARTIST'=>array('text'=>'(varchar) ARTIST'),
			'WRITER'=>array('text'=>'(varchar) WRITER'),
			'BAND'=>array('text'=>'(varchar) BAND'),
			'DATA'=>array('text'=>'(json)','type'=>'textarea')
		);
	private $album_info;
	private $fid;
	public function home()
	{
		self::$info['username']=parent::$user['fullname'];
		$page 			= $this->uri[1];
		if(file_exists($helper = self::$info['dir.project']."class.storage.$page.php")){
			require_once $helper;
		}else{
			$page='main';

			require_once self::$info['dir.project']."class.storage.$page.php";
		}
		$class = new $page();
		$class->userid 	= parent::$user['id'];
		$class->level 	= parent::$user['level'];

		$class->page 	= $page;
		$class->action 	= $this->uri[2];
		$class->option 	= $this->uri[3];
		$class->obid 	= $this->uri[4];
		$class->dir_filter($_GET['prefix']);
		$class->get_tables();
		if(in_array($page, $this->type)){
			if(is::ajax()){
				self::$info['page.type']='json';
				self::$data['page.data']=$class->sorry();
			}else{
				$class->dir_navigator();
				$class->home();
			}
		}else{
			self::$info['page.type']='json';
			if(in_array($class->action,get_class_methods($class))) {
				self::$data['page.data']=call_user_func(array($class,$class->action));
			}else{
				self::$data['page.data']=$class->home();
			}
		}
	}
	private function dir_filter($dir)
	{
		$this->prefix=array_filter(array_map(
			function($d){
				return call_user_func('rawurldecode', $d);
			},explode('/',rawurldecode($dir))
		));
	}
	public function dir_detail($dir)
	{
		$j['object']=implode('/',$dir);
		$j['object_md5']=md5($j['object']);
		$j['object_path']=self::$init['music.server'].$j['object'];
		$j['filename']=end($dir);
		$j['filename_md5']=md5($j['filename']);
		array_pop($dir);
		$j['dir']=implode('/',$dir);
		$j['dir_path']=self::$init['music.server'].$j['dir'];
		$j['dir_md5']=md5($j['dir']);
		return $j;
	}
	public function isAlbum($j)//dir_read
	{
		if($storage=opendir($j['object_path'])) {
			while(false !== ($name=readdir($storage))){
				$ext=strtolower(pathinfo($name, PATHINFO_EXTENSION));
				if($ext=='mp3'){
					$mp3[]=$name;
				}
				if($name=='info.txt'){
					$infotxt='/'.$name;
					//$this->album_info=$name;
				}
			}
			if(isset($mp3)){
				$this->album_data['TRACKS']=count($mp3);
				if($infotxt)$this->isAlbumInfo($j['object_path'],$infotxt);
				$this->album_data['PATH']=$j['object'];
				$this->album_data['UNIQUEID']=$j['object_md5'];
				$this->album_data['LANG']=($l=array_search($this->prefix[0],parent::$init['tracklanguages']))?$l:0;
				return $mp3;
			}
			closedir($storage);
		}
		/*
		if($storage=opendir($j['object_path'])) {
			while(false !== ($name=readdir($storage))){
				$extension = strtolower(end(explode('.', $name)));
				//$ext=strtolower(pathinfo($name, PATHINFO_EXTENSION));
				if($extension=='mp3')$mp3[]=$name;
				if($name=='info.txt')$infotxt='/'.$name;
			}
			if(isset($mp3)){
				$this->album_data['TRACKS']=count($mp3);
				if($infotxt)$this->isAlbumInfo($j['object_path'],$infotxt);
				$this->album_data['PATH']=$j['object'];
				$this->album_data['UNIQUEID']=$j['object_md5'];
				$this->album_data['LANG']=($l=array_search($this->prefix[0],parent::$init['tracklanguages']))?$l:0;
				return $mp3;
			}
			closedir($storage);
		}
		*/
	}
	public function isAlbumInfo($dir,$infotxt)//dir_readInfo_txt
	{
		//need to modify if track tag going to be added...
		header('Content-Type:charset=utf-8');
		if($infotxt){
			$content=file_get_contents($dir.$infotxt);
			$info=preg_replace('/[^\x20-\x7E]/','', explode("\n", $content));
			if ($info){
				foreach($info as $ik => $iv){
					$ivs = explode(":",$iv);
					if(isset($ivs[0]) and isset($ivs[1])){
						$ald[trim($ivs[0])]=trim($ivs[1]);
					} else {
						$ald[]=trim($iv);
					}
				}
				if($ald)foreach($ald as $i => $v)$this->album_data[strtoupper($i)]=$v;
			}
			$this->album_data['DATA']=json_encode($ald);
		}
	}
	public function isFileExists($file)//api_checkfile
	{
		if(file_exists($f = $file)) {
			return $f;
		}
	}
	public function getFileContent($file)//api_readfile
	{
		return file_get_contents($file);
	}
	private function dir_navigator()
	{
		foreach($this->prefix as $name):
			$dir[]=call_user_func('rawurlencode', $name);
			$href=implode('/',$dir);
			//self::$data['dir.nav'] .=$this->ztf($this->getTemplate('navigator'),false,NULL,array('href'=>$href,'name'=>$name));

			$this->ztf('navigator','dir.nav',array('href'=>$href,'name'=>$name));
		endforeach;
	}
}