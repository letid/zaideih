<?php
function multi_array_key_exists($needle, $haystack,$opt=NULL) {
    foreach ( $haystack as $key => $value ) :
        if ( $needle == $key)
			return ($opt==1)?NULL:$value;
        if ( is_array( $value ) ) :
             if ( multi_array_key_exists($needle, $value ) == true )
                return ($opt==2)?$value[$needle]:NULL;
             else
                 continue;
        endif;
    endforeach;
    return false;
}
function multi_array_value_exists($needle, $haystack,$opt=NULL) {
    foreach ( $haystack as $key => $value ) :
        if ( $needle == $value)
			return ($opt==1)?NULL:$key;
        if ( is_array( $value ) ) :
             if ( multi_array_value_exists($needle, $value ) == true )
				return ($opt==2)?array_search($needle,$value):NULL;
             else
                 continue;
        endif;
    endforeach;
    return false;
}
function in_object($val, $obj){
	if($val == ""){
		trigger_error("in_object expects parameter 1 must not empty", E_USER_WARNING);
		return false;
	}
	if(!is_object($obj)){
		$obj = (object)$obj;
	}

	foreach($obj as $key => $value){
		if(!is_object($value) && !is_array($value)){
			if($value == $val){
				return true;
			}
		}else{
			return in_object($val, $value);
		}
	}
	return false;
}
function is_valid_email($email)
{
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) return true;
}
function is_valid_url($url)
{
	if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) return true;
}
function str_extract_domain($url)
{
	if(preg_match('/^((.+)\.)?([A-Za-z][0-9A-Za-z\-]{1,63})\.([A-Za-z]{3})(\/.*)?$/',$url,$matches)):
		return $matches[3].'.'.$matches[4];
	else:
		return false;
	endif;
}
function is_valid_date($date,$check_year=NULL,$format='YYYY-MM-DD'){
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
function array_search_multi($array, $key, $value)
{
    $results = array();
    if(is_array($array)):
        if(isset($array[$key]) && $array[$key] == $value)
            $results[] = $array;
        foreach ($array as $subarray)
            $results = array_merge($results, array_search_multi($subarray, $key, $value));
    endif;
    return $results;
}
function new_pwd_generator()
{
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
function isAjax()
{
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){ return true; }
}
function makesentence($q,$l=_and,$s=',')
{
	return implode(" $l ", array_filter(array_reverse(array_merge(array(array_pop($q)), array(implode("$s ",$q))))));
}
function makequery($q)
{
	return implode(', ',array_map(function($v,$k){return sprintf("%s='%s'", $k, addslashes($v));},$q,array_keys($q)));
}
function convert($size)
{
	$unit=array('b','kb','mb','gb','tb','pb');
	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
function str_addfullsthopncomma($str,$l=NULL)
{
	$str_tmp = preg_replace('~,\s*(?=[^,]*,[^,]*$)~', _and, rtrim($str));
	$isRight = ($l)?_fullstop:'';
	return $str_tmp.$isRight;
}
function str_limit_char($str,$num,$tail=NULL)
{
	if(strlen($str) > $num) {
        $str_limit = substr($str, 0, $num);
        return $str_limit;//$str_limit.($tail)?$tail:NULL;
	} else {
		return $str;
	}
}
function scramble_word($word)
{
	//echo preg_replace('/(\w+)/e', 'scramble_word("\1")', 'A quick brown fox jumped over the lazy dog.');
	if (strlen($word) < 2)
		return $word;
	else
		return $word{0} . str_shuffle(substr($word, 1, -1)) . $word{strlen($word) - 1};
}
function array_clean(array $haystack)
{
    foreach ($haystack as $key => $value){
        if(is_array($value)){
            $haystack[$key] = array_clean($value);
        }elseif(is_string($value)){
            $value = trim($value);
        }
        if(!$value){
            unset($haystack[$key]);
        }
    }
    return $haystack;
}
function time_elapsed_string($datetime, $full=false)
{
	$now = new DateTime;
	$ago = new DateTime($datetime);
	$diff = $now->diff($ago);

	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = array('y'=>'year','m'=>'month','w'=>'week','d'=>'day','h'=>'hour','i'=>'minute','s'=>'second');
	foreach($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}
	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}