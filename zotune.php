<?php
class zotune {
	public static $home,$init,$info,$data,$z,$i;
	public static $db=array();
	protected static $user=array();
	/*
	with z:: Template=1,Page=2,Meta=3,Supports=4,Table=5,Detect=6
	*/
	public static $Template,$Language,$Meta=array(),$Page,$Url;
	public $uri=array(),$zotune_lastupdate = '2014-08-27 21:55';

	/*
	lastupdate using -> ("Y-n-j H:i:s")
	("D, d M Y H:i:s T")
	("l jS \of F Y h:i:s A")
	("Y-n-j")
	*/
	public function __construct() {
		self::var_table();
		self::var_uri();
		$this->http_referer			= isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:false;
		$this->name_referer 		= parse_url($this->http_referer, PHP_URL_HOST);
		$this->ip					= $_SERVER['REMOTE_ADDR'];
		$this->q					= isset($_GET['q'])?$_GET['q']:false;
		$this->mdHis				= date("mdHis");
	}
	public function isLogged($t,$m,$i,$p) {
		$db = new sql("SELECT *,DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(dob)), '%Y')+0 AS age FROM $t WHERE id=$i AND password='$p'",'get_object');
		if($db->total):
			foreach($db->rows as $k => $v) self::$user[$k]=$v;
			if(method_exists($m[0],$m[1]) == true) call_user_func($m);
			return true;
		endif;
	}
	public function isAuthorization($l) {
		$auth=self::$init['admin'][$l];
		$level=@self::$user['level'];
		return ($auth['level']<=$level || in_array(@self::$user['id'],$auth['userid']))?self::$init['admin']['level'][$level]:false;
	}
	public function noAuthorization($d=array()) {
		self::$info['pro.description'] = isset($d['d'])?$d['d']:'error';
		self::$info['pro.msg'] = isset($d['m'])?$d['m']:self::ztl('Authorization required!');
		self::$info['page.including'] = 'msg';
		self::$info['page.type'] = 'system';
	}
	public function zPathInitiate() {
		foreach(self::$init['dir'] as $n=>$d) self::$info['url.'.$n]=self::$init['path'].self::$info['dir.'.$n]=self::ztd($d);
	}
	public function zPageInitiate($p) {
		$USN = self::$init['user.speed.name'];
		if(empty($_SESSION[$USN][4]))$_SESSION[$USN][4]=self::zPageInitiation($p);
		self::$Page = $_SESSION[$USN][4];
	}
	private function zPageInitiation($p,$sub=NULL) {
		if(is_array($p)):
			foreach($p as $pk => $pv):
				if(isset($pv['authorization']) and is_array($pv['authorization'])):
					if(count($pv['authorization']) == self::zPageAuthorization($pv['authorization'])):
						$r[$pk] = self::zPageInitiation($pv);
					endif;
				else:
					$r[$pk] = self::zPageInitiation($pv);
				endif;
			endforeach;
		else:
			$r = $p;
		endif;
		return $r;
	}
	private function zPageArrange($page,$sub=NULL) {
		$s1 = @$sub[0]; $s2 = @$sub[1]; $p = @$page[$s1];self::$z[2]=array();
		if (is_array($p)):
			self::$z[2][] = $s1;
			if(is_array(@$p[$s2]) and isset($p[$s2]['menu']) || isset($p[$s2]['Class']) || isset($p[$s2]['Method'])):
				array_shift($sub);
				$p = array_replace_recursive($p[$s2],array_replace_recursive($p,self::zPageArrange($p,$sub)));
			endif;
		endif;
		return $p;
	}
	private function zPageAuthorization($p) {
		$_is_auth = 0;
		foreach($p as $auth => $type):
			$self_auth = @self::$user[$auth];
			if(is_array($type)):
				if(isset($type['operator'])):
					$au_mch = ($self_auth)?$self_auth:0;
					if(eval("return ($au_mch {$type['operator']} {$type[0]});")):
						$_is_auth ++;
					endif;
				else:
					if(in_array($self_auth,$type)) $_is_auth ++;
				endif;
			elseif(is_numeric($type) and $self_auth >= $type):
				$_is_auth ++;
			endif;
		endforeach;
		return $_is_auth;
	}
	public function zReg($class,$d) {
		if(class_exists($class) && is_array($d) && $n=new $class) foreach($d as $m => $v) if(is_array($v)) $n->{$m}($v); else $n->{$v}();
	}
	public function zRegInitiate($d,$c) {
		if(is_array($c)):
			if(array_key_exists('+',$c))$d=array_merge_recursive($d,$c['+']);
			if(array_key_exists('-',$c))foreach($c['-'] as $s) unset($d[array_search($s, $d)]);
			return $d;
		elseif($c or $c===false):
			return $c;
		else:
			return $d;
		endif;
	}
	private function RequirePage($files) {
		foreach($files as $file)
			if(file_exists($f=self::$info['dir.project'].$file)) require_once($f);
				elseif(file_exists($f=self::$info['dir.class'].$file)) require_once($f);
	}
	private function RequireLanguage($files) {
		self::$Language = $files;
	}
	private function RequireLanguageInitiate() {
		if(is_array(self::$Language)):
			$USN = self::$init['user.speed.name'];
			$SIL = self::$init['sil.current'];
			$SID = self::$init['sil.default'];
			$DRC = self::$info['dir.page.current'];
			//unset($_SESSION[$USN][$SIL][$DRC]);
			ob_start();
				if(isset($_SESSION[$USN][$SIL][$DRC]) && is_array($_SESSION[$USN][$SIL][$DRC])):
					foreach($_SESSION[$USN][$SIL][$DRC] as $file)require_once($file);
				else:
					$lc="lang.$SIL.php";
					$ld="lang.$SID.php";

					if(file_exists($page=$DRC.$lc)):
						require_once($page); $_SESSION[$USN][$SIL][$DRC]['page.current'] = $page;
					elseif($SIL != $SID and file_exists($page=$DRC.$ld)):
						require_once($page); $_SESSION[$USN][$SIL][$DRC]['page.default'] = $page;
					elseif(count(self::$z[2]) > 1):
						krsort(self::$z[2]);
						foreach(self::$z[2] as $page):
							if(file_exists($plc=self::$info['dir.page'].$page.'/'.$lc)):
								require_once($plc); $_SESSION[$USN][$SIL][$DRC]['page.current'] = $plc; break;
							elseif($SIL != $SID and file_exists($pld=self::$info['dir.page'].$page.'/'.$ld)):
								require_once($pld); $_SESSION[$USN][$SIL][$DRC]['page.current'] = $pld; break;
							else:
								continue;
							endif;
						endforeach;
					endif;
					foreach(self::$Language as $i => $file):
						$pro_current = self::$info['dir.language'].$SIL.'/'.$file;
						$pro_default = self::$info['dir.language'].$SID.'/'.$file;

						$app_current = self::$info['dir.common.language'].$SIL.'/'.$file;
						$app_default = self::$info['dir.common.language'].$SID.'/'.$file;

						if(file_exists($pro_current)):
							require_once($pro_current); $_SESSION[$USN][$SIL][$DRC]["lang.pro.current.$i"]=$pro_current;
						endif;
						if(file_exists($app_current)):
							require_once($app_current); $_SESSION[$USN][$SIL][$DRC]["lang.app.current.$i"]=$app_current;
						endif;
						if($SIL != $SID && $i == 'common'):
							if(file_exists($pro_default)):
								require_once($pro_default); $_SESSION[$USN][$SIL][$DRC]["lang.pro.default.$i"]=$pro_default;
							endif;
							if(file_exists($app_default)):
								require_once($app_default); $_SESSION[$USN][$SIL][$DRC]["lang.app.default.$i"]=$app_default;
							endif;
						endif;
					endforeach;
				endif;
			ob_end_clean();
		endif;
	}
	private function RequireMeta($files) {
		self::$Meta = $files;
	}
	private function RequireMetaFile($m,$a,$e,$s,$l) {
		$obj = $a[$s];
		if(is::valid_url($obj)):
			return $a;//return $obj;
		elseif($file=self::get_file_devising(self::ztl($obj),$e)):
			$a[$s] = str_replace(
				array(self::$info['dir.project'],self::$info['dir.common']),
				array(self::$info['url.project'],self::$info['url.common']),
			$file);
			return is_array($l)?array_merge($l,$a):$a;
		endif;
	}
	private function RequireMetaDevice($i,$x) {
		if(strpos($i,$x) !== false){
			if(strpos($i,$x.$_SESSION['device'][0]) !== false || strpos($i,$x.$_SESSION['detected_device']['device']) !== false) return false;
			return true;
		}


/*
						if(isset($d[$type]['s'])):
							if(strpos($i,':') !== false and strpos($i,':'.$_SESSION['device'][0]) === false):
								$a = NULL;

							else:
								$e = isset($d[$type]['e'])?$d[$type]['e']:ltrim(strstr($a['type'], '/'),'/');
								$a = self::RequireMetaFile($type,$a,".$e",$d[$type]['s'],$d[$type]['attach']);
							endif;
						elseif(strpos($i,':') !== false):
							if(strpos($i,':'.$_SESSION['detected_device']['device']) === false) $a = NULL;
							//$meta['meta']['viewport:m'] 		= array('name'=>'viewport', 'content'=>'initial-scale=1.0, user-scalable=no');
							//if(strpos($i,':'.$_SESSION['device'][0]) === false) $a = NULL;
						endif;
*/
	}
	public function RequireMetaInitiate($config){
//echo $_SESSION['device'][0].' '.$_SESSION['detected_device']['device'];
		if(is_array(self::$Meta)):
			$meta = array_replace_recursive(is_array($d=self::$z[3])?$d:array(), self::$Meta);
			if(is_array($config)) $meta = array_merge_recursive($config,$meta);
			$d['link'] = array('s'=>'href','attach'=>array('rel'=>'stylesheet'));
			$d['script'] = array('s'=>'src','e'=>'js');
			foreach(array_map('array_filter', $meta) as $type => $file):
				if(is_array($file)):
					$r = array();
					foreach($file as $i => $a):
						if(self::RequireMetaDevice($i,':')):
							$a = NULL;
						elseif(isset($d[$type]['s'])):
							$e = isset($d[$type]['e'])?$d[$type]['e']:ltrim(strstr($a['type'], '/'),'/');
							$a = self::RequireMetaFile($type,$a,".$e",$d[$type]['s'],$d[$type]['attach']);
						endif;
						/*
						if(isset($d[$type]['s'])):
							if(self::RequireMetaDevice($i,':')):
								$a = NULL;

							else:
								$e = isset($d[$type]['e'])?$d[$type]['e']:ltrim(strstr($a['type'], '/'),'/');
								$a = self::RequireMetaFile($type,$a,".$e",$d[$type]['s'],$d[$type]['attach']);
							endif;
						elseif(self::RequireMetaDevice($i,':')):
							$a = NULL;
						endif;
						*/
					/*
							//$meta['meta']['viewport:m'] 		= array('name'=>'viewport', 'content'=>'initial-scale=1.0, user-scalable=no');
							//if(strpos($i,':'.$_SESSION['device'][0]) === false) $a = NULL;


						if(isset($d[$type]['s'])):
							$e = isset($d[$type]['e'])?$d[$type]['e']:ltrim(strstr($a['type'], '/'),'/');
							$a = $this->RequireMetaFile($type,$a,".$e",$d[$type]['s'],$d[$type]['attach']);
						elseif(strpos($i,':') !== false):
							//if(strpos($i,':'.$_SESSION['detected_device']['device']) === false) $a = NULL;
							//$meta['meta']['viewport:m'] 		= array('name'=>'viewport', 'content'=>'initial-scale=1.0, user-scalable=no');
							if(strpos($i,':'.$_SESSION['device'][0]) === false) $a = NULL;
						endif;
						*/
						if($a) $r[] = self::$data["head.$type.$i"] = new html($type,NULL,$a);
					endforeach;
					self::$data["head.$type"] = implode("\n    ",$r);
				endif;
			endforeach;
		endif;
	}
	private function RequireTemplate($files){
		self::$Template = $files;
	}
	private function RequireTemplateInitiate(){
		self::$z[3]=array();
		if(is_array(self::$Template)):
			self::$z[3]['link']['current']['type'] = 'text/css';
			self::$z[3]['script']['current']['type'] = 'text/javascript';
			if(file_exists(self::$info['dir.page.current'])):
				$favicon = self::$info['dir.page.current'].'favicon';
				if(file_exists("$favicon.png"))self::$z[3]['link']['favicon']['href']=$favicon;
				self::$z[3]['link']['current']['href']=self::$info['dir.page.current'].self::$init['file']['style'];
				self::$z[3]['script']['current']['src']=self::$info['dir.page.current'].self::$init['file']['script'];
			else:
				self::$z[3]['link']['current']['href']=self::$info['dir.page.main'].self::$info['page.current'];
				self::$z[3]['script']['current']['src']=self::$info['dir.page.main'].self::$info['page.current'];
			endif;
			foreach(self::$Template as $name => $page) if($page) self::$z[1][$name]=self::RequireTemplateFile($page); else self::$z[1][$name]=$page;
		endif;
	}
	private function RequireTemplateFile($page) {
		if($x=self::get_file_devising(self::$info['dir.page.current'].$page));
		if(!$x) $x=self::get_file_devising(self::$info['dir.page.main'].self::$info['page.current'].'.'.$page);
		if(!$x and self::$info['page.main'] != self::$info['page.current']) $x=self::get_file_devising(self::$info['dir.page.main'].$page);
		if(!$x) $x=self::get_file_devising(self::$info['dir.template'].$page);
		return $x;
	}
	public function zPage(){
		//self::ini_error();
		$PPL							= self::zPageArrange(self::$Page,(self::$Page[$this->uri[0]])?$this->uri:array(self::$init[1][0]));
		self::$info['page.main'] 		= self::$z[2][0];
		self::$info['page.current'] 	= end(self::$z[2]);
		self::$Language					= self::$init['sil.require'];
		//print_r(array_replace_recursive(self::$Page[self::$init[1][0]],$PPL));
		if(is_array(self::$Page))foreach(array_replace_recursive(self::$Page[self::$init[1][0]],$PPL) as $k => $v):
			if(is_array($v) and method_exists($this,$k) == true) $this->{$k}($v);
				elseif(strpos($k,'.')) self::$info[$k]=$v;
				//elseif(strpos($k,'.') and $v) self::$info[$k]=($v)?$v:@self::$info[$k];
					else ${$k}=$v;//self::$z[$k]=$v;
		endforeach;
		$pid=self::$info['dir.page'];
		$www=self::$init['www'];
		self::$info['url.page.main']=$www.self::$info['dir.page.main']=$pid.self::$info['page.main'].'/';
		self::$info['url.page.current']	= $www.self::$info['dir.page.current']=$pid.self::$info['page.current'].'/';

		self::$info['page.link.full']		= self::$init['www'].self::$info['page.link'];
		self::$info['page.link.full.url']	= self::$init['www'].implode('/',$this->uri);

		if(empty(self::$user['id']) and self::$info['page.type'] == 'user'):
			self::$info['page.type'] = 'redirect';
		elseif(isset(self::$user['id']) and self::$info['page.type'] == 'guest'):
			self::$info['page.type'] = 'redirect';
		else:
			self::RequireLanguageInitiate();
			self::zReg('supports',self::zRegInitiate(self::$init['require.supports'],self::$info['require.supports']));
			self::zReg($Class,array($Method));
			if(self::$info['page.including']):
				if(file_exists(self::$info['page.including'])):
					self::$info['page.type'] = 'including';
				elseif(file_exists($page=self::$info['dir.project'].self::$info['page.including'])):
					self::$info['page.including']=$page;
					self::$info['page.type']='including';
				elseif($page=self::get_file_devising(self::$info['dir.content'].self::$info['page.including'])):
					self::$info['page.including']=$page;
					self::$info['page.type']='content';
				else:
					self::$info['page.type'] = 'system';
				endif;
			else:
				self::RequireTemplateInitiate();
			endif;
			self::zReg('consigns',self::zRegInitiate(self::$init['require.consigns'],self::$info['require.consigns']));
		endif;
	}
	public function link($text,$link,$iscurrent=NULL){
		if ($text):
			if (0 === strpos($link, self::$init['www'])):
				$attr['href'] = $link;
			elseif(is::valid_url($link)):
				$attr['href'] = $link;
				$attr['target'] = '_blank';
			else:
				$attr['href'] = self::$init['www'].$link;
			endif;
			if($iscurrent && $iscurrent != ':')$attr['class']=$iscurrent;
			return self::tags('a',self::ztl($text,true),$attr);
		endif;
	}
	public function tags($tag,$text,$attr=array()){
		if($tag and $text)return new html($tag,$text, array_filter($attr));
	}
	public function menu($pages,$type=NULL,$sub=NULL){
		$menu = array();
		foreach($pages as $m => $page):
			if (isset($page['navigator'])):
				if (isset($sub[1])):
					if ($sub[1] == $m):
						$iscurrent = ' current child';
						array_shift($sub);
					else:
						$iscurrent = NULL;
					endif;
				elseif (self::$info['page.main'] == $m):
					if (count($this->uri) > 0):
						$sub = $this->uri;
						$iscurrent = ' current parent';
					else:
						$iscurrent = ' current';
					endif;
				else:
					$iscurrent = NULL;
				endif;
				if (is_array($page)):
					$sub_menu = $this->menu($page,NULL,@$sub);
					$haschild = ($sub_menu)?' hasChild':NULL;
					$isother = isset($_GET[$m])?' current':NULL;
					$link = self::link($page['menu'],$page['page.link'],@$page['a.class']);
					$mn= new html('li', $link.$sub_menu, array('class'=>$m.$iscurrent.$isother.$haschild));
					if ($type):
						if ($page_type = isset($page['page.type'])?$page['page.type']:'page' and $page_type == $type):
							$menu[] =$mn;
						endif;
					else:
						$menu[] = $mn;
					endif;
				endif;
			endif;
		endforeach;
		if($menu and $menuclass = ($type)?"menu-{$type}":'menu')return new html('ul', implode($menu),array('class' => $menuclass));
	}
	public function zExecution(){
		switch(self::$info['page.type'])
		{
			case 'redirect':
				$url=(self::$info['page.redirect.url'])?self::$info['page.redirect.url']:self::$init['www'];
				header("Location: $url"); break;
			case 'header': exit(self::$data['page.data']); break;
			case 'json': echo json_encode(self::$data['page.data']); break;
			case 'content': exit(self::ztf(self::$info['page.including'],false)); break;
			case 'system': exit(self::ztf(self::$info['page.including'],array('dir'=>'dir.common.tpl','key'=>false))); break;
			case 'api': exit(self::$data['page.data']); break;
			case 'including': require_once(self::$info['page.including']); break;
			case 'array': print_r(self::$data['page.data']); break;
			default: self::done();
		}
	}
	public function zExecution_error($template,$init){
		self::$init=$init;
		self::zPathInitiate();
		self::$info['page.including'] = $template;
		self::$info['page.type'] = 'system';
		self::zExecution();
	}
	public function zHeader(){
		if(is_array($data=self::$data['page.data'])){
			foreach($data as $header => $value){
				header("$header:$value");
			}
		}
	}
	public function done(){
		if(isset(self::$z[1]))
			foreach(self::$z[1] as $name=>$template)
				if($template == end(self::$z[1])) exit(self::ztf($template,false));
					else self::ztf($template,array('dir'=>NULL,'device'=>false,'key'=>$name));
	}
	public function data_select_current($d,$n=NULL,$k=NULL,$i=NULL,$c=false) {
		if (is_array($d))
			foreach($d as $values => $names):
				$value = ($k)?@$names[$k]:$values;
				$name = ($n)?@$names[$n]:$names;
				if ($value == $i):
					if($c==true) if(defined($names)) return $names;
						else return $name;
				endif;
			endforeach;
	}
	public function form_select_option($d,$n=NULL,$k=NULL,$i=NULL,$c=false){
		if (is_array($d)):
			$option = array();
			foreach($d as $values => $names):
				$value = ($k)?@$names[$k]:$values; $name = ($n)?@$names[$n]:$names;
				if ($value == $i) $attributes = array('value'=>$value, 'selected'=>'selected');
					else $attributes = array('value'=>$value);
				if($c==true){
					if(defined($names))$option[]=new html('option',constant($name),$attributes);
				}else{
					$option[]=new html('option',$name,$attributes);
				}
			endforeach;
			return implode($option);
		endif;
	}
	public function get_file_devising($page,$e=NULL){
		if(is_array($d=$_SESSION['tpl'])):
			while(list($k,$extension)=each($d)):
				if($e and $e != self::$init[3])$extension = str_replace(self::$init[3],$e,$extension);
				if($file = get::files($page.$extension)):
					return $file;
					break;
				endif;
			endwhile;
		else:
			$extension = ($e)?$e:self::$init[3];
			if($file = get::files($page.$extension)) return $file;
		endif;
	}
	public function get_list_objecting($l,$n,$a=NULL){
		foreach($l as $t => $x)
			if(is_array($x)) self::get_list_objecting($x,$t,$a);
				elseif(isset($_GET[$t]) && array_key_exists($t, $l) or $t ==$a) self::$data["$n.selected"] = $x;
					else self::$data[$t] = $x;
	}
	public function var_table(){
		foreach(self::$db as $i => $v)$this->{self::$init['prefix'].$i}=$v;
	}
	public function var_uri() {
		self::$home=($lt=self::$z['URI'][self::$init[0]])?$lt:self::$init[1][0];
		foreach(self::$z['URI'] as $k => $v)if($k >= self::$init[0])$this->uri[]=$v;
	}
	public function var_user(){
		foreach(self::$user as $k => $v)$this->{"user_$k"}=$v;
	}
	public function mailing($to,$subject=NULL,$from=NULL,$template) {
		if(is::valid_email($to)):
			$eml_to 		= $to;
			$eml_from 		= ($from)?$from:self::$init['pro.email.noreply'];
			$eml_subject 	= ($subject)?$subject:self::$init['pro.name'];
			$eml_header 	= "From: {$eml_from}\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1";
			$eml_body 		= self::ztf($template);
			return (@mail($eml_to, $eml_subject, $eml_body, $eml_header))?true:false;
		endif;
	}
	public function ztl($tag,$key=false,$data=NULL){
		if($tag){
			$d = defined($tag)?constant($tag):$tag;
			return $key?$d:self::ztd($d,$data);
		}else{
			return 'html->content->Undefined!';
		}
	}
	public function ztf($t,$q=true,$data=NULL){
		static $f=array(),$k=array();
		if(!isset($f[$t])){
			$default=array('key'=>$t, 'dir'=>'dir.page.current', 'device'=>self::$init[3],'get'=>NULL);
			if(is_array($q)){
				$q+=$default;
			}elseif($q===false){
				$q=array_fill_keys(array_keys($default), NULL);
			}elseif($q===true){
				$q=$default;
			}else{
				$default['key']=is_string($q)?$q:$t;
				$q=$default;
			}
			$d=@self::$info[$q['dir']];
			$k[$t][0]=$q['key'];
			$i=$q['device'];
			if(isset($q['id']))$k[$t][1]=$q['id'];
			if($g=$q['get'])return($g===true)?$d.$t.$i:self::$z[1][$k[$t][0]]=$d.$t.$i;
			$f[$t]=($x=($i===true)?self::get_file_devising($d.$t):get::files($d.$t.$i))?file_get_contents($x):NULL;
		}elseif(is_string($q) and $k[$t] !=$q){
			$k[$t][0]=$q;
		}elseif(isset($q['id']) and $k[$t][1]!=$q['id']){
			self::$data[$k[$t][0]]=NULL;
			$k[$t][1]=$q['id'];
		}
		if(isset($f[$t][0])){
			//if($k[$t]=='d')echo 'value 2, ';
			if($k[$t][0] == 'get') return $f[$t];
				//elseif($key=$k[$t]) return self::$data[$key]=self::ztd($f[$t],$data);
				elseif(isset($k[$t][0]) && $key=$k[$t][0]) return self::$data[$key].=self::ztd($f[$t],$data);
					else return self::ztd($f[$t],$data);
		}else{
			return ($name=substr(strrchr($t, '/'), 1))?"file->template->{$name}->Notfound!":'file->template->Notprovided';
		}
	}
	public function ztd($template,$a=array()){
		return preg_replace_callback(self::$init[2],
			function($M)use($a){
				if(isset($a[$M[1]])) return $a[$M[1]];
					elseif(isset($this->{$M[1]})) return $this->{$M[1]};
					elseif(isset(self::$data[$M[1]])) return self::$data[$M[1]];
					elseif(isset(self::$info[$M[1]])) return self::$info[$M[1]];
					elseif(defined($M[1])) return $this->ztd(constant($M[1]));
					elseif(isset(self::$init[$M[1]])) return self::$init[$M[1]];
					elseif(ctype_upper($M[1]{0})) return $M[1];
						else return NULL;
				},$template
			);
	}
	public function zln($template,$a=array()){
		return preg_replace_callback(self::$init[2],
			function($M){
				$link=$M[1];
				return new html('a',$link, array('href'=>"?q=$link"));
				},$template
			);
	}
	public function ago($datetime, $full=false){
		$now=new DateTime;
		$ago=new DateTime($datetime);
		$diff=$now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array('y'=>'year','m'=>'month','w'=>'week','d'=>'day','h'=>'hour','i'=>'minute','s'=>'second');
		foreach($string as $k => &$v) {
			if($diff->$k){
				$v = $diff->$k . ' ' . self::ztl($v . ($diff->$k > 1 ? 's' : ''),true);
			}else{
				unset($string[$k]);
			}
		}
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' '.self::ztl('ago',true):self::ztl('just now',true);
	}
	/*REGISTER*/
	public function essential(){
		if(empty($_SESSION['sil']) || empty($_SESSION['sol'])  || empty($_SESSION['user.visited'])):
			if ($db=self::visited_fetch() and $db->total):
				$_SESSION['sil']=($db->sil)?$db->sil:self::$init['sil.default'];
				if(isset(self::$init['sol.list']))$_SESSION['sol']=($db->sol)?$db->sol:self::$init['sol.default']; else $_SESSION['sol']=0;
				$_SESSION['user.visited']=$db->visited;
			else:
				$_SESSION['sil'] = empty($_SESSION['sil'])?self::$init['sil.default']:$_SESSION['sil'];
				if(isset(self::$init['sol.list']))$_SESSION['sol']=empty($_SESSION['sol'])?self::$init['sol.default']:$_SESSION['sol'];
				$_SESSION['user.visited']=1;
				self::visited_insert($_SESSION['sil'],@$_SESSION['sol']);
			endif;
		endif;
	}
	public function SIL(){
		if(is_array($lists=self::$init['sil.list'])):
			if(array_key_exists(@$_GET['language'],$lists)):
				if($_GET['language'] != $_SESSION['sil']):
					$_SESSION['sil'] = $_GET['language'];
					self::visited_update('sil',$_SESSION['sil']);
				endif;
			endif;
			self::$init['sil.current'] 		= $_SESSION['sil'];
			self::$init['sil.current.name']	= $lists[$_SESSION['sil']];

			self::$info['dir.sil'] 			= self::$info['dir.language'].$_SESSION['sil'].'/';
			self::$info['count.sil'] 		= count($lists);
		endif;
	}
	public function SOL(){
		if(is_array($lists=self::$init['sol.list'])):
			if($sol=array_multi::value_exists(ucfirst(end($this->uri)),$lists,2)):
				if ($sol != $_SESSION['sol']):
					$_SESSION['sol'] = $sol;
					self::visited_update('sol',$sol);
				endif;
			endif;
			self::$init['sol.current'] 		= $_SESSION['sol'];
			self::$init['sol.current.name'] = array_multi::key_exists($_SESSION['sol'],$lists,2);
			self::$info['url.sol'] 			= self::$init['www'].self::$init['sol.url'].strtolower(self::$init['sol.current.name']);
			self::$info['count.sol'] 		= array_sum(array_map("count",$lists));
		endif;
	}
	public function visited_fetch(){
		return new sql("SELECT * FROM {$this->db_visited} WHERE ip='{$this->ip}'",'fetch_object');
	}
	public function visited_update($row,$value){
		new sql("UPDATE {$this->db_visited} SET {$row}='{$value}' WHERE ip='{$this->ip}'");
	}
	public function visited_counter(){
		$_SESSION['visited.time'] = $this->mdHis;
		new sql("UPDATE {$this->db_visited} SET visited=visited+1 WHERE ip='{$this->ip}'");
	}
	public function visited_insert($sil,$sol=NULL){
		$df = date("Y-n-j");
		$_SESSION['visited.time'] = $this->mdHis;
		new sql("INSERT INTO {$this->db_visited} SET ip='{$this->ip}',visited=1,dfirst='$df',sil='$sil',sol='$sol'");
	}
	/*SUPPORTS*/
	public function IP(){
		if(($_SESSION['visited.time']+3) < $this->mdHis):
			if(self::$init['iprecord'] == true)self::visited_counter();
			if($db=self::visited_fetch())$_SESSION['user.visited'] = $db->visited;
		endif;
		self::$data['user.visited'] = number_format($_SESSION['user.visited']);
	}
	public function http_referer(){
		if($ref_url=str_replace(self::$init['www'],'',strstr($this->http_referer,'//'), $is_referer)):
			if (isset($_SESSION[self::$info['page.link'].'m'])):
				if ($_SESSION[self::$info['page.link'].'m'] != $ref_url):
					if(self::$info['page.link'] != $ref_url):
						$_SESSION[self::$info['page.link'].'m'] = $ref_url;
						$_SESSION[self::$info['page.link']] = self::$init['www'].$ref_url;
					endif;
				endif;
			else:
				$_SESSION[self::$info['page.link'].'m'] = $ref_url;
				$_SESSION[self::$info['page.link']] = self::$init['www'].$ref_url;
			endif;
		else:
			$_SESSION[self::$info['page.link'].'m'] = self::$init[1][0];
			$_SESSION[self::$info['page.link']] = self::$init['www'];
		endif;
	}
	public function hits(){
		if((@$_SESSION['mdHis']+40) < $this->mdHis and $t=$this->db_visited)
			if($db=new sql("SELECT COUNT(id) AS tr, SUM(visited) AS td, f.dfirst AS df FROM (select dfirst from {$t} where ip=1) f, {$t}",'fetch_object') and $db->total):
				$_SESSION['mdHis'] = $this->mdHis;
				$_SESSION['total.visitor'] = $db->tr;
				$_SESSION['total.visited'] = $db->td;
				$_SESSION['first.visited'] = $db->df;
			endif;
		self::$data['total.visitor'] = number_format($_SESSION['total.visitor']);
		self::$data['total.visited'] = number_format($_SESSION['total.visited']);
		self::$data['first.visited'] = $_SESSION['first.visited'];
	}
	public function links(){
		self::$data['pro.link'] = self::link(self::$init['pro.name'],self::$init['www']);
		foreach(self::$init[1] as $i)
			if($d=self::$Page[$i] or $d=array_multi::page_search($i,self::$Page)):
				self::$data["www.$i"] = self::$init['www'].$d['page.link'];
				self::$data["link.$i"] = self::link($this->ztl($d['menu'],true),$d['page.link']);
			endif;
/*
			if($sol=array_multi::value_exists(ucfirst(end($this->uri)),$lists,2)):
				if ($sol != $_SESSION['sol']):
					$_SESSION['sol'] = $sol;
					self::visited_update('sol',$sol);
				endif;
			endif;
*/
	}
	public function ads(){
		foreach(self::$init['ads'] as $name => $v)self::$data["ads.$name"]=$v;
	}
	/*CONSIGNS*/
	public function MenuMain(){
		$profile=self::$init[1][4];
		$is_log='guest';
		if(isset(self::$user['id']) and isset(self::$Page[$profile])):
			self::$Page[$profile]['menu']=str::limit_char((self::$user['fullname'])?self::$user['fullname']:self::$user['username'], 23, '..');
			$is_log='user';
			self::$Page[$profile]['page.link']=self::$Page[$profile]['page.link'].'/'.self::$user['username'];
			if(is::valid_url(self::$user['site']))self::$Page=array_merge_recursive(array($profile=>
					array('site'=>array(
							'navigator'=>true,
							'menu'=>str::limit_char(str::extract_domain(self::$user['site']), 23, '..'),
							'page.link'=>self::$user['site'],
							'page.type'=>'user'
						)
					)
				),self::$Page);
		endif;
		self::$data['menu.user']=self::menu(self::$Page,$is_log);
		self::$data['menu.main']=self::menu(self::$Page,'page');
	}
	public function MenuLanguage(){
		if (is_array($sil_list = self::$init['sil.list'])):
			foreach($sil_list as $lang => $des):
			   $iscurrent = ($lang == self::$init['sil.current'])?"lang {$lang} current":"lang {$lang}";
			   $a = new html('a', $des, array('href' => "?language=$lang"));
			   $menu[] = new html('li', $a, array('class' => $iscurrent));
			endforeach;
			self::$data['menu.language'] = implode($menu);
		endif;
	}
	public function MenuSource(){
		if(is_array(self::$init['sol.list'])):
			$sol=$_SESSION['sol'];
			foreach(self::$init['sol.list'] as $group => $dict):
				$dictionary = array();
				foreach($dict as $k => $v):
					$dictionary['text'][]= array(
						'li'=>array(
							'text'=>array(
								'a'=>array(
									'text'=>array(
										'span'=>self::ztl($v), 'em'=>self::ztl('Myanmar')
									),
									'attr'=>array(
										'href'=>self::$init['www'].self::$init['sol.url'].strtolower($v)
									)
								)
							),
							'attr'=>array(
								'class'=>($k ==$sol)?"{$k} current":$k
							)
						)

					);
				endforeach;
				self::$data['menu.source'] .= new html('div',array(
						'text'=>array(
							'h2'=>self::ztl($group), 'ol'=>$dictionary
						)
					),
					array('class'=>($dict[$sol])?"{$group} current":$group)
				);
			endforeach;
		endif;
	}
	public function Copyright(){
		$YYYY = date("Y");
		if(self::$init['pro.released.year'] == true):
			self::$info['pro.year'] =($YYYY > self::$init['pro.released'])?self::$init['pro.released'].' - '.$YYYY:$YYYY;
		else:
			self::$info['pro.year'] = $YYYY;
		endif;
		self::$data['link.copyright'] = self::link(self::ztl('CopyrightName YEAR'),self::$Page['about-us']['page.link']);
	}
	/*FUNCTIONS*/

	/*COMMON*/
	public function ini_error($e=0){
		ini_set('error_reporting', $e);
	}
	public function load($d){
		if(is_array($d) or is_object($d))foreach($d as $k => $v)$this->{$k} = $v;
	}
	public function __set($name, $value){
		$this->{$name} = $value;
	}
	public function __get($name){
	   if(property_exists($this, $name)) return $this->{$name};
	}
    public function __call($name, $arguments) {
		if(method_exists($this, $name) == false) return $this->_call_underfind(__CLASS__,$name);
    }
	public function __toString(){
		return isset($this->tostring)?$this->tostring:$this->_call_private(__CLASS__,__METHOD__);
	}
    public function _call_underfind($class,$method){
		return str_replace(array('class','method'),array($class,$method),pro_method_not_exists);
    }
    public function _call_private($class,$method){
		return str_replace(array('class','method'),array($class,$method),pro_method_is_private);
    }
	public function _call_return_array($class,$method,$d){
		if(is_array($d))return str_replace(array('class','method','num'),array($class,$method,count($d)),pro_method_return_array);
	}
}