<?php
/*
new info('listOption',array("id"=> 2,"data" => $this->zotune->config['gender'],"def" => true));
new info('listOption',array("id"=> 2,"data" => $this->zotune->config['nametitle'],"def" => true));
new info('listBeh',array("id"=> 1));
new info('listZogam',array("id"=> 2));
new info('listCountry',array("code"=> 'NO'));

new info('getOption',array("id"=> 2,"data" => $this->zotune->config['gender'],"def" => true));
new info('getOption',array("id"=> 2,"data" => $this->zotune->config['nametitle'],"def" => true));
new info('getBeh',array("id"=> 2));
new info('getZogam',array("id"=> 454));
new info('getCountry',array("code"=> 'NO'));

new info("getUser",array("type"=> "getUser","userid" => 1));
$getUser->getUser_email;
*/
class info extends zotune
{
	private function q($t,$r,$v,$s,$c,$o)
	{
		$criterion 			= array('0'=>'q','1'=>'q%','2'=>'%q','3'=>'%q%');
		$criteria 			= isset($criterion[$c])?$criterion[$c]:$criterion[0];
		$v 					= str_replace("q",addslashes($v),$criteria);
		$operator 			= ($o)?$o:'=';
		if (is_array($s)):
			$rows = implode($s);
		elseif($s):
			$rows = $s;
		else:
			$rows = "*";
		endif;
		$this->table	= self::$db[$t];
		return "SELECT {$rows} FROM {$this->table} WHERE {$r} {$operator} '{$v}'";
	}
	public function has($vars)
	{
		//array("t","r","v","s","c","o","m","d");
		//array("table","row","value","select","criteria","operator","method","data");
		$table = $vars['t'];
		$row = @$vars['r'];
		$value = @$vars['v'];
		$select = @$vars['s'];
		$criteria = @$vars['c'];
		$operator = @$vars['o'];
		$method = @$vars['m'];
		$data = @$vars['d'];

		$db = new sql($this->q($table,$row,$value,$select,$criteria,$operator));
		$this->total = $db->total;
		if ($this->total):
			if ($method):
				$db->{$method}();
				if($data) $this->{$data} = $db->{$data};
			endif;
			return $this->total;
		else:
			return false;
		endif;
	}
	public function getUsers()
	{
		$db = new sql("SELECT * FROM {$this->db_users} WHERE id='{$this->user_id}'",'fetch_array');
		if($db->total)return $db->rows[0];
	}
	public function checkData($vars)
	{
		$table = $vars['table'];
		$row = $vars['row'];
		$value = $vars['value'];
		$db = new sql("SELECT * FROM {$table} WHERE $row='{$value}'",'fetch_array');
		if($db->total) return $db->{$row};
	}
	public function checkUsersData($vars)
	{
		$row = $vars['row'];
		$value = $vars['value'];
		$db = new sql("SELECT * FROM {$this->db_users} WHERE $row='{$value}'",'fetch_object');
		if($db->total)return $db->{$row};
	}
	public function form_option_nametitle($vars)
	{
	}
	public function form_option_beh($vars)
	{
		$db = new sql("SELECT * FROM {$this->db_beh} ORDER BY beh_min ASC",'fetch_array');
		return $this->form_select_option($db->rows,'beh_min','beh_id',$vars['beh_id']);
	}
	public function form_option_zogam($vars)
	{
		$db = new sql("SELECT * FROM {$this->db_zogam} ORDER BY zogam_min ASC",'fetch_array');
		return $this->form_select_option($db->rows,'zogam_min','zogam_id',$vars['zogam_id']);
	}
	public function form_option_country($vars)
	{
		$db = new sql("SELECT * FROM {$this->db_countries} ORDER BY country_name ASC",'fetch_array');
		return $this->form_select_option($db->rows,'country_name','country_code',$vars['country_code']);
	}
	public function get_beh($id)
	{
		$db = new sql("SELECT beh_min FROM {$this->db_beh} WHERE beh_id ='{$id}'",'fetch_object');
		if($db->total) return $db->beh_min;
	}
	public function get_zogam($id)
	{
		$db = new sql("SELECT zogam_min FROM {$this->db_zogam} WHERE zogam_id ='{$id}'","fetch_object");
		if($db->total) return $db->zogam_min;
	}
	public function get_country($code)
	{
		$db = new sql("SELECT country_name FROM {$this->db_countries} WHERE country_code='{$code}'","fetch_object");
		if($db->total) return $db->country_name;
	}
}