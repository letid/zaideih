<?php 
class blog extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->action));
	}
	public function feedback()
	{
		$tags=array(
			'NAME'=>array('text'=>'Name','icon'=>'N','ifuser'=>true),
			'EMAIL'=>array('text'=>'E-mail','icon'=>'E','ifuser'=>true),
			'CONTENT'=>array('text'=>'Feedback for Zaideih','icon'=>'*')
		);
		if($this->obid)return $this->feedback_post();
		if($this->userid){
			$nickname=parent::$user['nickname'];
			$j['p'][]=array('t'=>'p','d'=>array('class'=>'msg','html'=>"Hi {$nickname}, Zaideih would love to hear anything you say about its feature and functionality. Please feel free to let us know what to be done!"));
		}else{
			$j['p'][]=array('t'=>'p','d'=>array('class'=>'msg','html'=>'Please feel free to let use know what it need to be done on Zaideih!'));
		}
		foreach($tags as $e => $i){
			if($e=='CONTENT'){
				$input=array('t'=>'textarea', 'd'=>array('name'=>$e));
			}else{
				$input=array('t'=>'input', 'd'=>array('type'=>'text','name'=>$e));
			}
			if($i['ifuser']==true and $this->userid){

			}else{
				$j['p'][]=array('t'=>'p', 'd'=>array('title'=>$i['icon']),
						'l'=>array(
							array('t'=>'label', 'd'=>array('html'=>$i['text'])), $input
						)
					);
			}
		}
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),
			'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Submit'))
			)
		);
		$z['zj'][]=array('t'=>'form', 'd'=>array('method'=>'get','action'=>"blog/feedback/$this->laid/post"), 'l'=>$j['p']);
		return $z;
	}
	private function feedback_post()
	{
		$error=true;
		$CONTENT=addslashes($_GET['CONTENT']);
		if(!$CONTENT){
			$error=false;
		}elseif($this->userid && $CONTENT){
			$error=false;
		}elseif($_GET['NAME'] || $_GET['EMAIL'] || $CONTENT){
			if(is_valid_email($_GET['EMAIL'])){
				$error=false;
			}
		}
		if($error==false && $this->feedback_check($CONTENT)==false){
			if ($this->userid){
				new sql("INSERT INTO $this->db_blog SET CATALOG=2, USER=$this->userid, CONTENT='$CONTENT', STATUS=0");
			}else{
				$NAME=addslashes($_GET['NAME']); $EMAIL=$_GET['EMAIL'];
				new sql("INSERT INTO $this->db_blog SET CATALOG=2, NAME='$NAME', EMAIL='$EMAIL', CONTENT='$CONTENT', STATUS=0");
			}
			$z['msg']='Thank you for your feedback!';
			
		}else{
			$z['msg']='An error found!';
		}
		return $z;
	}
	private function feedback_check($CONTENT)
	{
		$s=new sql("SELECT ID FROM $this->db_blog WHERE CONTENT LIKE '$CONTENT'");
		return($s->total)?true:false;
	}
}
?>