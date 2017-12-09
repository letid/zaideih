<?php
    /**
     * html
     *
     * @copyright Copyright 2010 (c) Jared Clarke @ Pixaweb.co.uk
     * @author Jared Clarke <jared@pixaweb.co.uk>
     * @version 0.1
     */
class html {
	public $element;
	public $innerHTML;
	public $attributes = array();
	private $special = array("img", "input", "hr", "br", "meta", "link");
	public function __construct($element, $innerHTML = NULL, $attributes = NULL) {
		$this->element = $element;
		if(!is_null($innerHTML)) $this->innerHTML($innerHTML);
		if(!is_null($attributes)) $this->attributes($attributes);
	}
	public function __toString() {
		return $this->generate();
	}
	public function attributes($attributes) {
		$this->attributes = array_merge($this->attributes, (array) $attributes);
		return $this;
	}
	public function innerHTML($innerHTML) {
		$this->innerHTML = $innerHTML;
		return $this;
	}
	public function output() {
		return $this->generate();
	}
	private function generate() {
		$html = "<{$this->element}";
		if(!empty($this->attributes)):
			foreach($this->attributes as $key => $value):
				// allow boolean array("disabled" => true);
				if(is_bool($value)):
					// most browsers support <.. disabled OR disabled="disabled" />
					if(!$value) continue;
					$value = $key;
				endif;
				$html .= ' '. $key .'="'. $value .'"';
			endforeach;
		endif;

		if(in_array($this->element, $this->special)):
			$html .= "/>";
			return $html;
		endif;
		$html .= ">{$this->innerHTML}</{$this->element}>";
		return $html;
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