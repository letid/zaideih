<?php 
class upgrade extends storage
{
	public function home()
	{
	}
	public function visited()
	{
		$table=$this->db_visited;
		$d = date("Y-n-j");
		$sql = new sql("SELECT COUNT(id) AS visitor_count, SUM(visited) AS visited_count FROM $table","fetch_object");
		$query="INSERT INTO {$table} (ip,visited,dfirst,sil,sol) VALUES ('1', {$sql->visited_count}, '{$d}', '{$_SESSION['sil']}', '{$_SESSION['sol']}');";
				new sql("TRUNCATE TABLE $table;");
				new sql($query);
		$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>"Successfully reset: {$table}, visited {$sql->visited_count} with {$sql->visitor_count} visitors!"));
		return $z;
	}
	public function log()
	{
		/*
		$RT = array("mob"=>parent::$db['log']);
		$RA = @$_GET['t'];
		if (isset($RT[$RA])):
			$table 	= $RT[$RA];
			$sql 	= new sql("SELECT COUNT(id) AS log_count FROM {$table}","fetch_this");
					new sql("TRUNCATE TABLE {$table}");
			self::$data['pro.description'] = "Reset {$table} Table";
			self::$data['pro.msg'] = "Successfully reset: {$table}, there was {$sql->log_count} logs count!";
		else:
			self::$data['pro.description'] = "Reset cancelled!";
			self::$data['pro.msg'] = "[log] Probably you have no right to do this...";
		endif;
		*/
	}
}
?>