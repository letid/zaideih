<?php 
class artist extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->action));
	}
	public function all()
	{
		$page=new sql("SELECT COUNT(*) as total_rows FROM $this->db_suggestion WHERE CATALOG='2' AND TAG IS NOT NULL",'fetch_row');
		$this->total_row=$page->rows[0];
		if($this->total_row){
			$p=new pagination($this->total_row,12,7);
			$p->base_url_path='artist/all/2';
			$p->get_page();
			$start=$p->sql;
			$limit=$p->row_per_page;
			$s=new sql("SELECT * FROM $this->db_suggestion WHERE CATALOG='2' AND TAG IS NOT NULL ORDER BY DATE DESC LIMIT $start, $limit");
			$s->fetch_assoc('CODE');
			foreach($s->rows as $i => $v){
				$j['li'][]=array('t'=>'li', 'd'=>array('class'=>$i),
					'l'=>array(
						array('t'=>'a', 'd'=>array('href'=>self::$data['www.artist'].'/'.rawurlencode($i),'html'=>$v[0]['CODE']))
						//array('t'=>'a', 'd'=>array('href'=>"track/get/$i", 'class'=>'CON zA','data-role'=>'ol','html'=>'detail'))
					)
				);
			}
			if($p->zj())$j['li'][]=array('t'=>'li', 'd'=>array('class'=>'page'),'l'=>$p->zj());
			$z['zj'][]=array('t'=>'ul', 'd'=>array('class'=>'fe'), 'l'=>$j['li']);
		}else{
			$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'msg', 'html'=>"..no tag suggestion for this track!"));
		}
		return $z;
	}
	public function get()
	{
		$artist= rawurldecode($this->obid);
		if(strpos($artist,',')){
			$msg='yes';
			$j['li']=array_map(function($v){
				return array('t'=>'li', 'l'=>array(
						array('t'=>'a', 
							'd'=>array(
							'href'=>sprintf('artist/get/%s/%s',$this->laid,rawurlencode(trim($v))),
							'class'=>'CON zA', 
							'html'=>"about $v?"
							)
						)
					));
				},explode(',',$artist)
			);
			$z['zj'][]=array('t'=>'ul', 'd'=>array('class'=>'d3 ds'), 'l'=>$j['li']);
		}else{
			$z=$this->form(trim($artist));
		}
		return $z;
	}
	public function post()
	{
		$this->submit=true;
		return $this->form(rawurldecode($this->obid));
	}
	public function form($artist)
	{
		$this->getSuggestion(addslashes($artist));
		$tags=array(
			'fullname'=>array('text'=>'Fullname','icon'=>'f'),
			'stagename'=>array('text'=>'Stage name / Known as','icon'=>'s'),
			'dob'=>array('text'=>'Date of birth {yyyy-mm-dd}','icon'=>'d'),
			'gender'=>array('text'=>'Gender (Male/Female)','icon'=>'g'),
			'nationality'=>array('text'=>'Nationality','icon'=>'n'),
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
		$msg="It does not meant that all the fields have to be filled, but only the fields that you acknowledge about '{$artist}'. Please do not spam us!";

		if(isset($this->submit)){
			$d=array_filter(array_map('trim',$data));
			if(count($d)>0){
				$m=$this->postSuggestion("TAG='".addslashes(json_encode($d,true))."'",$artist,2);
				$msg='Thank you for the contribution!';
			}
			return array('msg'=>$msg);
		}

		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>$msg));
		$z['zj'][]=array('t'=>'form', 'd'=>array('method'=>'get',
			'action'=>sprintf('artist/post/%s/%s',$this->laid,$this->obid)
			), 'l'=>$j['p']);
		return $z;
	}
	public function tag()
	{
		$artist= rawurldecode($this->laid);
		$artists=array_filter(array_map('trim',explode(',',$artist)));
		$j['li']=array_map(function($v){
			return array('t'=>'li', 'd'=>array('class'=>'cur'),'l'=>array(
					array('t'=>'a', 
						'd'=>array('href'=>sprintf('artist/tag/%s',rawurlencode($v)), 'class'=>'CON zA', 'html'=>$v)
					),
					$this->tag_extend($v)
				));
			},$artists
		);
		$z['zj'][]=array('t'=>'ol', 'd'=>array('class'=>'d3 as'), 'l'=>$j['li']);
		if($this->artist_tag){
			$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'b1 msg', 'data-icon'=>'!', 'html'=>'Please be aware that this information is not posted by Zaideih, but as user suggested... Zaideih will only select and added up when it is suitable or confirmed!'));
		}
		/*
		if(strpos($artist,',')){
			$artists=array_filter(array_map('trim',explode(',',$artist)));
			$j['li']=array_map(function($v){
				return array('t'=>'li', 'd'=>array('class'=>'cur'),'l'=>array(
						array('t'=>'a', 
							'd'=>array('href'=>sprintf('artist/tag/%s',rawurlencode($v)), 'class'=>'CON zA', 'html'=>$v)
						),
						$this->tag_extend($v)
					));
				},$artists
			);
			$z['zj'][]=array('t'=>'ol', 'd'=>array('class'=>'d3 ds'), 'l'=>$j['li']);
		}else{
			$z['zj'][]=$this->tag_extend(trim($artist));
		}
		*/
		return $z;
	}
	private function tag_extend($artist)
	{
		$s=new sql("SELECT TAG FROM $this->db_suggestion WHERE CODE='$artist' AND TAG IS NOT NULL",'fetch_array');
		//if($s->total)foreach($s->rows as $r)foreach(json_decode($r['TAG'],true)as $i=>$n)$tag[$i][]=$n;
		
		if($s->total){
			foreach($s->rows as $r){
				foreach(json_decode($r['TAG'],true)as $i=>$n){
					$tag[$i][]=$n;
				}
			}
		}
		if(isset($tag)){
			$this->artist_tag=$s->total;
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
			$z=array('t'=>'ul', 'l'=>$j['li']);
		}else{
			$z=array('t'=>'p', 'd'=>array('html'=>"..no tag suggestion found for {$artist}!"));
		}
		
		return $z;
	}
}
?>