<?php 
class album extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->action));
	}
	public function get()
	{
		$this->getSuggestion($this->laid);
		/*
		Released, Genre, Label and Producer
		*/
		$tags=array(
			'title'=>array('text'=>'Album Title','icon'=>'At'),
			'artist'=>array('text'=>'Album Artist','icon'=>'Aa'),
			'released'=>array('text'=>'Released on {yyyy-mm-dd} or {yyyy}','icon'=>'Ar'),
			'genre'=>array('text'=>'Genre','icon'=>'Ag'),
			'label'=>array('text'=>'Label','icon'=>'Al'),
			
			'studio'=>array('text'=>'Studio','icon'=>'AS'),
			'band'=>array('text'=>'Band','icon'=>'Ab'),
			'writer'=>array('text'=>'Writer','icon'=>'Aw'),
			'copyright'=>array('text'=>'Copyright','icon'=>'Ac'),
			
			'producer'=>array('text'=>'Producer','icon'=>'Ap'),
			'description'=>array('text'=>'About','icon'=>'*')
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
		$msg='It does not meant that all the fields have to be filled, but only the fields that you acknowledge. 
		Commas should be used when various artists and more than one value of genre, label & producer. Please do not spam!';

		if($this->obid){
			$d=array_filter(array_map('trim',$data));
			if(count($d)>0){
				$m=$this->postSuggestion("TAG='".addslashes(json_encode($d,true))."'",$this->laid,3);
				$msg='Thank you for the contribution!';
			}
			return array('msg'=>$msg);
		}

		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>$msg));
		$z['zj'][]=array('t'=>'form', 'd'=>array('method'=>'get',
			'action'=>sprintf('album/get/%s/post',$this->laid)
			), 'l'=>$j['p']);
		return $z;
	}
	public function all()
	{
		$page=new sql("SELECT COUNT(*) as total_rows FROM $this->db_suggestion WHERE CATALOG='3' AND TAG IS NOT NULL",'fetch_row');
		$this->total_row=$page->rows[0];
		if($this->total_row){
			$p=new pagination($this->total_row,12,7);
			$p->base_url_path='album/all/3';
			$p->get_page();
			$start=$p->sql;
			$limit=$p->row_per_page;
			$s=new sql("SELECT S.*,T.ALBUM AS ALBUM FROM $this->db_track T LEFT JOIN $this->db_suggestion S ON S.CODE=T.UNIQUEID WHERE CATALOG='3' AND TAG IS NOT NULL ORDER BY S.DATE DESC LIMIT $start, $limit");
			$s->fetch_assoc('CODE');
			foreach($s->rows as $i => $v){
				$j['li'][]=array('t'=>'li', 'd'=>array('class'=>$i),
					'l'=>array(
						array('t'=>'a', 'd'=>array('href'=>self::$data['www.album'].'/'.$i,'html'=>$v[0]['ALBUM']))
						//array('t'=>'a', 'd'=>array('href'=>"track/get/$i", 'class'=>'CON zA','data-role'=>'ol','html'=>'detail'))
					)
				);
			}
			if($p->zj())$j['li'][]=array('t'=>'li', 'd'=>array('class'=>'page'),'l'=>$p->zj());
			$z['zj'][]=array('t'=>'ul', 'd'=>array('class'=>'fe'), 'l'=>$j['li']);
		}else{
			$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'msg', 'data-icon'=>'!', 'html'=>"..no tag suggestion for this track!"));
		}
		return $z;
	}
	public function tag()
	{
		$s=new sql("SELECT TAG FROM $this->db_suggestion WHERE CODE='$this->laid' AND TAG IS NOT NULL",'fetch_array');
		//if($s->total)foreach($s->rows as $r)foreach(json_decode($r['TAG'],true)as $i=>$n)$tag[$i][]=$n;
		
		if($s->total){
			foreach($s->rows as $r){
				foreach(json_decode($r['TAG'],true)as $i=>$n){
					$tag[$i][]=$n;
				}
			}
		}
		if(isset($tag)){
			foreach($tag as $i=>$n){
				$name=array_filter(array_unique(array_map('trim',$n)));
				$j['li'][]=array('t'=>'li', 'd'=>array('class'=>$i), 
					'l'=>array(array('t'=>'strong', 'd'=>array('title'=>sprintf('%s/%s',count($n),count($name)),'html'=>$i)),
						array('t'=>'p', 'l'=>array_map(
							function($v){
								return array('t'=>'span', 'd'=>array('html'=>$v));
							}, $name)
						)
					)
				);
			}
			$z['zj'][]=array('t'=>'ol', 'd'=>array('class'=>'d3 dr'), 'l'=>$j['li']);
			$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'b1 msg', 'data-icon'=>'!', 'html'=>'Please be aware that this information is not posted by Zaideih, but as user suggested... Zaideih will only select and added up when it is suitable or confirmed!'));
		}else{
			$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'b1 msg', 'data-icon'=>'!', 'html'=>"..no tag suggestion for this Album!"));
		}
		
		return $z;
	}
}
?>