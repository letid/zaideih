<?php 
class download extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->action));
	}
	public function check()
	{
		$this->trackInfo($this->laid);
		$this->trackCost=self::$init['trackcost'][$this->track->LANG];
		if($this->isOk() == true){
			$_SESSION['download']=$this->laid;
			$z['url']=self::$data['www.api'].'audio/download/'.$this->laid;
			$z['fn']='audio';
			if($this->creditBalance){
				$msg="Downloading {$this->track->TITLE}, your credits balance is {$this->creditBalance}...";
			}else{
				$msg="Downloading {$this->track->TITLE}, {$this->download->DOWNLOAD} counting...";
			}
			$again[]=array('t'=>'span', 'd'=>array('class'=>'fn','data-role'=>'download again','html'=>'....Download Again...'));
		}else{
			if($this->creditBalance){
				$msg="Osp! your credits balance is {$this->creditBalance} and you need {$this->trackCost} credits to download {$this->track->TITLE}...";
			}elseif($this->userid){
				$msg="Osp! you need credits to download {$this->track->TITLE}...";
			}else{
				$msg="Osp! its required credits to download {$this->track->TITLE}...";
			}
		}
		//$msg='To be able to download » Dawimangpa, please verify your email or update your profile. E-mail verification allow you to download instant. You can also download limited tracks by updating your profile, however it\'s necessary to make approval by one of our admin. | Permission denied ';
		$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'dw','text'=>$msg), 'l'=>$again);
		return $z;
	}
	public function isOk()
	{
		$i='DOWNLOAD';
		$ok=false;
		$cost='0';
		if($this->userid){
			if($this->downloadInfo($this->userid,$this->laid)){
				$ok=true;
			}else if($this->creditInfo($this->userid,$this->trackCost)){
				$ok=true;
				$cost=$this->trackCost;
				$this->creditBalance=$this->creditBalance - $this->trackCost;
			}else{
				$i='ATTEMPT';
			}
			if($this->download->ID){
				$q="UPDATE $this->db_download SET $i=$i+1 WHERE ID={$this->download->ID}";
			}else{
				$q="INSERT INTO $this->db_download SET USER=$this->userid, TRACK=$this->laid, PAID=$cost, $i=1";
			}
			new sql($q);
			return $ok;
		}
	}

}
?>