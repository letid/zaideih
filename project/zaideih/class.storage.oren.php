<?php
class oren extends storage
{
	public function home()
	{
		$j=$this->dir_detail($this->prefix);
		$z['zj'][]=$this->form($j['object_md5'],$j['object'],$j['filename'],$msg);
		$z['fid']=$this->fid;
		return $z;
	}
	public function post()
	{
		$this->j=$this->dir_detail($this->prefix);
		$this->filename=trim($_GET['filename']);
		if (false == rename($this->j['object_path'], $this->j['dir_path'].'/'.$this->filename)) {
			$z['msg']='Could not rename.';
		}else{
			if(method_exists($this,$this->option)){
				return call_user_func(array($this,$this->option));
			}else{
				$z['msg']='Done';
			}
		}
		return $z;
	}
	private function mp3()
	{
		$uniqueid = $this->j['dir_md5'];
		$oldfile = addslashes($this->j['filename']);
		$newfile = addslashes($this->filename);
		$msg=new sql("UPDATE $this->db_track SET PATH='$newfile' WHERE UNIQUEID LIKE '$uniqueid' AND PATH LIKE '$oldfile'");
		if(is_numeric($msg->msg)){
			$z['msg']='Done, updated DB!';
		}else{
			$z['msg']=$msg->msg;
		}
		return $z;
	}
	private function album()
	{
		$uniqueid_old = $this->j['object_md5'];
		$dir = $this->j['dir'].'/'.$this->filename;
		$path = addslashes($dir);
		$uniqueid_new = md5($dir);
		if($uniqueid_old != $uniqueid_new){
			$s=new sql("SELECT ID FROM $this->db_album WHERE UNIQUEID LIKE '$uniqueid_old'");
			if($s->total > 0){
				$a=new sql("UPDATE $this->db_album SET PATH='$path', UNIQUEID='$uniqueid_new' WHERE UNIQUEID LIKE '$uniqueid_old'");
				$t=new sql("UPDATE $this->db_track SET UNIQUEID='$uniqueid_new' WHERE UNIQUEID LIKE '$uniqueid_old'");
				$z['msg']=sprintf('RE: (Album %s) (Track %s) Done!',$a->msg,$t->msg);
			}else{
			}
		}else{
			$z['msg']='Done, its the same!';
		}
		return $z;
	}
	private function form($id,$prefix,$filename,$msg)
	{
		$j['p'][]=array('t'=>'p', 'd'=>array('title'=>'filename'),'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'text','name'=>'filename','value'=>$filename))
			)
		);
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'hidden','name'=>'prefix','value'=>$prefix)),
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Rename!'))
			)
		);
		$this->fid='ORN-'.$id;
		$j['p'][]=array('t'=>'p', 'd'=>array('name'=>'msg','class'=>'msg','html'=>$msg));
		return array('t'=>'form',
			'd'=>array('class'=>'d1 oren','id'=>$this->fid,'method'=>'get','action'=>'oren/post/'.$this->action),
			'l'=>$j['p']
		);
	}
}