<?php 
class privacy extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->action));
	}
	public function all()
	{
		$page=new sql("SELECT COUNT(*) as total_rows FROM $this->db_suggestion WHERE PRIVACY IS NOT NULL",'fetch_row');
		$this->total_row=$page->rows[0];
		if($this->total_row){
			$p=new pagination($this->total_row,12,7);
			$p->base_url_path='track/all/1';
			$p->get_page();
			$start=$p->sql;
			$limit=$p->row_per_page;
			$s=new sql("SELECT S.*,T.TITLE AS TITLE FROM $this->db_track T LEFT JOIN $this->db_suggestion S ON S.CODE=T.ID OR S.CODE=T.UNIQUEID WHERE PRIVACY IS NOT NULL ORDER BY S.DATE DESC LIMIT $start, $limit");
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
	public function get()
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
				$m=$this->postSuggestion(sprintf("PRIVACY='%s'",addslashes(json_encode($d,true))));
				$msg='Zaideih will response the report as soon as possible, thank you!';
			}
			return array('msg'=>$msg);
		}
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>$msg));
		$z['zj'][]=array('t'=>'form', 'd'=>array('method'=>'get','action'=>"contribution/privacy/$this->laid/post"), 'l'=>$j['p']);
		return $z;
	}
}
?>