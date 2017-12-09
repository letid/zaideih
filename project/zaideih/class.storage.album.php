<?php
class album extends storage
{
	public function home()
	{
		$j=$this->dir_detail($this->prefix);
		$z['msg']="CK: no Mp3!";
		$this->post_directly=($this->action=='submit')?true:false;
		if($mp3=$this->isAlbum($j)){
			$z['zj'][]=$this->form();
			$z['fid']=$this->fid;
			if($this->post_directly==true){
				$message =($this->sql_tast)?$this->sql_tast:'(sql problem)';
				$z['msg']=sprintf('CK: (%s) %s Ok!',count($mp3),$message);
			}else{
				$z['msg']=sprintf('CK: (%s) Ok!',count($mp3));
			}
		}
		return $z;
	}
	public function post($DirectData=NULL)
	{
		foreach($this->album_requirement as $i => $d){
			$data=is_array($DirectData)?$DirectData[$i]:$_GET[$i];
			if($data){
				$AlbumData[$i]=$data;
				${$i}=$data;
			}
		}
		if($AlbumData){
			$set_query=get::query($AlbumData);
			$s=new sql("SELECT * FROM $this->db_album WHERE UNIQUEID='$UNIQUEID'");
			if ($s->total){
				$query = "UPDATE $this->db_album SET $set_query WHERE UNIQUEID='$UNIQUEID'";
			} else {
				$query = "INSERT INTO $this->db_album SET $set_query";
			}
			$msg=new sql($query);
			$z['msg']=$msg->msg;
			if(is_numeric($msg->msg)){
				$task=strtok($query, ' ');
				$z['msg']=$task.' -> success...';
				$this->sql_tast=$task;
			}
		}else{
			$z['msg']='It seem nothing to submit!';
		}
		return $z;
	}
	private function form()
	{
		foreach($this->album_requirement as $i => $d){
			$value=$this->album_data[$i];
			if(isset($d['type'])){
				$input=array('t'=>$d['type'], 'd'=>array('name'=>$i,'html'=>$value));
			}else{
				$input=array('t'=>'input', 'd'=>array('type'=>'text','name'=>$i,'value'=>$value));
			}
			$j['p'][]=array('t'=>'p', 'd'=>array('title'=>$i),
				'l'=>array(
					$input,array('t'=>'label', 'd'=>array('html'=>$d['text']))
				)
			);
		}
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),
			'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Submit'))
			)
		);
		if($this->post_directly==true){
			$r=$this->post($this->album_data);
			$msg=$r['msg'];
		}else{
			$msg='Ready to submit?';
		}
		$this->fid='ALB-'.$this->album_data['UNIQUEID'];
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>$msg));
		return array('t'=>'form', 'd'=>array('class'=>'d1','id'=>$this->fid,'method'=>'get','action'=>'album/post'), 'l'=>$j['p']);
	}
}