<?php 
class star extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->action));
	}
	public function get()
	{
		if ($this->userid):
			$s=new sql("SELECT STAR FROM $this->db_favorite WHERE TRACK='$this->laid' AND USER=$this->userid",'fetch_this');
			if ($s->total):
				$favorite = (int)$s->rows['STAR'] + 1;
				if (count(parent::$init['trackfavorite']) <= $favorite):
					$z['star'] = parent::$init['trackfavorite'][0];
					new sql("DELETE FROM $this->db_favorite WHERE TRACK='$this->laid' AND USER=$this->userid");
				else:
					$z['star'] = parent::$init['trackfavorite'][$favorite];
					new sql("UPDATE $this->db_favorite SET STAR=$favorite WHERE TRACK='$this->laid' AND USER=$this->userid");
				endif;
			else:
				$z['star'] = parent::$init['trackfavorite'][1];
				$i=new sql("INSERT INTO $this->db_favorite SET TRACK='$this->laid', USER=$this->userid, STAR=1");
				//$z['msg']=$i->msg;
			endif;
		else:
			$z['star'] = parent::$init['trackfavorite'][0];
		endif;
		return $z;
	}
}
?>