<?php
class sitemap extends zotune
{
	var $http='http:', $zxe='-';
	public function home()
	{
		$v=explode($this->zxe,$this->uri[1]);
		if(method_exists($this,$v[0])){
			$this->page=$v[0];
		}else{
			$this->page='track';
		}
		$this->head();
		call_user_func(array($this,$this->page));
	}
	private function head()
	{
		header('Content-Type: application/xml; charset=utf-8');
	}
	private function pagination($table,$where,$order=NULL,$is=false,$rowperpage=14,$numperpage=11,$s='*')
	{
		$sql = new sql(sprintf('SELECT COUNT(*) as total_rows FROM %s %s',$table,$where),'fetch_row');
		$this->total_row=($is)?$sql->rows[0]:$sql->total;
		if ($this->total_row){
			$p=new pagination($this->total_row,$rowperpage,$numperpage);
			$p->page_var = $this->page.$this->zxe;
			$p->page_method = 'url';
			$p->get_page();
			return sprintf('SELECT %s FROM %s %s %s LIMIT %s, %s',$s,$table,$where,$order,$p->sql,$p->row_per_page);
		}
	}
	public function track()
	{
		$query=$this->pagination(self::$db['track'],NULL,NULL,true,100);
		$s=new sql($query,'fetch_array');
		foreach($s->rows as $d){
			$url_album 			= rawurlencode($d['ALBUM']);
			$url_artist 		= rawurlencode($d['ARTIST']);
			$url_title	 		= rawurlencode($d['TITLE']);
			$d['loc'] 	= $this->http.self::$data['www.music'].'/'.$url_album.'/'.$url_artist.'/'.$url_title;
			$this->ztf('list',true,$d);
		}
		$this->ztf('sitemap','page.data');
	}
	public function album()
	{
		$query=$this->pagination(self::$db['track'],'GROUP BY ALBUM',NULL,false,100);
		$s=new sql($query,'fetch_array');
		foreach($s->rows as $d){
			$d['loc'] 	= $this->http.self::$data['www.album'].'/'.rawurlencode($d['ALBUM']);
			$this->ztf('list',true,$d);
		}
		$this->ztf('sitemap','page.data');
	}
	public function artist()
	{
		$query=$this->pagination(self::$db['track'],'GROUP BY ARTIST',NULL,false,100);
		$s=new sql($query,'fetch_array');
		foreach($s->rows as $d){
			$d['loc'] 	= $this->http.self::$data['www.artist'].'/'.rawurlencode($d['ARTIST']);
			$this->ztf('list',true,$d);
		}
		$this->ztf('sitemap','page.data');
	}
}