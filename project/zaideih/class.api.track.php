<?php 
class track extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->uri[2]));
	}
	public function all()
	{
		$page=new sql("SELECT COUNT(*) as total_rows FROM $this->db_suggestion WHERE CATALOG='1' AND TAG IS NOT NULL",'fetch_row');
		$this->total_row=$page->rows[0];
		if($this->total_row){
			$p=new pagination($this->total_row,12,7);
			$p->base_url_path='track/all/1';
			$p->get_page();
			$start=$p->sql;
			$limit=$p->row_per_page;
			$s=new sql("SELECT S.*,T.TITLE AS TITLE FROM $this->db_track T LEFT JOIN $this->db_suggestion S ON S.CODE=T.ID WHERE CATALOG='1' AND TAG IS NOT NULL ORDER BY S.DATE DESC LIMIT $start, $limit");
			$s->fetch_assoc('CODE');
			foreach($s->rows as $i => $v){
				$j['li'][]=array('t'=>'li', 'd'=>array('class'=>$i),
					'l'=>array(
						array('t'=>'a', 'd'=>array('href'=>self::$data['www.music'].'?laid='.$i,'html'=>$v[0]['TITLE']))
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
	public function comment()
	{
		$page=new sql("SELECT COUNT(*) as total_rows FROM $this->db_comment WHERE TRACK='$this->laid'",'fetch_row');
		$this->total_row=$page->rows[0];
		if($this->total_row){
			$p=new pagination($this->total_row,7,7);
			$p->base_url_path='track/comment/'.$this->laid;
			$p->get_page();
			$start=$p->sql;
			$limit=$p->row_per_page;
			/*
			SELECT S.*,T.TITLE AS TITLE FROM $this->db_track T LEFT JOIN $this->db_suggestion S ON S.CODE=T.ID WHERE CATALOG='1' AND TAG IS NOT NULL ORDER BY S.DATE DESC LIMIT $start, $limit
			
			SELECT C.*,T.TITLE AS TITLE FROM $this->db_track T LEFT JOIN $this->db_comment C ON C.TRACK=T.ID WHERE C.TRACK='$this->laid' ORDER BY C.DATES DESC LIMIT $start, $limit
			SELECT * FROM $this->db_comment WHERE TRACK='$this->laid' ORDER BY DATES DESC LIMIT $start, $limit
			
			*/
			$s=new sql("SELECT C.*,T.TITLE AS TITLE FROM $this->db_track T LEFT JOIN $this->db_comment C ON C.TRACK=T.ID WHERE C.TRACK='$this->laid' ORDER BY C.DATES DESC LIMIT $start, $limit");
			$s->fetch_assoc('TRACK');
			//$s->fetch_array();
			foreach($s->rows as $i => $v){
				$j['li'][]=array('t'=>'li', 'd'=>array('class'=>'cur'),
					'l'=>array(
						array('t'=>'a', 'd'=>array('html'=>$v[0]['TITLE'])),
						array('t'=>'ul', 'l'=>array_map(
							function($d){
								return array('t'=>'li', 'l'=>array(
									array('t'=>'strong', 'd'=>array('html'=>$d['NAME'])),
									array('t'=>'p', 'd'=>array('html'=>$d['COMMENT']))
								));
							},$v
							)
						)
					)
				);
			}
			if($p->zj())$j['li'][]=array('t'=>'li', 'd'=>array('class'=>'page'),'l'=>$p->zj());
			$z['zj'][]=array('t'=>'ol', 'd'=>array('class'=>'d3 co'), 'l'=>$j['li']);
		}else{
			$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'b1 msg', 'data-icon'=>'!', 'html'=>"..no comment for this track!"));
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
					'l'=>array(
						array('t'=>'strong', 'd'=>array('title'=>sprintf('%s/%s',count($n),count($name)),'html'=>$i)),
						array('t'=>'p', 'l'=>array_map(
							function($v){
								return array('t'=>'span', 'd'=>array('html'=>$v));
							}, $name
							)
						)
					)
				);
			}
		}
		if(isset($j['li'])){
			$z['zj'][]=array('t'=>'ol', 'd'=>array('class'=>'d3 dr'), 'l'=>$j['li']);
			$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'b1 msg', 'data-icon'=>'!', 'html'=>'Please be aware that this information is not posted by Zaideih, but as user suggested... Zaideih will only select and added up when it is suitable or confirmed!'));
		}else{
			$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'b1 msg', 'data-icon'=>'!', 'html'=>"..no tag suggestion for this track!"));
		}
		
		return $z;
	}
	public function editor()
	{	
		$tags=array(
			'TITLE'=>array('text'=>'Title','icon'=>'#'),
			'ARTIST'=>array('text'=>'Artist {if various artists, seperated by comma}','icon'=>'Ar'),
			'ALBUM'=>array('text'=>'Album','icon'=>'Al'),
			'TRACK'=>array('text'=>'Track (numeric)','icon'=>'#'),
			'YEAR'=>array('text'=>'Year (format) (numeric) {yyyy}','icon'=>'Yr'),
			'GENRE'=>array('text'=>'Genre','icon'=>'Gr'),
			'TYPE'=>array('text'=>'Music type (numeric)','icon'=>'M'),
			'LENGTH'=>array('text'=>'Length (format) {00:00}','icon'=>'L'),

			'COMMENT'=>array('text'=>'Description','icon'=>'*'),
			'LANG'=>array('text'=>'Language (numeric)','icon'=>'La'),
			'STATUS'=>array('text'=>'Status (numeric)','icon'=>'S')
		);
		$data=array();
		$this->trackInfo($this->laid);
		foreach($tags as $e => $i){
			$d1=isset($_GET[$e])?$_GET[$e]:NULL;
			$data[$e]=stripslashes(($d1)?$d1:$this->track->{$e});
			if($e=='COMMENT'){
				$input=array('t'=>'textarea', 'd'=>array('name'=>$e,'html'=>$data[$e]));
			}else{
				$input=array('t'=>'input', 'd'=>array('type'=>'text','name'=>$e,'value'=>$data[$e]));
			}
			$j['p'][]=array('t'=>'p', 'd'=>array('data-icon'=>$i['icon']),
				'l'=>array(
					array('t'=>'label', 'd'=>array('html'=>$i['text'])), $input
				)
			);
		}
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),
			'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Modify'))
			)
		);
		if($this->obid){
			$d=array_filter(array_map('trim',$data));
			if(count($d)>0){
				$this->trackUpdate($this->laid,get::query($d));
				$msg='Modified!';
			}
			return array('msg'=>$msg);
		}
		$msg='You have right to modify, but you acknowledged that this modification make the information total changed!';
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>$msg));
		$z['zj'][]=array('t'=>'form', 'd'=>array('method'=>'get','action'=>"track/editor/$this->laid/post"), 'l'=>$j['p']);
		//other/trackeditor
		return $z;
	}
}
?>