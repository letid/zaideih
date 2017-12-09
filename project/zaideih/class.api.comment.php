<?php
class comment extends api
{
	public function home()
	{
		require_once(self::$init['dir']['class'].'class.zawgyi2unicode.php');
		return call_user_func(array($this,$this->action));
	}
	public function post()
	{
/*
		$zawgyi2unicode= new zawgyi2unicode();
		$conts = $zawgyi2unicode->zg_uni(data);
*/
		$zawgyi2unicode=new zawgyi2unicode();
		$userid=($this->userid)?$this->userid:0;
		$name=addslashes($_POST['name']); $comment=addslashes($_POST['comment']);
		if($name && $comment){
			//$name=$zawgyi2unicode->zg_uni($name);
			//$comment=$zawgyi2unicode->zg_uni($comment);
			$s=new sql("SELECT * FROM $this->db_comment WHERE (CODE='$this->laid') AND (NAME LIKE '$name') AND (DESCRIPTION LIKE '$comment')");
			if($s->total){
				$z['msg']='Recently commented!';
			}else{
				new sql("INSERT INTO $this->db_comment SET USER=$userid, CODE='$this->laid', NAME='$name', DESCRIPTION='$comment'");
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
		$d=new sql("DELETE FROM $this->db_comment WHERE ID=$this->obid");
		$z['msg']='Deleted';
		$z['zj']=$this->get('ul');
		return $z;
	}
	public function get($get=NULL)
	{
		//DATE_FORMAT(C.DATES, '%e %b, %Y') ->  AND (C.ID > $at)
		$at=($this->obid)?$this->obid:0;
		$s=new sql("SELECT C.*,T.TITLE, DATE_FORMAT(C.DATE, '%e %M, %Y') as DFM
		 	FROM $this->db_track T
				LEFT JOIN $this->db_comment AS C ON C.CODE=T.ID
					WHERE T.ID={$this->laid} ORDER BY C.DATE DESC LIMIT 17",'fetch_array');
		if ($s->total){
			$TITLE=$s->rows[0]['TITLE'];
			if($s->rows[0]['ID']){
				foreach($s->rows as $i => $d){
					//$d['DFM']
					$j['ul'][]=array('t'=>'ul','d'=>array('id'=>"cp-{$d['ID']}",'class'=>($c++%2==1)?'odd':NULL), 'l'=>array(
							array('t'=>'li','d'=>array('class'=>'n'), 'l'=>array(
								array('t'=>'strong','d'=>array('html'=>$d['NAME'])),
								array('t'=>'em','d'=>array('html'=>$d['DFM'])),
								array('t'=>'span','d'=>array('html'=>$this->isAuthorized($d['USER'])?'delete':NULL,'class'=>'fn','data-role'=>"remove"))
								)
							),
							array('t'=>'li','d'=>array('class'=>'c'), 'l'=>array(
								array('t'=>'p','d'=>array('html'=>$d['DESCRIPTION'].'... ')),
								array('t'=>'em','d'=>array('html'=>$this->ago($d['DATE'])))
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
		$z['form']=array('commentTo'=>"...comment to $TITLE",'yourName'=>'...your Name','submit'=>'...Comment');
		$z['fn']='form';
		$z['empty']='fields required...';

		return $z;
	}
}