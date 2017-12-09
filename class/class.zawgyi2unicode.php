<?php
class zawgyi2unicode
{
	private $logfile="log.txt";
	private $fh;
	private $BeginTime;
	private $EndTime;
	/*
	public function __construct($data)
	{
		$this->_r_ = zg_uni($data);
	}
	*/
	//unicode number to character
	private function unichr($hex)
	{
		$str="&#".hexdec($hex).";";
		return html_entity_decode($str, ENT_QUOTES, "UTF-8");
	}
	function zg_uni($txt)
	{
		$tallAA=$this->unichr("102B");
		$AA=$this->unichr("102C");
		$vi=$this->unichr("102D");

		//lone gyi tin
		$ii=$this->unichr("102E");
		$u=$this->unichr("102F");
		$uu=$this->unichr("1030");
		$ve=$this->unichr("1031");

		$ai = $this->unichr("1032");
		$ans = $this->unichr("1036");
		$db = $this->unichr("1037");
		$visarga = $this->unichr("1038");

		$asat = $this->unichr("103A");

		$ya = $this->unichr("103B");
		$ra = $this->unichr("103C");
		$wa = $this->unichr("103D");
		$ha = $this->unichr("103E");
		$zero = $this->unichr("1040");

		$j=0;

		$pattern[$j]="/".$this->unichr("106A")."/u";
		$replacement[$j] = $this->unichr("1009");

		$j++;

		$pattern[$j]="/".$this->unichr("1025")."(?=[".$this->unichr("1039").$this->unichr("102C")."])/u";
		$replacement[$j] = $this->unichr("1009");

		$j++;

		$pattern[$j]="/".$this->unichr("1025").$this->unichr("102E")."/u";
		$replacement[$j] = $this->unichr("1026");

		$j++;

		$pattern[$j]="/".$this->unichr("106B")."/u";
		$replacement[$j] = $this->unichr("100A");

		$j++;

		$pattern[$j]="/".$this->unichr("1090")."/u";
		$replacement[$j] = $this->unichr("101B");

		$j++;

		$pattern[$j]="/".$this->unichr("1040")."/u";
		$replacement[$j] = $zero;

		$j++;

		$pattern[$j]="/".$this->unichr("108F")."/u";
		$replacement[$j] = $this->unichr("1014");

		$j++;

		$pattern[$j]="/".$this->unichr("1012")."/u";
		$replacement[$j] = $this->unichr("1012");

		$j++;

		$pattern[$j]="/".$this->unichr("1013")."/u";
		$replacement[$j] = $this->unichr("1013");

		$j++;

		$pattern[$j]="/[".$this->unichr("103D").$this->unichr("1087")."]/u";
		$replacement[$j] = $ha;

		$j++;

		$pattern[$j]="/".$this->unichr("103C")."/u";

		$replacement[$j] = $wa;

		$j++;

		$pattern[$j]="/[".$this->unichr("103B").$this->unichr("107E").$this->unichr("107F").$this->unichr("1080").$this->unichr("1081").$this->unichr("1082").$this->unichr("1083").$this->unichr("1084")."]/u";
		$replacement[$j] = $ra;

		$j++;


		$pattern[$j]="/[".$this->unichr("103A").$this->unichr("107D")."]/u";
		$replacement[$j] = $ya;

		$j++;

		$pattern[$j]="/".$this->unichr("103E").$this->unichr("103B")."/u";
		$replacement[$j] = $ya.$ha;

		$j++;

		$pattern[$j]="/".$this->unichr("108A")."/u";
		$replacement[$j] = $wa.$ha;

		$j++;

		$pattern[$j]="/".$this->unichr("103E").$this->unichr("103D")."/u";
		$replacement[$j] = $wa.$ha;
		$j++;


		/////Reordering/////
		$pattern[$j]="/(".$this->unichr("1031").")?(".$this->unichr("103C").")?([".$this->unichr("1000")."-".$this->unichr("1021")."])".$this->unichr("1064")."/u";

		$replacement[$j] = $this->unichr("1064")."$1$2$3";

		$j++;

		$pattern[$j]="/(".$this->unichr("1031").")?(".$this->unichr("103C").")?([".$this->unichr("1000")."-".$this->unichr("1021")."])".$this->unichr("108B")."/u";

		$replacement[$j] = $this->unichr("1064")."$1$2$3".$this->unichr("102D");

		$j++;

		$pattern[$j]="/(".$this->unichr("1031").")?(".$this->unichr("103C").")?([".$this->unichr("1000")."-".$this->unichr("1021")."])".$this->unichr("108C")."/u";

		$replacement[$j] = $this->unichr("1064")."$1$2$3".$this->unichr("102E");

		$j++;

		$pattern[$j]="/(".$this->unichr("1031").")?(".$this->unichr("103C").")?([".$this->unichr("1000")."-".$this->unichr("1021")."])".$this->unichr("108D")."/u";

		$replacement[$j] = $this->unichr("1064")."$1$2$3".$this->unichr("1036");

		$j++;
		///////////////////

		$pattern[$j]="/".$this->unichr("105A")."/u";
		$replacement[$j] = $tallAA.$asat;

		$j++;

		$pattern[$j]="/".$this->unichr("108E")."/u";
		$replacement[$j] = $vi.$ans;

		$j++;

		$pattern[$j]="/".$this->unichr("1033")."/u";
		$replacement[$j] = $u;

		$j++;

		$pattern[$j]="/".$this->unichr("1034")."/u";
		$replacement[$j] = $uu;

		$j++;

		$pattern[$j]="/".$this->unichr("1088")."/u";
		$replacement[$j] = $ha.$u;

		$j++;

		$pattern[$j]="/".$this->unichr("1089")."/u";
		$replacement[$j] = $ha.$uu;

		$j++;

		/////////////////

		$pattern[$j]="/".$this->unichr("1039")."/u";
		$replacement[$j] = $this->unichr("103A");

		$j++;

		$pattern[$j]="/(".$this->unichr("1094")."|".$this->unichr("1095").")/u";
		$replacement[$j] = $db;

		$j++;

		/////////////
		//pasint order , human error
		$pattern[$j]="/([".$this->unichr("1000")."-".$this->unichr("1021")."])([".$this->unichr("102C").$this->unichr("102D").$this->unichr("102E").$this->unichr("1032").$this->unichr("1036")."]){1,2}([".$this->unichr("1060").$this->unichr("1061").$this->unichr("1062").$this->unichr("1063").$this->unichr("1065").$this->unichr("1066").$this->unichr("1067").$this->unichr("1068").$this->unichr("1069").$this->unichr("1070").$this->unichr("1071").$this->unichr("1072").$this->unichr("1073").$this->unichr("1074").$this->unichr("1075").$this->unichr("1076").$this->unichr("1077").$this->unichr("1078").$this->unichr("1079").$this->unichr("107A").$this->unichr("107B").$this->unichr("107C").$this->unichr("1085")."])/u";
		$replacement[$j] = "$1$3$2";

		$j++;

		//////////////

		$pattern[$j]="/".$this->unichr("1064")."/u";
		$replacement[$j] = $this->unichr("1004").$this->unichr("103A").$this->unichr("1039");

		$j++;

		$pattern[$j]="/".$this->unichr("104E")."/u";
		$replacement[$j] = $this->unichr("104E").$this->unichr("1004").$this->unichr("103A").$this->unichr("1038");

		$j++;

		$pattern[$j]="/".$this->unichr("1086")."/u";
		$replacement[$j] = $this->unichr("103F");

		$j++;


		$pattern[$j]="/".$this->unichr("1060")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1000");

		$j++;

		$pattern[$j]="/".$this->unichr("1061")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1001");

		$j++;

		$pattern[$j]="/".$this->unichr("1062")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1002");

		$j++;

		$pattern[$j]="/".$this->unichr("1063")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1003");

		$j++;

		$pattern[$j]="/".$this->unichr("1065")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1005");

		$j++;

		$pattern[$j]="/[".$this->unichr("1066").$this->unichr("1067")."]/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1006");

		$j++;

		$pattern[$j]="/".$this->unichr("1068")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1007");

		$j++;

		$pattern[$j]="/".$this->unichr("1069")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1008");

		$j++;

		$pattern[$j]="/".$this->unichr("106C")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("100B");

		$j++;

		$pattern[$j]="/".$this->unichr("1070")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("100F");

		$j++;

		$pattern[$j]="/[".$this->unichr("1071").$this->unichr("1072")."]/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1010");

		$j++;

		$pattern[$j]="/[".$this->unichr("1073").$this->unichr("1074")."]/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1011");

		$j++;

		$pattern[$j]="/".$this->unichr("1075")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1012");

		$j++;


		$pattern[$j]="/".$this->unichr("1076")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1013");

		$j++;

		$pattern[$j]="/".$this->unichr("1077")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1014");

		$j++;

		$pattern[$j]="/".$this->unichr("1078")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1015");

		$j++;

		$pattern[$j]="/".$this->unichr("1079")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1016");

		$j++;

		$pattern[$j]="/".$this->unichr("107A")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1017");

		$j++;

		$pattern[$j]="/".$this->unichr("107B")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1018");

		$j++;

		$pattern[$j]="/".$this->unichr("107C")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("1019");

		$j++;

		$pattern[$j]="/".$this->unichr("1085")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("101C");

		$j++;

		$pattern[$j]="/".$this->unichr("106D")."/u";
		$replacement[$j] = $this->unichr("1039").$this->unichr("100C");

		$j++;

		$pattern[$j]="/".$this->unichr("1091")."/u";
		$replacement[$j] = $this->unichr("100F").$this->unichr("1039").$this->unichr("100D");

		$j++;

		$pattern[$j]="/".$this->unichr("1092")."/u";
		$replacement[$j] = $this->unichr("100B").$this->unichr("1039").$this->unichr("100C");

		$j++;

		$pattern[$j]="/".$this->unichr("1097")."/u";
		$replacement[$j] = $this->unichr("100B").$this->unichr("1039").$this->unichr("100B");

		$j++;

		$pattern[$j]="/".$this->unichr("106F")."/u";
		$replacement[$j] = $this->unichr("100E").$this->unichr("1039").$this->unichr("100D");

		$j++;

		$pattern[$j]="/".$this->unichr("106E")."/u";
		$replacement[$j] = $this->unichr("100D").$this->unichr("1039").$this->unichr("100D");

		$j++;


		$pattern[$j]="/(".$this->unichr("103C").")([".$this->unichr("1000")."-".$this->unichr("1021")."])/u";
		$replacement[$j] = "$2$1$3";

		$j++;


		//$pattern[$j]="/(".$this->unichr("103E").")?(".$this->unichr("103D").")?([".$this->unichr("103B").$this->unichr("103C")."])/u";
		//$replacement[$j] = "$3$2$1";

		//$j++;


		$pattern[$j]="/(".$this->unichr("103E").")(".$this->unichr("103D").")([".$this->unichr("103B").$this->unichr("103C")."])/u";
		$replacement[$j] = $this->unichr("100D").$this->unichr("1039").$this->unichr("100D");

		$j++;


		/*$pattern[$j]="/(".$this->unichr("103E").")([".$this->unichr("103B").$this->unichr("103C")."])/u";
		$replacement[$j] = "$2$1";

		$j++;
		*/
		$pattern[$j]="/(".$this->unichr("103D").")([".$this->unichr("103B").$this->unichr("103C")."])/u";
		$replacement[$j] = "$2$1";

		$j++;


		//need to add 0 or wa

		// need to add 7 or ra

		//storage order rediner
		$pattern[$j]="/(".$this->unichr("1031").")?([".$this->unichr("1000")."-".$this->unichr("1021")."])(".$this->unichr("1039")."[".$this->unichr("1000")."-".$this->unichr("1021")."])?([".$this->unichr("102D").$this->unichr("102E").$this->unichr("1032")."])?([".$this->unichr("1036").$this->unichr("1037").$this->unichr("1038")."]{0,2})([".$this->unichr("103B")."-".$this->unichr("103E")."]{0,3})([".$this->unichr("102F").$this->unichr("1030")."])?([".$this->unichr("1036").$this->unichr("1037").$this->unichr("1038")."]{0,2})([".$this->unichr("102D").$this->unichr("102E").$this->unichr("1032")."])?/u";
		$replacement[$j] ="$2$3$6$1$4$9$7$5$8";

		$j++;

		$pattern[$j]="/".$ans.$u."/u";
		$replacement[$j] = $u.$ans;

		$j++;

		$pattern[$j]="/(".$this->unichr("103A").")(".$this->unichr("1037").")/u";
		$replacement[$j] = "$2$1";

		$j++;
	   ///
		$txt=preg_replace($pattern, $replacement, $txt);
		return $txt;
	}

	function html_decode($constr)
	{
		//change HTML to unicode

		$en_chr=array("&#4096;", "&#4097;", "&#4098;", "&#4099;", "&#4100;", "&#4101;", "&#4102;", "&#4103;", "&#4104;", "&#4105;", "&#4106;", "&#4107;", "&#4108;", "&#4109;", "&#4110;", "&#4111;", "&#4112;", "&#4113;", "&#4114;", "&#4115;", "&#4116;", "&#4117;", "&#4118;", "&#4119;", "&#4120;", "&#4121;", "&#4122;", "&#4123;", "&#4124;", "&#4125;", "&#4126;", "&#4127;", "&#4128;", "&#4129;", "&#4130;", "&#4131;", "&#4132;", "&#4133;", "&#4134;", "&#4135;", "&#4136;", "&#4137;", "&#4138;", "&#4139;", "&#4140;", "&#4141;", "&#4142;", "&#4143;", "&#4144;", "&#4145;", "&#4146;", "&#4147;", "&#4148;", "&#4149;", "&#4150;", "&#4151;", "&#4152;", "&#4153;", "&#4154;", "&#4155;", "&#4156;", "&#4157;", "&#4158;", "&#4159;", "&#4160;", "&#4161;", "&#4162;", "&#4163;", "&#4164;", "&#4165;", "&#4166;", "&#4167;", "&#4168;", "&#4169;", "&#4170;", "&#4171;", "&#4172;", "&#4173;", "&#4174;", "&#4175;", "&#4176;", "&#4177;", "&#4178;", "&#4179;", "&#4180;", "&#4181;", "&#4182;", "&#4183;", "&#4184;", "&#4185;", "&#4186;", "&#4187;", "&#4188;", "&#4189;", "&#4190;", "&#4191;", "&#4192;", "&#4193;", "&#4194;", "&#4195;", "&#4196;", "&#4197;", "&#4198;", "&#4199;", "&#4200;", "&#4201;", "&#4202;", "&#4203;", "&#4204;", "&#4205;", "&#4206;", "&#4207;", "&#4208;", "&#4209;", "&#4210;", "&#4211;", "&#4212;", "&#4213;", "&#4214;", "&#4215;", "&#4216;", "&#4217;", "&#4218;", "&#4219;", "&#4220;", "&#4221;", "&#4222;", "&#4223;", "&#4224;", "&#4225;", "&#4226;", "&#4227;", "&#4228;", "&#4229;", "&#4230;", "&#4231;", "&#4232;", "&#4233;", "&#4234;", "&#4235;", "&#4236;", "&#4237;", "&#4238;", "&#4239;", "&#4240;", "&#4241;", "&#4242;", "&#4243;", "&#4244;", "&#4245;", "&#4246;", "&#4247;", "&#4248;", "&#4249;", "&#4250;", "&#4251;", "&#4252;", "&#4253;", "&#4254;", "&#4255;");

		$utf8_chr=array("က", "ခ", "ဂ", "ဃ", "င", "စ", "ဆ", "ဇ", "ဈ", "ဉ", "ည", "ဋ", "ဌ", "ဍ", "ဎ", "ဏ", "တ", "ထ", "ဒ", "ဓ", "န", "ပ", "ဖ", "ဗ", "ဘ", "မ", "ယ", "ရ", "လ", "ဝ", "သ", "ဟ", "ဠ", "အ", "ဢ", "ဣ", "ဤ", "ဥ", "ဦ", "ဧ", "ဨ", "ဩ", "ဪ", "ါ", "ာ", "ိ", "ီ", "ု", "ူ", "ေ", "ဲ", "ဳ", "ဴ", "ဵ", "ံ", "့", "း", "္", "်", "ျ", "ြ", "ွ", "ှ", "ဿ", "၀", "၁", "၂", "၃", "၄", "၅", "၆", "၇", "၈", "၉", "၊", "။", "၌", "၍", "၎", "၏", "ၐ", "ၑ", "ၒ", "ၓ", "ၔ", "ၕ", "ၖ", "ၗ", "ၘ", "ၙ", "ၚ", "ၛ", "ၜ", "ၝ", "ၞ", "ၟ", "ၠ", "ၡ", "ၢ", "ၣ", "ၤ", "ၥ", "ၦ", "ၧ", "ၨ", "ၩ", "ၪ", "ၫ", "ၬ", "ၭ", "ၮ", "ၯ", "ၰ", "ၱ", "ၲ", "ၳ", "ၴ", "ၵ", "ၶ", "ၷ", "ၸ", "ၹ", "ၺ", "ၻ", "ၼ", "ၽ", "ၾ", "ၿ", "ႀ", "ႁ", "ႂ", "ႃ", "ႄ", "ႅ", "ႆ", "ႇ", "ႈ", "ႉ", "ႊ", "ႋ", "ႌ", "ႍ", "ႎ", "ႏ", "႐", "႑", "႒", "႓", "႔", "႕", "႖", "႗", "႘", "႙", "ႚ", "ႛ", "ႜ", "ႝ", "႞", "႟");


		$last=str_replace($en_chr,$utf8_chr,$constr);
		return $last;

	}

	static function FormatElapsed($Start, $End = NULL) {
		  if($End === NULL)
			 $Elapsed = $Start;
		  else
			 $Elapsed = $End - $Start;

		  $m = floor($Elapsed / 60);
		  $s = $Elapsed - $m * 60;
		  $Result = sprintf('%02d:%05.2f', $m, $s);

		  return $Result;
	}
	/*
	Log Files
	*/
	function start_log()
	{
		//start time
		$this->fh = fopen($this->logfile, 'w') or die("can't open file");
		$this->BeginTime=microtime(true);
	}

	function decodedone_log()
	{
		fwrite($this->fh,"HTML Decode Done.\n");
	}

	function end_log()
	{
		//end time
		$this->EndTime=microtime(true);
		fwrite($this->fh,"Total Time:");
		fwrite($this->fh, self::FormatElapsed($this->EndTime-$this->BeginTime));
		fclose($this->fh);
	}
    public function __toString()
    {
        return (string)$this->_r_;
    }
}
/*
$zawgyi2unicode= new zawgyi2unicode();
$this->_r_ $zawgyi2unicode->zg_uni($definition)
*/