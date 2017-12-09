<?php
class is
{
	static function ajax(){
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
	}
	static function appengine(){
		if(isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'],'Google App Engine') !== false) return true;
	}
	static function valid_email($email){
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
	}
	static function valid_url($url){
		if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) return true;
	}
	static function valid_date($date,$check_year=NULL,$format='YYYY-MM-DD'){
		if(strlen($date) >= 8 && strlen($date) <= 10){
			$separator_only = str_replace(array('M','D','Y'),'', $format);
			$separator = $separator_only[0];
			if($separator){
				$regexp = str_replace($separator, "\\" . $separator, $format);
				$regexp = str_replace('MM', '(0[1-9]|1[0-2])', $regexp);
				$regexp = str_replace('M', '(0?[1-9]|1[0-2])', $regexp);
				$regexp = str_replace('DD', '(0[1-9]|[1-2][0-9]|3[0-1])', $regexp);
				$regexp = str_replace('D', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
				$regexp = str_replace('YYYY', '\d{4}', $regexp);
				$regexp = str_replace('YY', '\d{2}', $regexp);
				if($regexp != $date && preg_match('/'.$regexp.'$/', $date)){
					foreach(array_combine(explode($separator,$format), explode($separator,$date)) as $key=>$value){
						if($key == 'YY') $year = '20'.$value;
						if($key == 'YYYY') $year = $value;
						if($key[0] == 'M') $month = $value;
						if($key[0] == 'D') $day = $value;
					}
					if(isset($check_year)){
						if($check_year >= $year){
							if(checkdate($month,$day,$year)) return true;
						}else{
							return false;
						}
					}else{
						if(checkdate($month,$day,$year)) return true;
					}
				}
			}
		}
		return false;
	}
}
class str
{
	static function extract_domain($url){
		if(preg_match('/^((.+)\.)?([A-Za-z][0-9A-Za-z\-]{1,63})\.([A-Za-z]{3})(\/.*)?$/',$url,$matches)) return $matches[3].'.'.$matches[4];
	}
	static function scramble_word($word){
		if (strlen($word) < 2)
			return $word;
		else
			return $word{0} . str_shuffle(substr($word, 1, -1)) . $word{strlen($word) - 1};
	}
	static function limit_char($str,$num,$tail=NULL){
		if(strlen($str) > $num) {
			$str_limit = substr($str, 0, $num);
			return $str_limit;//$str_limit.($tail)?$tail:NULL;
		} else {
			return $str;
		}
	}
}
class get
{
	static function files($f,$require=false)
	{
		if(file_exists($f)):
			if($require===true)require_once $f;
			return $f;
		endif;
		//return file_exists($f)?$f:NULL;
	}
	static function new_pwd(){
		$chars = "abchefghjkmnpqrstuvwxyz0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pwd = NULL;
		while ($i <= 9):
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pwd = $pwd . $tmp;
			$i++;
		endwhile;
		return $pwd;
	}
	static function sentence($q,$l=_and,$s=','){
		return implode(" $l ", array_filter(array_reverse(array_merge(array(array_pop($q)), array(implode("$s ",$q))))));
	}
	static function query($q){
		return implode(', ',array_map(function($v,$k){return sprintf("%s='%s'", $k, addslashes($v));},$q,array_keys($q)));
	}
	static function size($size)
	{
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	static function browser()
	{
		$agent=$_SERVER['HTTP_USER_AGENT'];
		if(strpos($agent, 'MSIE') !== FALSE) return 'ie';
		 elseif(strpos($agent, 'Trident') !== FALSE) return 'ie';//For Supporting IE 11
		 elseif(strpos($agent, 'Chrome') !== FALSE) return 'chrome';
		 elseif(strpos($agent, 'Firefox') !== FALSE) return 'firefox';//Mozilla Firefox
		 elseif(strpos($agent, 'Opera Mini') !== FALSE)return 'OperaMini';
		 elseif(strpos($agent, 'Opera') !== FALSE) return 'Opera';
		 elseif(strpos($agent, 'Safari') !== FALSE) return 'safari';
			 else return 'other';
	}
    static function device() {
        $Agent=strtolower($_SERVER['HTTP_USER_AGENT']);
        if(preg_match('/chrome/', $Agent)) $browser='chrome';
			elseif(preg_match('/firefox/', $Agent)) $browser='firefox';
			elseif(preg_match('/trident/', $Agent)) $browser='ie';
			elseif(preg_match('/safari/', $Agent)) $browser='safari';
			elseif(preg_match('/msie/', $Agent) )$browser ='ie';
			elseif(preg_match('/webkit/', $Agent)) $browser='safari';
			//elseif(preg_match('/mozilla/', $Agent) && !preg_match('/compatible/', $Agent)) $browser='firefox';
			elseif(preg_match('/opera/', $Agent)) $browser='opera';
				else $browser='unrecognized';
        if(preg_match('/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/', $Agent, $matches)) $version = $matches[1];
        	else $version = 'unknown';
        if(preg_match('/linux/', $Agent)) $platform='linux';
			elseif(preg_match('/macintosh|mac os x/', $Agent)) $platform='mac';
			elseif(preg_match('/windows|win32/', $Agent)) $platform='windows';
				else $platform='unrecognized';
        //return array('browser'=>$browser, 'version'=>$version, 'platform'=>$platform);
		return array($browser,$version,$platform);
    }
/*

*/
}
class array_multi
{
	static function key_exists($needle, $haystack,$opt=NULL) {
		foreach($haystack as $key => $value):
			if($needle == $key)
				return ($opt==1)?NULL:$value;
			if(is_array($value)):
				 if(self::key_exists($needle,$value ) == true)
					return ($opt==2)?$value[$needle]:NULL;
				 else
					 continue;
			endif;
		endforeach;
		return false;
	}
	static function page_search($needle, $haystack,$menu='menu') {
		foreach($haystack as $key => $value):
			if($needle == $key and $haystack[$menu]):
				return $needle;
			elseif(is_array($value) and self::page_search($needle,$value)):
				 if(self::page_search($needle,$value)) return $value[$needle];
				 	else continue;
			endif;
		/*
			if($needle == $key and $haystack[$menu])
				return ($opt==1)?NULL:$value;
			if(is_array($value)):
				 if(self::page_search($needle,$value) == true)
					return ($opt==2)?$value[$needle]:NULL;
				 else
					 continue;
			endif;
			*/
		endforeach;
		return false;
	}
	static function value_exists($needle, $haystack,$opt=NULL) {
		foreach($haystack as $key => $value):
			if($needle == $value)
				return ($opt==1)?NULL:$key;
			if(is_array($value)):
				 if(self::value_exists($needle,$value) == true)
					return ($opt==2)?array_search($needle,$value):NULL;
				 else
					 continue;
			endif;
		endforeach;
		return false;
	}
}