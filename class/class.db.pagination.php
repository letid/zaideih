<?php
class pagination extends zotune
{
	var $mainclass = "pagination";
	var $Previous = "Previous";
	var $Next = "Next";
	var $First = "First";
	var $Last = "Last";
	var $hellip_after = '&hellip;';
	var $hellip_before = '&hellip;';

	var $class_current = 'current';
	var $class_page = 'page';
	var $class_last = 'last';
	var $class_after = 'hellip after';
	var $class_before = 'hellip before';

	var $page_var = 'page'; /*page,page-*/
	var $page_method = 'get'; /*get,url*/
	var $trailing_slash = '/'; /*/,NULL*/

	var $base_url = NULL;//'http://zotune.com/';
	var $preserve_query_strings = true; /*true,false*/

	public function __construct($rt,$rp=12,$np=12)
	{
		$this->total_row = $rt;
		$this->row_per_page = $rp;
		$this->num_per_page = $np;
	}
    public function base_url()
    {
        $url = parse_url(($this->base_url == NULL ? $_SERVER['REQUEST_URI'] : $this->base_url));
        $this->base_url_path = $url['path'];
        $this->base_url_query = isset($url['query'])?$url['query']:NULL;
    }
    public function get_page()
    {
		if($this->page_method == 'url' &&
			preg_match('/\b'.preg_quote($this->page_var).'([0-9]+)\b/i',$_SERVER['REQUEST_URI'],$matches) > 0)$this->page=(int)$matches[1];
			else $this->page = @$_GET[$this->page_var];
		$this->page = ($this->page < 1)?1:$this->page;
		$this->total_page = ceil($this->total_row / $this->row_per_page);
        if($this->total_page > 0):
            if($this->page > $this->total_page):
				$this->page = $this->total_page;
			endif;
        endif;
		$this->page_previous= $this->page - 1;
		$this->page_next 	= $this->page + 1;
		$this->sql 			= $this->page_previous * $this->row_per_page;
    }
    private function get_uri($page)
    {
        if ($this->page_method == 'url'):
            if (preg_match('/\b'.$this->page_var.'([0-9]+)\b/i',$this->base_url_path,$matches) > 0) $url=str_replace('//','/',preg_replace('/\b'.$this->page_var.'([0-9]+)\b/i',$this->page_var.$page,$this->base_url_path));
				else $url = rtrim($this->base_url_path,'/').'/'.$this->page_var.$page;
            $url = rtrim($url,'/').$this->trailing_slash;
            if(!$this->preserve_query_strings)$query=implode('&',$this->base_url_query);
            	else $query = $_SERVER['QUERY_STRING'];
            return $url.($query!=NULL?'?'.$query:'');
        else:
            if (!$this->preserve_query_strings)$query=$this->base_url_query;
            	else parse_str($_SERVER['QUERY_STRING'], $query);
            if ($page > 0)$query[$this->page_var]=$page;
           		else unset($query[$this->page_var]);
            return htmlspecialchars($this->base_url_path.(!empty($query)?'?'.http_build_query($query):NULL));
        endif;
    }
	public function rendering()
	{
        if ($this->total_page <= 1) return NULL;
        if ($this->total_page > $this->num_per_page):
			if ($this->page == 1 || $this->page < 1) $r['Previous'] = array('href'=>'#','class'=>'btn previous disabled');
				else $r['Previous'] = array('href'=>$this->get_uri($this->page_previous),'class'=>'btn previous');
        endif;
        if ($this->total_page <= $this->num_per_page):
            for($i = 1; $i <= $this->total_page; $i++):
				$class=($this->page < $i)?'page pnt':'page pvt';
				if ($this->page == $i) $r[$i] = array('href'=>$this->get_uri($i),'class'=>'current');
					else $r[$i] = array('href'=>$this->get_uri($i),'class'=>$class);
            endfor;
		else:
            $adjacent = floor(($this->num_per_page - 3) / 2);
            $adjacent = ($adjacent == 0 ? 1 : $adjacent);
            $scroll_from = $this->num_per_page - $adjacent;
            $starting_page = 2;
            if ($this->page >= $scroll_from):
                $starting_page = $this->page - $adjacent;
                if ($this->total_page - $starting_page < ($this->num_per_page - 2)) $starting_page -= ($this->num_per_page - 2) - ($this->total_page - $starting_page);
				$r['First'] = array('href'=>$this->get_uri(1),'class'=>'first');
				$r['hellip_after'] = array('class'=>'hellip after');
            else:
				if ($this->page == 1 || $this->page < 1) $r[1] = array('href'=>$this->get_uri(1),'class'=>'current');
					else $r[1] = array('href'=>$this->get_uri(1),'class'=>'page pvt');
			endif;
            $ending_page = $starting_page + $this->num_per_page - 3;
            if ($ending_page > $this->total_page - 1) $ending_page = $this->total_page - 1;
            for ($i = $starting_page; $i <= $ending_page; $i++):
				$class=($this->page < $i)?'page pnt':'page pvt';
				if ($this->page == $i) $r[$i] = array('href'=>$this->get_uri($i),'class'=>'current');
					else $r[$i] = array('href'=>$this->get_uri($i),'class'=>$class);
            endfor;
            if ($this->total_page - $ending_page > 1):
				$r['hellip_before'] = array('class'=>'hellip before');
				$r['Last'] = array('href'=>$this->get_uri($this->total_page),'class'=>'last');
			else:
				$class = ($this->page == $i)?'current':'page pnt';
				$r[$this->total_page] = array('href'=>$this->get_uri($this->total_page),'class'=>$class);
			endif;
            if ($this->total_page > $this->num_per_page):
				if ($this->page == $this->total_page) $r['Next'] = array('href'=>'#','class'=>'btn next disabled');
					else $r['Next'] = array('href'=>$this->get_uri($this->page_next),'class'=>'btn next');
            endif;
        endif;
		return $r;
	}
	public function navigator()
	{
		$page_list = $this->rendering();
		if(is_array($page_list)):
			foreach($page_list as $k => $v):
				if(is_numeric($k)):
					$nav[] = new html('a',$k,$v);
				elseif($this->{$k}):
					if (isset($v['href'])) $nav[] = new html('a',$k,$v);
						else $nav[] = new html('a',$this->{$k},$v);
				endif;
			endforeach;
			return new html('div',implode($nav),array('class'=>$this->mainclass));
		endif;
	}
	public function zj()
	{
		$page_list =$this->rendering();
		if($page_list):
			foreach($page_list as $k => $v):
				$tag='a';
				$t['html']=$k;
				$v['class']='PAN zA '.$v['class'];
				if($this->{$k} and !isset($v['href'])):
					$tag='span';
					$t['html']=$this->{$k};
				endif;
				$j[]=array('t'=>'a', 'd'=>array_merge($t, $v));
			endforeach;
			return $j;
			//return array('t'=>'p', 'd'=>array('class'=>'pages'), 'l'=>$j);
			//return new html("div",implode($nav),array("class"=>$this->mainclass));
		endif;
	}
}