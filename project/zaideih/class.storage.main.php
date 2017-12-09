<?php
class main extends storage
{
	private $obj_extension=array('mp3','txt','jpg','png','config','ogg','avi','bmp','pdf','wav');
	private $obj_hide=array('.','..');
	private $obj_unwanted=array('ds_store','ini','db','m3u','nfo','sfv','ini','cue','log','html');
	public function home()
	{
		$dir=$this->dir_detail($this->prefix);
		self::$data['dir.main']=rawurlencode($dir['object']);
		if($storage=opendir($dir['object_path'])) {
			while(false !== ($name=readdir($storage))){
				if(!in_array($name,$this->obj_hide)){
					$href=$dir['object'].'/'.$name;
					$d['id']=md5($href);
					$d['href']=rawurlencode($href);
					$d['name']=$name;
					$ext=strtolower(pathinfo($name, PATHINFO_EXTENSION));

					if(in_array($ext,$this->obj_unwanted))$d['class']='unwanted '.$ext;
						elseif(in_array($ext,$this->obj_extension)) $d['class']=$ext;
							else $d['class']='album';
					$this->ztf('dir.list',true,$d);
				}
			}
			if(!isset(self::$data['dir.list'])){
				$this->ztf('dir.list.no','dir.list',array('dir'=>$dir['object']));
			}
			closedir($storage);
		}else{
			$this->ztf('dir.list.not','dir.list',array('dir'=>$dir['object']));
		}
	}
	public function sorry()
	{
		$is =($this->action)?$this->action:$this->page;
		$z['msg']="{$is}: method not ready!";
		return $z;
	}
}