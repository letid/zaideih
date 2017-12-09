<?php
class page extends zotune
{
	public function home()
	{
		$s = new sql("SELECT COUNT(LANG) AS total, LANG, SUM(PLAYS) AS PLAY FROM {$this->db_track} GROUP BY LANG ORDER BY PLAY DESC",'fetch_array');
		foreach($s->rows as $t => $d){
			$d['lang_name'] = self::$init['tracklanguages'][$d['LANG']];
			$d['lang_des'] = self::$init['tracklanguages_des'][$d['LANG']];
			parent::ztf('list.lang',true,$d);
		}
	}
	public function faq()
	{
		$db=new sql("SELECT * FROM $this->db_blog WHERE CATALOG=1 AND CONTENT IS NOT NULL AND REPLY IS NOT NULL ORDER BY MDATE DESC",'fetch_array');
		foreach($db->rows as $d){
			$this->faq_reply=implode(array_map(function($v){
				return parent::ztf('list.reply',array('key'=>false),array('reply'=>$v));
				},explode("\n", $d['REPLY'])));
				parent::ztf('list.faq',true,$d);
		}
	}
	public function contents()
	{
		/*
		self::$tmp["RequireMeta"] = false;
		self::$tmp['RequireMeta']['script']['fromclasspage'] = array("src"=>"", "type"=>"text/javascript");
		self::$tmp['RequireMeta']['script']['jquery'] = false;
		self::$info['tpl_final'] = true;

		parent::ztf(tpl,true,$d);
		parent::ztf(tpl,false);
		parent::ztf(tpl,name);
		parent::ztf(tpl,array(key,dir,device,get));
		*/
	}
	public function about_us()
	{
		/*
		$userid = array(1,2);
		$search = "SELECT id,fullname,profile,email FROM zu_users WHERE id=".implode(' OR id=',$userid);

		//echo $search;
		self::$data['liksk']='abc';
		$s= new sql($search,'fetch_array');
		self::$data['admin.profile']=NULL;
		foreach($s->rows as $i => $d){
			//echo $d['profile'];
			//$t,$d=true,$key=NULL,$data=NULL
			self::$data['admin.profile'].=$this->ztf(parent::$info['dir.page.current'].'profile.html',false,NULL,$d);
		}
		*/

		//print_r($s->rows);

	}
	public function contents_editor()
	{
		header('Content-Type: text/plain; charset=utf-8');
		//header('Content-Type: text/plain;');
		$file = isset($_GET['file'])?$_GET['file']:'editor.txt';
		$Tags= array('h2'=>'-','h3'=>'--','p'=>'---','li'=>'----');
		$file_contents = file_get_contents(self::$init['dir.project'].$file);
		$file_contents_clearn = array_clean(explode("\n",$file_contents));
		foreach($file_contents_clearn as $fcc){
			if(strlen(trim($fcc)))$Page[]=$fcc;
		}
		$d=array();
		foreach($Page as $i => $content){
			$t = explode('§',$content,2);
			$html = array_search(preg_replace('/[^\x20-\x7E]/','', $t[0]),$Tags);
			if(isset($_GET['get'])){
				$final_content = trim(str_replace("'","\'",$t[1]));
				echo "define('zp$i','$final_content');\n";
			}else{
				$dump= "<$html>{zp$i}</$html>\n";
				if($html == 'li'){
					$d[]=$dump;
				}else{
					if(count($d)>0){
						$f="<ul>\n".implode($d)."</ul>\n";
						$d=NULL;
					}else{
						$f=$dump;
					}
					echo $f;
				}
			}

		}
		if(isset($Page)){
		}
	}
}