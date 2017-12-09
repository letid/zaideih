<?php
class api extends zotune
{
	private $allowed = array(
	'localhost',
	'www.zaideih.com','zaideih.com','beta.zaideih.com','v3.zaideih.com','beta.zotune-zaideih.appspot.com',
	'localhost:8080','10.0.0.129','yoursol',
	'zotune.com','zaideih.zotune.com','developer.zotune.com',
	'zaideih.lethil.me','zaideih.local','zaideih.zomi.today',
	'nahnuai.com','zaideih.nahnuai.com'
	);
	public $genreList=array(1=>'Alternative',2=>'Blues',3=>'Classic',4=>'Country',5=>'Dance',6=>'Electronic',7=>'Folk',8=>'Hip hop',9=>'Instrumental',10=>'Jazz',11=>'Latin',12=>'Metal',13=>'Newage',14=>'Pop',15=>'Punk',16=>'R&B',17=>'Rap',18=>'Reggae',19=>'Rock',20=>'Soul',21=>'Techno',22=>'Vocal');
	public $zolaNam=array(1=>'Pasian Phatnala',2=>'Zawl-la',3=>'Zolapi',4=>'Gamla',5=>'Minamla',6=>'Guallelhla',7=>'Pasian Phatna Latom ',8=>'Zawl-la Lui',9=>'La Lui',10=>'Gualzawhla',11=>'Theihloh/Adang');
	public $statusList=array(1=>'...is not playing?',2=>'...is not playing at all?',3=>'...has incorrect information?',4=>'...need a better audio quality?',5=>'...is Okey!');
	public $genreChristian=array(1=>'Gospel',2=>'Praise & Worship',3=>'Hymnology',4=>'Prayer',4=>'Rhymning spiritual');

	public function home()
	{
		$page=$this->uri[1];
		if(in_array($this->name_referer, $this->allowed) || isset($_GET['deploy'])){
			if($api=get::files(self::$info['dir.project']."class.api.$page.php")){
				self::ini_error();
				require_once $api;
				$z 				= new $page();
				$z->page 		= $page;
				$z->action 		= $this->uri[2];
				$z->laid 		= $this->uri[3];
				$z->obid 		= $this->uri[4];

				$z->userid 		= parent::$user['id'];
				$z->level 		= parent::$user['level'];
				$z->zris 		= $_SERVER['HTTP_ZRIS'];
				$z->zrid 		= $_SERVER['HTTP_ZRID'];
				self::$data['page.data'] = $z->home();
			}
		}else{
			$this->noAuthorization(array('d'=>$this->name_referer));
		}
	}
	public function getSuggestion($id)
	{
		switch($this->obid){
			case 'none': break;
			case 'all': //$this->row=new sql("SELECT * FROM $this->db_suggestion");
				break;
			case 'track__': //$this->row=new sql("SELECT * FROM $this->db_suggestion WHERE TRACK='$this->laid'",'fetch_array');
				break;
			case 'user': //$this->row=new sql("SELECT * FROM $this->db_suggestion WHERE USER='$this->userid'");
				break;
			default:
				$where =($this->userid)?"USER='$this->userid'":"IP='$this->ip'";
				$s=new sql("SELECT * FROM $this->db_suggestion WHERE CODE='$id' AND $where",'fetch_object');
				if ($s->total){
					$this->msg = $s;
					$this->ID = $s->ID;
					$this->TAG = json_decode($s->TAG,true);
					$this->PRIVACY = json_decode($s->PRIVACY,true);
					$this->GENRE = $s->GENRE;
					$this->CHRISTIAN = $s->CHRISTIAN;
					$this->AUDIO = $s->AUDIO;
					$this->ZOLANAM = $s->ZOLANAM;
					$this->DATE = $s->DATE;
					$this->STATUS = $s->STATUS;
				}
		}
	}
	public function blog()
	{
		//CATALOG faq=1,feedback=2
	}
	public function postSuggestion($q,$id,$catalog=1)
	{
		if($this->ID){
			//CATALOG 1=TRACK, 2=ARTIST,3=ALBUM
			return new sql("UPDATE $this->db_suggestion SET $q WHERE ID=$this->ID");
		}else{
			if($this->userid){
				$is = "USER='$this->userid'";
			}else{
				$is = "IP='$this->ip'";
			}
			return new sql("INSERT INTO $this->db_suggestion SET $is, CATALOG=$catalog,CODE='$id', $q, STATUS=0");
		}
	}
	public function getAlbum($id)
	{
		$s=new sql("SELECT t.*, a.PATH AS DIR FROM $this->db_track AS t LEFT JOIN $this->db_album AS a ON a.UNIQUEID=t.UNIQUEID WHERE t.UNIQUEID='$id'",'fetch_array');
		if ($s->total):
			foreach($s->rows as $d):
				$mp3 = self::$data['www.api'].'/audio/play/'.$d['ID'];
				$z[] = array('id'=>$d['ID'],'title'=>$d['TITLE'],'artist'=>$d['ARTIST'],'mp3'=>$mp3);
			endforeach;
			return $z;
		endif;
	}
	public function downloadInfo($user,$laid)
	{
		$this->download=new sql("SELECT * FROM $this->db_download WHERE USER=$user AND TRACK=$laid",'fetch_object');
		if($this->download->total) return ($this->download->DOWNLOAD >= 1)?$this->download->DOWNLOAD:false;
	}
	public function downloadUpdate($id,$is)
	{
		return new sql("UPDATE $this->db_download SET $is=$is+1 WHERE ID=$id");
	}
	public function downloadInsert($user,$laid,$paid,$is)
	{
		return new sql("INSERT INTO $this->db_download SET USER=$user, TRACK=$laid, PAID=$paid, $is=$is+1");
	}
	public function creditInfo($user,$cost=0)
	{
		$this->credit=new sql("SELECT SUM(F.AMOUNT) AS Total, SUM(F.USED) AS Used, SUM(F.AMOUNT-F.USED) AS Amount, (SELECT SUM(PAID) AS PAID FROM $this->db_download WHERE USER = F.USER) AS Paid FROM $this->db_financial AS F WHERE F.USER=$user GROUP BY F.USER",'fetch_object');
		if($this->credit->total){
			$this->creditAmount=$this->credit->Amount;
			$this->creditPaid=$this->credit->Paid;
			$this->creditBalance=$this->creditAmount - $this->creditPaid;
			if($this->creditBalance >= $cost) return $this->creditBalance;
		}
	}
	public function isAuthorized($userid)
	{
		if($this->userid){
			if($this->isAuthorization('lyric') or $userid == $this->userid){
				return true;
			}
		}
	}
	public function trackInfo($laid,$d=array())
	{
		$this->track=new sql("SELECT T.*,A.PATH AS DIR,GROUP_CONCAT(A.PATH,'/',T.PATH) AS URL FROM $this->db_track AS T LEFT JOIN $this->db_album AS A ON A.UNIQUEID=T.UNIQUEID WHERE T.ID=$laid",'fetch_object');
		if ($this->track->total){
			$this->trackName=$this->track->PATH;
			$this->trackUrl=self::$init['music.server'].$this->trackPaths($this->track->URL);
			if(isset($d['p']))$this->trackPlays($this->track->ID);
			if(isset($d['s']))$this->trackSize=filesize($this->trackUrl);
			if(isset($d['t']))$this->trackMtime=date('r',filemtime($this->trackUrl));
		}
	}
	public function trackUpdate($laid,$d)
	{
		return new sql("UPDATE $this->db_track SET $d WHERE ID=$laid");
	}
	public function trackPlays($id)
	{
		return new sql("UPDATE $this->db_track SET PLAYS=PLAYS+1 WHERE ID=$id");
	}
	public function trackPaths($d,$r='rawurldecode')
	{
		if(is_array($d))$d=implode('/',$d);
		return implode("/", array_filter(array_map($r, explode("/",$d))));
	}
}
?>