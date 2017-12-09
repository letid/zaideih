<?php 
class contribution extends api
{
	public function home()
	{
		$this->getSuggestion($this->laid);
		return call_user_func(array($this,$this->action));
	}
	private function listGen($q,$cur)
	{
		return array_map(function(&$v,$k,$cur) use ($cur){return array('t'=>'li', 
			'd'=>array('data-role'=>sprintf('li %s', $k), 'html'=>$v, 'class'=>sprintf('COP zA %s', ($cur == $k)?'cur':'')));
			},$q, array_keys($q)
		);
	}
	public function genre()
	{
		if($this->obid)return $this->postSuggestion("GENRE=$this->obid",$this->laid);
		$z['zj'][]=array('t'=>'ul','d'=>array('class'=>'b'),'l'=>$this->listGen($this->genreList,$this->GENRE));
		return $z;
	}
	public function audio()
	{
		if($this->obid)return $this->postSuggestion("AUDIO=$this->obid",$this->laid);
		$z['zj'][]=array('t'=>'ul','d'=>array('class'=>'b'),'l'=>$this->listGen($this->statusList,$this->AUDIO));
		return $z;
	}
	public function christian()
	{
		if($this->obid)return $this->postSuggestion("CHRISTIAN=$this->obid",$this->laid);
		$z['zj'][]=array('t'=>'ul','d'=>array('class'=>'b'),'l'=>$this->listGen($this->genreChristian,$this->CHRISTIAN));
		return $z;
	}
	public function zola()
	{
		if($this->obid)return $this->postSuggestion("ZOLANAM=$this->obid",$this->laid);
		$j['ul']=$this->listGen($this->zolaNam ,$this->ZOLANAM);
		$j['ul'][]=array('t'=>'p', 'd'=>array('text'=>'..hi bang in nong huh sawm na pen lam dang sa kei ung, bang hang hiam cih leh Zomi te khat leh khat a ki huh diam diam pen i ngei na khat hi...'),
			'l'=>array(
				array('t'=>'strong', 'd'=>array('html'=>'..this section might not be relevant for everyone, unless its understood the written... if so please feel free to skip...'))
			)
		);
		$z['zj'][]=array('t'=>'ul', 'd'=>array('class'=>'b'), 'l'=>$j['ul']);
		return $z;
	}
	public function tag()
	{
		$tags=array(
			'title'=>array('text'=>'Title','icon'=>'#'),
			'artist'=>array('text'=>'Artist {if various artists, seperated by comma}','icon'=>'Ar'),
			'album'=>array('text'=>'Album','icon'=>'Al'),
			'writer'=>array('text'=>'Writer','icon'=>'@'),
			'year'=>array('text'=>'Year {yyyy}','icon'=>'Yr'),
			'genre'=>array('text'=>'Genre','icon'=>'Gr'),
			'website'=>array('text'=>'Website{URL}','icon'=>'Ws'),
			'studio'=>array('text'=>'Studio','icon'=>'®'),
			'band'=>array('text'=>'Band','icon'=>'™'),
			'copyright'=>array('text'=>'Copyright/Owner','icon'=>'©'),
			'publisher'=>array('text'=>'Publisher','icon'=>'-'),
			'mixing'=>array('text'=>'Mixing','icon'=>'Mx'),
			'description'=>array('text'=>'Description','icon'=>'*')
		);
		$data=array();
		foreach($tags as $e => $i){
			$d1=isset($_GET[$e])?($_GET[$e])?$_GET[$e]:' ':'';
			$d2=isset($this->TAG[$e])?$this->TAG[$e]:'';
			$data[$e]=stripslashes(($d1)?$d1:$d2);
			if($e=='description'){
				$input=array('t'=>'textarea', 'd'=>array('name'=>$e,'html'=>$data[$e]));
			}else{
				$input=array('t'=>'input', 'd'=>array('type'=>'text','name'=>$e,'value'=>$data[$e]));
			}
			$j['p'][]=array('t'=>'p', 'd'=>array('title'=>$i['icon']), 
				'l'=>array(
					array('t'=>'label', 'd'=>array('html'=>$i['text'])), $input
				)
			);
		}
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),
			'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Post'))
			)
		);
		$msg='It does not meant that all the fields have to be filled, but only the fields that you acknowledge. Please do not spam!';
		if($this->obid){
			$d=array_filter(array_map('trim',$data));
			if(count($d)>0){
				$m=$this->postSuggestion("TAG='".addslashes(json_encode($d,true))."'",$this->laid);
				$msg='Thank you for the contribution!';
			}
			return array('msg'=>$msg);
		}
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>$msg));
		$z['zj'][]=array('t'=>'form', 'd'=>array('method'=>'get','action'=>"contribution/tag/$this->laid/post"), 'l'=>$j['p']);
		return $z;
	}
	public function privacy()
	{
		$tags=array(
			'name'=>array('text'=>'Name','icon'=>'N'),
			'email'=>array('text'=>'E-mail','icon'=>'E'),
			'phone'=>array('text'=>'Phone/Mobile','icon'=>'P'),
			'description'=>array('text'=>'Description','icon'=>'*')
		);
		$data=array();
		foreach($tags as $e => $i){
			$d1=isset($_GET[$e])?$_GET[$e]:'';
			$d2=isset($this->PRIVACY[$e])?$this->PRIVACY[$e]:'';

			$data[$e]=stripslashes(($d1)?$d1:$d2);
			if($e=='description'){
				$input=array('t'=>'textarea', 'd'=>array('name'=>$e,'html'=>$data[$e]));
			}else{
				$input=array('t'=>'input', 'd'=>array('type'=>'text','name'=>$e,'value'=>$data[$e]));
			}
			$j['p'][]=array('t'=>'p', 'd'=>array('title'=>$i['icon']),
				'l'=>array(
					array('t'=>'label', 'd'=>array('html'=>$i['text'])), $input
				)
			);
		}
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),
			'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Submit'))
			)
		);
		$msg=isset($this->PRIVACY['msg'])?$this->PRIVACY['msg']:' ';
		if($this->obid){
			$d=array_filter(array_map('trim',$data));
			//$d = array_filter($data);
			if(count($d)>0){
				$m=$this->postSuggestion(sprintf("PRIVACY='%s'",addslashes(json_encode($d,true))),$this->laid);
				$msg='Zaideih will response the report as soon as possible, thank you!';
			}
			return array('msg'=>$msg);
		}
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>$msg));
		$z['zj'][]=array('t'=>'form', 'd'=>array('method'=>'get','action'=>"contribution/privacy/$this->laid/post"), 'l'=>$j['p']);
		return $z;
	}
	public function gt()
	{
		$s=new sql("SELECT GENRE, count(GENRE) AS person FROM $this->db_suggestion WHERE CODE='$this->laid' GROUP BY GENRE ORDER BY person DESC",'fetch_array');
		if ($s->total){
			foreach($s->rows as $i => $r){
				if(isset($this->genreList[$r['GENRE']])){
					$suggestion=$this->genreList[$r['GENRE']];
					$person =$r['person'];
					$is=($person>1)?'people':'person';
					$j['ul'][]=array('t'=>'li', 'd'=>array('html'=>"...$person $is suggested as <strong>$suggestion</strong>..."));
					
				}
			}
		}
		if(!isset($j['ul'])){
			$j['ul'][]=array('t'=>'li', 'd'=>array('class'=>'no','html'=>"Sorry there is no user suggestion for this track!"));
		}
		$z['zj'][]=array('t'=>'ul', 'l'=>$j['ul']);
		return $z;
	}
	public function ct()
	{
		$s=new sql("SELECT CHRISTIAN, count(CHRISTIAN) AS person FROM $this->db_suggestion WHERE CODE='$this->laid' GROUP BY CHRISTIAN ORDER BY person DESC",'fetch_array');
		if ($s->total){
			foreach($s->rows as $i => $r){
				if(isset($this->genreChristian[$r['CHRISTIAN']])){
					$suggestion=$this->genreChristian[$r['CHRISTIAN']];
					$person =$r['person'];
					$is=($person>1)?'people':'person';
					$j['ul'][]=array('t'=>'li', 'd'=>array('html'=>"...$person $is suggested as <strong>$suggestion</strong>..."));
				}
			}
		}
		if(!isset($j['ul'])){
			$j['ul'][]=array('t'=>'li', 'd'=>array('class'=>'no','html'=>"Sorry there is no user suggestion for this track!"));
		}
		$z['zj'][]=array('t'=>'ul', 'l'=>$j['ul']);
		return $z;
	}
	public function at()
	{
		$s=new sql("SELECT AUDIO, count(AUDIO) AS person FROM $this->db_suggestion WHERE CODE='$this->laid' GROUP BY AUDIO ORDER BY person DESC",'fetch_array');
		if ($s->total){
			foreach($s->rows as $i => $r){
				if(isset($this->statusList[$r['AUDIO']])){
					$suggestion=$this->statusList[$r['AUDIO']];
					$person =$r['person'];
					$is=($person>1)?'people':'person';
					$j['ul'][]=array('t'=>'li', 'd'=>array('html'=>"...$person $is said that this track <strong>$suggestion</strong>..."));
				}
			}
		}
		if(!isset($j['ul'])){
			$j['ul'][]=array('t'=>'li', 'd'=>array('class'=>'no','html'=>"So far, there is no Audio quality report found..."));
		}
		$z['zj'][]=array('t'=>'ul', 'l'=>$j['ul']);
		return $z;
	}
	public function zo()
	{
		$s=new sql("SELECT ZOLANAM, count(ZOLANAM) AS person FROM $this->db_suggestion WHERE CODE='$this->laid' GROUP BY ZOLANAM ORDER BY person DESC",'fetch_array');
		if ($s->total){
			foreach($s->rows as $i => $r){
				if(isset($this->zolaNam[$r['ZOLANAM']])){
					$suggestion=$this->zolaNam[$r['ZOLANAM']];
					$person =$r['person'];
					$is=($person>1)?'people':'person';
					$j['ul'][]=array('t'=>'li', 'd'=>array('html'=>"...$person $is said that this track is <strong>$suggestion</strong>..."));
				}
			}
		}
		if(!isset($j['ul'])){
			$j['ul'][]=array('t'=>'li', 'd'=>array('class'=>'no','html'=>"Sorry there is no user suggestion for this track!"));
		}
		$z['zj'][]=array('t'=>'ul', 'l'=>$j['ul']);
		return $z;
	}
}
?>