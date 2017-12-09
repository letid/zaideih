<?
class html {
	public $element;
	public $innerHTML;
	public $attributes = array();
	private $special = array('img','input','hr','br','meta','link');
	var $tpl=array('<{element}{attr}/>','<{element}{attr}>{innerHTML}</{element}>');
	var $attr=' {k}="{v}"';
	public function __construct($element,$innerHTML=NULL,$attributes=NULL)
	{
		$this->element = self::h($element);
		if($innerHTML)self::innerHTML($innerHTML);
		if($attributes)self::attributes($attributes);
	}
	public function __toString()
	{
		return self::generate();
	}
	public function attributes($attributes)
	{
		return $this->attributes = array_merge($this->attributes, (array) $attributes);
	}
	public function innerHTML($innerHTML)
	{
		return $this->innerHTML = self::h($innerHTML);
	}
	public function output() {
		return self::generate();
	}
	private function generate() {
		$attr = NULL;
		foreach($this->attributes as $k=>$v){
			if(is_bool($v)) $v = $k;
			$attr .= ' '.$k.'="'.$v.'"';
		}
		if(in_array($this->element, $this->special)){
			return "<{$this->element}$attr/>";
		}else{
			return "<{$this->element}$attr>{$this->innerHTML}</{$this->element}>";
		}
	}
	private function preg($q) {
		return preg_replace_callback(self::$init[2],function($M){
				return $this->{$M[1]};
				},$this->tpl[$q]
			);
	}
	public static function h($d){
		if(is_array($d)){
			foreach($d as $k => $v){
				if(is_numeric($k) or $k == 'html'){
					$r[] = self::h($v);
				}else{
					$r[] = new html($k,self::h($v['html']), @$v['attr']);
				}
			}
			return implode(' ', $r);
		}else{
			return $d;
		}
	}
}
/*    
$ol 		= new html("ol", "{language}", array("id" => "ddd","class" => "class"));
$ol_li 		= new html("li", "{language}", array("class" => "class"));
$ol_li_a 	= new html("a", "{language}", array("class" => "class"));

    $input = new html("input");
    echo $input->attributes(array("name" => "test", "value" => "testing", "disabled" => true))->output();
    // <input name="test" value="testing" disabled="disabled"/>

    echo new html("a", "Link Text", array("href" => "http://www.google.com"));
    // <a href="http://www.google.com">Link Text</a>

    $html = new html("a");
    $html->innerHTML("Link Text");
    $html->attributes(array("href" => "http://www.google.com"));
    echo $html->output();
    // <a href="http://www.google.com">Link Text</a>
    
    echo $html->innerHTML("Link Text")->attributes(array("href" => "http://www.google.com"))->output();
    // <a href="http://www.google.com">Link Text</a>
    
    $html->innerHTML = "Override Text";
    echo $html->output();
    // <a href="http://www.google.com">Override Text</a>
    
    $html->attributes["href"] = "http://www.yahoo.com";
    echo $html->output();
    // <a href="http://www.yahoo.com">Override Text</a>
*/