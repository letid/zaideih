<?php 
class lyric extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->action));
	}
	public function get()
	{
		
		$s=new sql("SELECT L.*,T.TITLE AS OT FROM $this->db_track T LEFT JOIN $this->db_lyric L ON L.TRACK=T.ID WHERE T.ID=$this->laid ORDER BY L.DATE DESC");
		$s->fetch_assoc('ID');
		if ($s->total){
			if(isset($s->rows[0])){
				$msg ="Lyric not available yet for {$s->rows[0]['OT']}, would you like to post?";
				$j['ul'][]=array('t'=>'p', 'd'=>array('class'=>'unavailable','text'=>$msg), 'l'=>array(
						array('t'=>'span','d'=>array('html'=>'Yes','class'=>'fn yes','data-role'=>'yes')),
						array('t'=>'span','d'=>array('html'=>'No','class'=>'fn no','data-role'=>'no'))
					)
				);
				$j['ol']=NULL;
			}else{
				if($s->rows[$this->obid])$isCur=true;
				foreach($s->rows as $i => $d){
					$cur=($isCur?($this->obid==$d[0]['ID'])?'cur':'no':$f=(!$f)?'cur':'no');
					$zjid="lp-{$d[0]['ID']}";
					$j['ul'][]=array('t'=>'ul', 'd'=>array('id'=>$zjid,'class'=>$cur,'title'=>$this->ago($d[0]['DATE'])), 'l'=>array(
							array('t'=>'li', 'd'=>array('html'=>$d[0]['TITLE'],'class'=>'title')),
							array('t'=>'li', 'd'=>array('html'=>$d[0]['ARTIST'],'class'=>'artist')),
							array('t'=>'li', 'd'=>array('html'=>$d[0]['LYRIC'],'class'=>'lyric','title'=>$d[0]['EDITION']))
						)
					);
					if($this->isAuthorized($d[0]['USER'])){
						$option=array(
							array('t'=>'a', 'd'=>array('html'=>$d[0]['NAME'],'class'=>'fn','data-role'=>'show')),
							array('t'=>'span', 'd'=>array('html'=>'Edit','class'=>'fn','data-role'=>'yes')),
							array('t'=>'span', 'd'=>array('html'=>'Delete','class'=>'fn','data-role'=>'remove dl'))
						);
					}else{
						$option=array(
							array('t'=>'a', 'd'=>array('html'=>$d[0]['NAME'],'class'=>'fn','data-role'=>'show')),
							array('t'=>'span', 'd'=>array('html'=>'Edit','class'=>'fn','data-role'=>'yes'))
						);
					}
					$j['oli'][]=array('t'=>'li', 'd'=>array('class'=>"$zjid $cur"), 'l'=>$option);
				}
				$j['ol'][]=array("t"=>"ol", "l"=>$j['oli']);
			}
		}
		$z['zj'][]=array('t'=>'dl', 'd'=>array('id'=>'l-'.$this->laid), 'l'=>array(
				array('t'=>'dt', 'd'=>array('class'=>'lp'), 'l'=>$j['ul']),
				array('t'=>'dd', 'd'=>array('class'=>'by'), 'l'=>$j['ol'])
			)
		);
		$z['msg']='Posting....';
		$z['form']=array('Title'=>'...Title....','Artist'=>'...Artist.','yourName'=>'...your Name.','submit'=>'Post.','cancel'=>'Cancel..');
		return $z;
	}
	public function post()
	{
		$UNWANTED= array("http://","https://","fuck"); 
		$z['msg']='Error occurred during posting lyric...';
		$TRACK=$this->laid;
		$USER=($this->userid)?$this->userid:'0';
		$TITLE=addslashes($_POST['title']);
		$ARTIST=addslashes($_POST['artist']);
		$LYRIC=addslashes($_POST['lyric']);
		$NAME=addslashes($_POST['name']);
		$IP=($this->userid)?0:self::$info['ip'];

		if ($TITLE &&  $ARTIST && $LYRIC &&  $NAME && $TRACK){

			//$zawgyi2unicode= new zawgyi2unicode();
			//$TITLE=$zawgyi2unicode->zg_uni($zawgyi2unicode->html_decode($TITLE));
			//$ARTIST=$zawgyi2unicode->zg_uni($zawgyi2unicode->html_decode($ARTIST));
			//$LYRIC=$zawgyi2unicode->zg_uni($zawgyi2unicode->html_decode($LYRIC));
			//$NAME=$zawgyi2unicode->zg_uni($zawgyi2unicode->html_decode($NAME));

			//$LYRIC=$zawgyi2unicode->zg_uni($LYRIC);
			//$NAME=$zawgyi2unicode->zg_uni($NAME);
			//str_word_count($LYRIC) > 1
			if($LYRIC){
				/*
				preg_match_all('/\w+/', $LYRIC, $m);
				if(array_diff($m[0],$UNWANTED) === $m[0]){
				}else{
					$z['msg']='unwanted';
				}
				*/
				//if(is_numeric($this->obid) and $this->userid){}
				$s=new sql("SELECT ID,USER,IP FROM $this->db_lyric WHERE TRACK='$TRACK'",'fetch_array');
				if ($s->total){
					foreach($s->rows as $d){
						if($this->isAuthorization('lyric') and $this->obid==$d['ID']){
							$ID=$this->obid;break;
						}elseif($this->userid==$d['USER']){
							$ID=$d['ID'];break;
						}elseif(self::$info['ip']==$d['IP']){
							$ID=$d['ID'];break;
						}
					}
				}
				if(isset($ID)){
					$q="UPDATE $this->db_lyric SET 
						TITLE='$TITLE',ARTIST='$ARTIST',LYRIC='$LYRIC',NAME='$NAME',EDITION=EDITION+1,STATUS=1 WHERE ID=$ID";
				}else{
					$q="INSERT INTO $this->db_lyric SET 
						USER=$USER,TRACK='$TRACK',TITLE='$TITLE',ARTIST='$ARTIST',LYRIC='$LYRIC',NAME='$NAME',IP='$IP',STATUS=0";
				}
				$is=new sql($q);
				if(is_numeric($is->msg)){
					$t=stripslashes($TITLE);
					$z=array('is'=>'done','submit'=>'Posted','close'=>'Close');
					$z['msg']="Successfull posted $t's lyric...";
				}else{
					$z['msg']=$is->msg;
				}
			}else{
				$z['msg']='hum!';
			}
		}
		//return $this->lyric_get();
		return $z;
	}
	public function remove()
	{
		new sql("DELETE FROM $this->db_lyric WHERE ID=$this->obid");
		return array('msg'=>'deleted');
	}
	public function name()
	{
		$s=new sql("SELECT * FROM $this->db_lyric WHERE TRACK=$this->laid",'fetch_array');
		if($s->total){
			foreach($s->rows as $i => $e){
				$lyid="lyric-{$e['TRACK']}-{$e['ID']}";
				$j['lyric'][]=array('t'=>'li', 'd'=>array('class'=>'P zA','id'=>$lyid,'html'=>$e['NAME']));
			}
			$z['zj'][]=array('t'=>'ol','d'=>array('class'=>'lyrics','title'=>$e['DATE']),'l'=>$j['lyric']);
		}else{
			$z['zj'][]=array('t'=>'p','d'=>array('class'=>'lyrics','html'=>'No lyric available!'));
		}
		return $z;
	}
}
?>