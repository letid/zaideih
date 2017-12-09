<?php
class orem extends storage
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
		$j=$this->dir_detail($this->prefix);
		$z['msg']=$j['filename'].' is deleted!';
		if(is_dir($j['object_path'])){
			if(false == rmdir($j['object_path']))$z['msg']=$j['filename'].' could not delete as dir, it contains need to delete first!';
		}else{
			if(false == unlink($j['object_path']))$z['msg']=$j['filename'].' could not delete as file!';
		}
		return $z;
	}
	private function form($id,$prefix,$filename,$msg)
	{
		$j['p'][]=array('t'=>'p', 'd'=>array('html'=>"Are you sure to delete {$filename}?")
		);
		$j['p'][]=array('t'=>'p', 'd'=>array('title'=>'filename'),'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'text','name'=>'filename','value'=>$filename))
			)
		);
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'hidden','name'=>'prefix','value'=>$prefix)),
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Delete!'))
			)
		);
		$this->fid='ORM-'.$id;
		$j['p'][]=array('t'=>'p', 'd'=>array('name'=>'msg','class'=>'msg','html'=>$msg));
		return array('t'=>'form',
			'd'=>array('class'=>'d1 orem','id'=>$this->fid,'method'=>'get','action'=>'orem/post'),
			'l'=>$j['p']
		);
	}
}