<?php 
class comment extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->action));
	}
	public function post()
	{
		$USER=($this->userid)?$this->userid:0;
		$name=addslashes($_POST['name']); $comment=addslashes($_POST['comment']);
		if($name && $comment){
			$s=new sql("SELECT * FROM $this->db_blog WHERE (CATALOG=1) AND (CODE='$this->laid') AND (NAME LIKE '$name') AND (CONTENT LIKE '$comment')");
			if($s->total){
				$z['msg']='Recently commented!';
			}else{
				new sql("INSERT INTO $this->db_blog SET CATALOG=1, USER=$USER, CODE='$this->laid', NAME='$name', CONTENT='$comment'");
				$z['msg']='Commented';
				$z['is']='done';
			}
		}else{
			$z['msg']='Required fields';
		}
		$z['zj']=$this->get('ul');
		return $z;
	}
	public function remove()
	{
		$d=new sql("DELETE FROM $this->db_blog WHERE ID=$this->obid");
		$z['msg']='Deleted';
		$z['zj']=$this->get('ul');
		return $z;
	}
	public function get($get=NULL)
	{
		//DATE_FORMAT(C.DATES, '%e %b, %Y') ->  AND (C.ID > $at)
		$at=($this->obid)?$this->obid:0;
		$s=new sql("SELECT B.*,T.TITLE 
		 	FROM $this->db_track T 
				LEFT JOIN $this->db_blog AS B ON B.CODE=T.ID 
					WHERE T.ID={$this->laid} ORDER BY B.MDATE DESC LIMIT 17",'fetch_array');
		if ($s->total){
			$TITLE=$s->rows[0]['TITLE'];
			if($s->rows[0]['ID']){
				foreach($s->rows as $i => $d){
					//$odd=($c++%2==1)?'odd':NULL:
					//$admin=$this->isAuthorized($d['USER'])?array('t'=>'span', 'd'=>array('html'=>'x','class'=>'fn','data-role'=>"remove")):NULL;
					$j['ul'][]=array('t'=>'ul','d'=>array('id'=>"cp-{$d['ID']}",'class'=>($c++%2==1)?'odd':'even'), 'l'=>array(
							array('t'=>'li','d'=>array('class'=>'n'), 'l'=>array(
								array('t'=>'strong','d'=>array('html'=>$d['NAME'])),
								array('t'=>'em','d'=>array('html'=>$d['MDATE'])),
								array('t'=>'span','d'=>array('html'=>$this->isAuthorized($d['USER'])?'delete':NULL,'class'=>'fn','data-role'=>"remove"))
								)
							),
							array('t'=>'li','d'=>array('class'=>'c'), 'l'=>array(
								array('t'=>'p','d'=>array('html'=>$d['CONTENT'].'... ')), 
								array('t'=>'em','d'=>array('html'=>$this->ago($d['MDATE'])))
								)
							)
						)
					);
				}
			}else{
				$j['ul'][]=array(
					't'=>'p', 'd'=>array('class'=>'nocomment','text'=>"There is no comment for "), 'l'=>array(
						array('t'=>'strong', 'd'=>array('html'=>$TITLE)),
						array('t'=>'em', 'd'=>array('html'=>', be the first to post?'))
					)
				);
			}
			if($get)return $j[$get];
			$z['zj'][]=array('t'=>'dl', 'd'=>array('id'=>"c-{$this->laid}"), 'l'=>array(
					array('t'=>'dt', 'd'=>array('class'=>'cp'), 'l'=>$j['ul']),
					array('t'=>'dd', 'd'=>array('html'=>$TITLE))
				)
			);
		}else{
			//SORRY THERE IS NO TRACK	
		}
		/*
		$z['form'] =array('commentTo'=>"from PHP ...comment to $TITLE",'yourName'=>'...your Name','submit'=>'...Comment');
		$z['fn']='form';
		$z['empty']='fields required...';
		*/
		$z['form']=array('commentTo'=>"...comment to $TITLE",'yourName'=>'...your Name','submit'=>'...Comment');
		$z['fn']='form';
		$z['empty']='fields required...';
		
		return $z;
	}
}
?>