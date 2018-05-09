<?php
/*
$time[] = '1:0';
$counthour = new counthour($time);
echo $counthour;
echo $counthour->define_hour();
echo $counthour->diff('21:09:04','22:57:23');
echo $counthour->define_hour();
*/
namespace app\component
{
  class counthour
  {
		var $format = 'M:S'; //M:S,HH:MM:SS,H:M:S
		var $pattern = array(
			'HH:MM:SS'=>'#([0-9]{2}):([0-9]{2}):([0-9]{2})$#',
			'H:M:S'=>'#([0-9]{1,100}):([0-9]{1,2}):([0-9]{1,2})$#',
			'M:S'=>'#([0-9]{1,100}):([0-9]{1,2})$#'
		);
		var $hours;
		var $result;
		public function __construct($hours,$method='get')
		{
			$this->hours = $hours;
			if ($method):
				call_user_func(array($this, $method));
			endif;
		}
		public function get()
		{
			foreach($this->hours as $hour)
				$diff[] = $this->seconds($hour);
			$this->result = $this->std(array_sum($diff));
		}
	    public function check($time) {
	        if(preg_match($this->pattern[$this->format],$time)) $this->result = 'Ok';
				else $this->result = "Format should be $time {$this->format}";
	    }
	    function seconds($time) {
	        $t = explode(':',$time);
			if (isset($t[2])) return $t[0]*3600 + $t[1]*60 + $t[2];
				elseif (isset($t[1])) return $t[0]*60 + $t[1];
				elseif (isset($t[0])) return $t[0];
					else return 1;
	    }
	    function std($init) {
	        return gmdate("H:i:s", $init);
	    }
	    function diff_seconds($first,$last) {
	        $f = $this->seconds($first);
	        $l = $this->seconds($last);
	        if($l<$f || $l==$f) return $f-$l;
	        	else return $l-$f;
	    }
	    function diff($first,$last) {
	        $diff = $this->diff_seconds($first,$last);
			$this->result = $this->std($diff);
	        return $this->result;
	    }
		function define_hour() {
			$time_array = explode(':',$this->result);
			$hourr = $time_array[0];
			$minutee = $time_array[1];
			$secondd = $time_array[2];
			if($hourr==0) $hourr = '';
				elseif($hourr==1) $hourr = '1 hour ';
				elseif($hourr>1 && $hourr<10) $hourr = str_replace('0','',$hourr).' hours ';
					else $hourr = $hourr.' hours ';
			if($minutee==0) $minutee = '';
				elseif($minutee==1) $minutee = '1 minute ';
				elseif($minutee>1 && $minutee<10) $minutee = str_replace('0','',$minutee).' minutes ';
					else $minutee = $minutee.' minutes ';
			if($secondd==0)  $secondd = '';
				elseif($secondd==1) $secondd = '1 second';
				elseif($secondd>1 && $secondd<10)  $secondd = str_replace('0','',$secondd).' seconds';
				else $secondd = $secondd.' seconds';
			return trim($hourr.$minutee.$secondd);
		}
		public function __toString()
		{
			if($this->result) return $this->result;
		}
	}
}