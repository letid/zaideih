<?php 
class playlist extends api
{
	public function home()
	{
		$e=explode('-',$this->obid);
		$this->plid=isset($e[1])?$e[1]:$e[0];
		return call_user_func(array($this,$this->action));
	}
	public function get($get=NULL)
	{
		if($this->userid){
			$s=new sql("SELECT * FROM $this->db_playlist WHERE USER=$this->userid",'fetch_array');
			if($s->total){
				foreach($s->rows as $i => $r){
					$plid="playlist-{$r['ID']}";
					$tracks=json_decode($r['TRACK']);
					$tracks_count=(count($tracks)>0)?count($tracks):'0';
					$class=(array_search($this->laid, $tracks))?'yes':'no';
					$j['ol'][]=array('t'=>'li', 'd'=>array('class'=>$class,'id'=>$plid,'title'=>$tracks_count), 'l'=>array(
							array('t'=>'p', 'd'=>array('class'=>'name fn h','data-role'=>'playlist add','html'=>$r['PLAYLIST'])),
							array('t'=>'span', 'd'=>array('class'=>'des fn','data-role'=>'playlist desc','html'=>'!')),
							array('t'=>'span', 'd'=>array('class'=>'edit fn','data-role'=>'playlist edit','html'=>$this->ztd('Edit'))),
							array('t'=>'span', 'd'=>array('class'=>'delete fn','data-role'=>'playlist remove','html'=>$this->ztd('Delete'))),
							array('t'=>'span', 'd'=>array('class'=>'play fn','data-role'=>'playlist play','html'=>$this->ztd('Play'))),
							array('t'=>'p', 'd'=>array('class'=>'desc','html'=>$r['COMMENT']))
						)
					);
				}
			}else{
				$j['ol'][]=array('t'=>'li', 'd'=>array('class'=>'noplaylist','html'=>'You dont have any playlist....'));
			}
		}else{
			$j['ol'][]=array('t'=>'li', 'd'=>array('class'=>'noplaylist aut','html'=>'You dont have any playlist, please login to create one!'));
		}
		if($get)return $j[$get];
		$z['zj'][]=array('t'=>'ol', 'd'=>array('class'=>'pl'), 'l'=>$j['ol']);
		if($this->userid){
			$z['zj'][]=array('t'=>'form', 'd'=>array('method'=>'get','data-role'=>'playlist post'), 'l'=>array(
					array('t'=>'p', 'd'=>array('title'=>'Name','class'=>'ns'),
						'l'=>array(
							array('t'=>'input', 'd'=>array('type'=>'text','name'=>'name')),
							array('t'=>'span','d'=>array('class'=>'fdm fn','data-role'=>'playlist fdm','html'=>'!')),
							array('t'=>'input', 'd'=>array('type'=>'hidden','name'=>'plid')),
							array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'add','value'=>'Playlist')),
							array('t'=>'input', 'd'=>array('type'=>'button','name'=>'reset','value'=>'reset'))
						)
					),
					array('t'=>'p', 'd'=>array('title'=>'Description', 'class'=>'fdmw'),
						'l'=>array(
							array('t'=>'textarea', 'd'=>array('name'=>'desc')),
							array('t'=>'label','d'=>array('html'=>'anything to identify this playlist...'))
						)
					)
				)
			);
			/*
			$z['zj'][]=array('t'=>'form', 'd'=>array('method'=>'get','data-role'=>'playlist post'), 'l'=>array(
					array('t'=>'input', 'd'=>array('type'=>'text','name'=>'name')),
					array('t'=>'input', 'd'=>array('type'=>'hidden','name'=>'plid')),
					array('t'=>'textarea', 'd'=>array('name'=>'desc')),
					array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'add','value'=>'add')),
					array('t'=>'input', 'd'=>array('type'=>'button','name'=>'reset','value'=>'reset'))
				)
			);
			*/
		}
		return $z;
	}
	public function post()
	{
		$name=addslashes($_GET['name']);$desc=addslashes($_GET['desc']);
		if($_GET['plid'] and $name){
			$s=new sql("SELECT * FROM $this->db_playlist WHERE ID=$this->plid");
			if($s->total)new sql("UPDATE $this->db_playlist SET USER=$this->userid, PLAYLIST='$name',COMMENT='$desc' WHERE ID=$this->plid");
		}elseif($name){
			new sql("INSERT INTO $this->db_playlist SET USER=$this->userid, PLAYLIST='$name', COMMENT='$desc'");
		}
		//return $this->get();
		$z['zj']=$this->get('ol');
		return $z;
	}
	public function add()
	{
		$d=array();$t=array();
		$s=new sql("SELECT * FROM $this->db_playlist WHERE ID={$this->plid}",'fetch_this');
		if($s->total){
			$d['add']='no'; $d['remove']='yes';$t=array();
			if($s->rows['TRACK']){
				$tracks=json_decode($s->rows['TRACK'],true);
				foreach($tracks as $v)if($v != $this->laid)$t[]=$v;
				if(!in_array($this->laid,$tracks)){
					$t[]=$this->laid; $d['add']='yes'; $d['remove']='no';
				}
			}else{
				$t[]=$this->laid;
			}
			$d['total']=count($t);
			$TRACK_json_encode=json_encode($t,true);
			$u=new sql("UPDATE $this->db_playlist SET TRACK='$TRACK_json_encode' WHERE ID={$this->plid}");
		}
		return $d;
	}
	public function remove()
	{
		new sql("DELETE FROM $this->db_playlist WHERE ID={$this->plid}");
		return array('zj'=>$this->get('ol'));
	}
	public function play()
	{
		$s=new sql("SELECT * FROM $this->db_playlist WHERE ID=$this->plid",'fetch_this');
		if($s->total){
			if($s->rows['TRACK']){
				$q='ID='.implode(' OR ID=',json_decode($s->rows['TRACK']));
				$l=new sql("SELECT ID,TITLE,ARTIST FROM $this->db_track WHERE $q",'fetch_array');
				if ($l->total):
					foreach($l->rows as $i):
					//self::$data['www.api']
						$track_url=self::$data['www.api'].'/audio/play/'.$i['ID'];
						$z[]= array("id"=>$i['ID'],"title"=>$i['TITLE'],"artist"=>$i['ARTIST'],"mp3"=>$track_url);
					endforeach;
				endif;
			}
		}
		return $z;
	}
	public function album()
	{
		//$this->zrid, 7c560ed3e4b2b4b6efbff128a145e8d4
		return parent::getAlbum($this->zrid);
	}
}
?>