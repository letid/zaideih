<?
class html extends zotune {
	public $element;
	public $html;
	public $attribute;
	public $attr = array();
	private static $tag = array('img','input','hr','br','meta','link');
	private static $tpl=array('<{element}{attribute}/>','<{element}{attribute}>{html}</{element}>','{html}');
	private static $preg = '/[{](.*?)[}]/';
	public function __construct($element,$html=NULL,$attr=NULL)
	{
		if(is_array($element)){
			$this->html=self::h($element);
		}else{
			$this->element=self::h($element);
			if($html)$this->html=self::h($html);
			if($attr)self::attr($attr);
		}
	}
	public function __toString()
	{
		return self::generate();
	}
	public function attr($q)
	{
		return $this->attr = array_merge($this->attr, (array) $q);
	}
	private function generate()
	{
		array_walk($this->attr, create_function('&$v,$k','$v=is_bool($v)?$k:$v; $v=" $k=\"$v\"";'));
		$this->attribute = implode($this->attr);
		return preg_replace_callback(self::$preg,function($M){
				return $this->{$M[1]};
			},self::$tpl[$this->element?in_array($this->element, self::$tag)?0:1:2]
		);
	}
	public static function h($d)
	{
		if(is_array($d)){
			foreach($d as $k => $v){
				if(is_numeric($k) or $k == 'text') $r[] = self::h($v);
					else $r[] = new html($k,self::h($v['text']), @$v['attr']);
			}
			return implode(' ',$r);
		}else{
			return $d;
		}
	}
}