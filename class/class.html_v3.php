<?
class html {
	public $element;
	public $html;
	public $attribute;
	public $attr = array();
	private $special = array('img','input','hr','br','meta','link');
	var $tpl=array('<{element}{attribute}/>','<{element}{attribute}>{html}</{element}>');
	var $preg = '/[{](.*?)[}]/';
	public function __construct($element,$html=NULL,$attr=NULL)
	{
		if(is_array($element)){
			return self::h($element);
		}else{
			$this->element = self::h($element);
			if($html)self::innerHTML($html);
			if($attr)self::attributes($attr);
		}
	}
	public function attributes($attr)
	{
		return $this->attr = array_merge($this->attr, (array) $attr);
	}
	public function innerHTML($html)
	{
		return $this->html = self::h($html);
	}
	public function output()
	{
		return self::generate();
	}
	private function generate()
	{
		array_walk($this->attr, create_function('&$v,$k','$v=is_bool($v)?$k:$v; $v=" $k=\"$v\"";'));
		$this->attribute = implode($this->attr);
		return preg_replace_callback($this->preg,function($M){
				return $this->{$M[1]};
			},$this->tpl[in_array($this->element, $this->special)?0:1]
		);
	}
	public static function h($d)
	{
		if(is_array($d)){
			foreach($d as $k => $v){
				if(is_numeric($k) or $k == 'html'){
					$r[] = self::h($v);
				}else{
					$r[] = new html($k,self::h($v['html']), @$v['attr']);
				}
			}
			return implode(' ',$r);
		}else{
			return $d;
		}
	}
	public function __toString()
	{
		return self::generate();
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