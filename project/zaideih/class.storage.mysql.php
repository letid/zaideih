<?php 
class mysql extends storage
{
	public function home()
	{
		$j=$this->dir_detail($this->prefix);
		$z['zj'][]=$this->form($j['object_md5'],$msg);
		$z['fid']=$this->fid;
		return $z;
	}
	public function post()
	{
		$sql=new sql($_POST['query']);
		$z['msg']=$sql->msg;
		if($sql->total){
			//$sql->fetch_array();
			$sql->fetch_assoc();
			foreach($sql->rows as $i => $r){
				$j['ol'][]=array('t'=>'ol','d'=>array('class'=>'row'), 'l'=>array_map(function($d,$k){
							return  array('t'=>'li', 'd'=>array('html'=>$d, 'class'=>$k));
						},$r, array_keys($r)
					)
				);
			}
			$z['zj'][]=array('t'=>'div','d'=>array('class'=>'response'),  'l'=>$j['ol']);
		}
		return $z;
	}
	private function form($id,$msg)
	{
		$j['p'][]=array('t'=>'p', 'l'=>array(
				array('t'=>'textarea', 'd'=>array('name'=>'query','html'=>'')),
			)
		);
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...send Query!'))
			)
		);
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'query','html'=>'zd_album, zd_track select * from zd_track where ID=4; TRUNCATE TABLE table; ALTER TABLE table AUTO_INCREMENT=value;'));
		$this->fid='SQL-'.$id;
		$j['p'][]=array('t'=>'p', 'd'=>array('name'=>'msg','class'=>'msg','html'=>$msg));
		return array('t'=>'form', 
			'd'=>array('class'=>'d1 mysql','id'=>$this->fid,'method'=>'post','action'=>'mysql/post'), 
			'l'=>$j['p']
		);
	}
}
?>