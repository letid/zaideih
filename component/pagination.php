<?php
namespace app\component
{
  class pagination
  {
		public $classMain = "pagination";
		public $classCurrent = 'current';
		public $classPage = 'page';
		public $classLast = 'last';
		public $classAfter = 'hellip after';
		public $classBefore = 'hellip before';

		public $textPrevious = "Previous";
		public $textNext = "Next";
		public $textFirst = "First";
		public $textLast = "Last";
		public $hellipAfter = '&hellip;';
		public $hellipBefore = '&hellip;';


		public $pageVar = 'page'; /*page,page-*/
		public $pageMethod = 'get'; /*get,url*/
		private $pageCurrent = 0;
		public $trailing_slash = '/'; /*/,NULL*/

		public $base_url = null;//'http://zotune.com/';
		public $preserve_query_strings = true; /*true,false*/
		public function __construct($rt,$rp=12,$np=12)
		{
			$this->totalRow = $rt;
			$this->row_per_page = $rp;
			$this->num_per_page = $np;
		}
		private function base_url()
		{
				$url = parse_url(($this->base_url == null ? $_SERVER['REQUEST_URI'] : $this->base_url));
				$this->base_url_path = $url['path'];
				$this->base_url_query = isset($url['query'])?$url['query']:null;
		}
		public function page()
		{
			if($this->pageMethod == 'url' && preg_match('/\b'.preg_quote($this->pageVar).'([0-9]+)\b/i',$_SERVER['REQUEST_URI'],$matches) > 0) {
				$this->pageCurrent=(int)$matches[1];
			} elseif (isset($_GET[$this->pageVar])) {
				$this->pageCurrent = $_GET[$this->pageVar];
			}
			$this->pageCurrent = ($this->pageCurrent < 1)?1:$this->pageCurrent;
			$this->totalPage = ceil($this->totalRow / $this->row_per_page);
			if($this->totalPage > 0) if($this->pageCurrent > $this->totalPage) $this->pageCurrent = $this->totalPage;
			$this->pagePrevious= $this->pageCurrent - 1;
			$this->pageNext = $this->pageCurrent + 1;
			$this->currentOffset = $this->pagePrevious * $this->row_per_page;
		}
		private function get_uri($page)
		{
				if ($this->pageMethod == 'url'):
						if (preg_match('/\b'.$this->pageVar.'([0-9]+)\b/i',$this->base_url_path,$matches) > 0) $url=str_replace('//','/',preg_replace('/\b'.$this->pageVar.'([0-9]+)\b/i',$this->pageVar.$page,$this->base_url_path));
				else $url = rtrim($this->base_url_path,'/').'/'.$this->pageVar.$page;
						$url = rtrim($url,'/').$this->trailing_slash;
						if(!$this->preserve_query_strings)$query=implode('&',$this->base_url_query);
							else $query = $_SERVER['QUERY_STRING'];
						return $url.($query!=NULL?'?'.$query:'');
				else:
						if (!$this->preserve_query_strings)$query=$this->base_url_query;
							else parse_str($_SERVER['QUERY_STRING'], $query);
						if ($page > 0)$query[$this->pageVar]=$page;
							else unset($query[$this->pageVar]);
						return htmlspecialchars($this->base_url_path.(!empty($query)?'?'.http_build_query($query):NULL));
				endif;
		}
		private function rendering()
		{
			$this->base_url();
			if ($this->totalPage <= 1) return null;
			if ($this->totalPage > $this->num_per_page) {
				if ($this->pageCurrent == 1 || $this->pageCurrent < 1) {
					$r[]=array(
						'text'=>$this->textPrevious,
						'attr'=>array('href'=>'#','class'=>'btn previous disabled')
					);
				} else {
					$r[]=array(
						'text'=>$this->textPrevious,
						'attr'=>array('href'=>$this->get_uri($this->pagePrevious),'class'=>'btn previous')
					);
				}
			}
			if ($this->totalPage <= $this->num_per_page) {
				for($i = 1; $i <= $this->totalPage; $i++) {
					$class=($this->pageCurrent < $i)?'page pnt':'page pvt';
					if ($this->pageCurrent == $i) {
						$class = 'current';
					}
					$r[]=array(
						'text'=>$i,
						'attr'=>array('href'=>$this->get_uri($i),'class'=>$class)
					);
				}
			} else {
				$adjacent = floor(($this->num_per_page - 3) / 2);
				$adjacent = ($adjacent == 0 ? 1 : $adjacent);
				$scroll_from = $this->num_per_page - $adjacent;
				$starting_page = 2;

				if ($this->pageCurrent >= $scroll_from) {
					$starting_page = $this->pageCurrent - $adjacent;
					if ($this->totalPage - $starting_page < ($this->num_per_page - 2)) {
						$starting_page -= ($this->num_per_page - 2) - ($this->totalPage - $starting_page);
					}

					$r[]=array(
						'text'=>$this->hellipAfter,
						'attr'=>array('class'=>'hellip after')
					);
					$r[]=array(
						'text'=>$this->textFirst,
						'attr'=>array('href'=>$this->get_uri(1),'class'=>'first')
					);

				} else {
					$class=($this->pageCurrent == 1 || $this->pageCurrent < 1)?'current':'page pvt';
					$r[]=array(
						'text'=>1,
						'attr'=>array('href'=>$this->get_uri(1),'class'=>$class)
					);
				}

				$ending_page = $starting_page + $this->num_per_page - 3;
				if ($ending_page > $this->totalPage - 1) $ending_page = $this->totalPage - 1;
				for ($i = $starting_page; $i <= $ending_page; $i++) {
					$class=($this->pageCurrent < $i)?'page pnt':'page pvt';
					if ($this->pageCurrent == $i) {
						$class = 'current';
					}
					$r[]=array(
						'text'=>$i,
						'attr'=>array('href'=>$this->get_uri($i),'class'=>$class)
					);
				}

				if ($this->totalPage - $ending_page > 1) {
					$r[]=array(
						'text'=>$this->hellipBefore,
						'attr'=>array('class'=>'hellip before')
					);
					$r[]=array(
						'text'=>$this->textLast,
						'attr'=>array('href'=>$this->get_uri($this->totalPage),'class'=>'last')
					);
				} else {
					$class = ($this->pageCurrent == $i)?'current':'page pnt';
					$r[]=array(
						'text'=>$this->totalPage,
						'attr'=>array('href'=>$this->get_uri($this->totalPage),'class'=>$class)
					);
				}

				if ($this->totalPage > $this->num_per_page) {
					if ($this->pageCurrent == $this->totalPage) {
						$r[]=array(
							'text'=>$this->textNext,
							'attr'=>array('href'=>'#','class'=>'btn next disabled')
						);
					} else {
						$r[]=array(
							'text'=>$this->textNext,
							'attr'=>array('href'=>$this->get_uri($this->pageNext),'class'=>'btn next')
						);
					}
				}
			}
			return $r;
		}
		public function navigator($Id='page.pagination')
		{
      if ($map=$this->rendering()) {
        \app\avail::content($Id)->set(
          \app\avail::html(
            array(
              'div'=>array(
                'text'=>array_map(function($j) {
                  return array(isset($j['attr']['href'])?'a':'span'=>$j);
                },$map),
                'attr'=>array(
                  'class'=>$this->classMain
                )
              )
            )
          )
        );
      }
		}
		// TODO: javascript
		private function zj()
		{
			// $page =$this->rendering();
			// if($page):
			// 	foreach($page as $k => $v):
			// 		$tag='a';
			// 		$t['html']=$k;
			// 		$v['class']='PAN zA '.$v['class'];
			// 		if($this->{$k} and !isset($v['href'])):
			// 			$tag='span';
			// 			$t['html']=$this->{$k};
			// 		endif;
			// 		$j[]=array('t'=>'a', 'd'=>array_merge($t, $v));
			// 	endforeach;
			// 	return $j;
			// 	//return array('t'=>'p', 'd'=>array('class'=>'pages'), 'l'=>$j);
			// 	//return new html("div",implode($nav),array("class"=>$this->classMain));
			// endif;
		}
	}
}