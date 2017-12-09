<?php
class mp3 extends storage
{
	private $mp3_requirement=array(
			'PATH'=>array('text'=>'(url)','sql'=>true),
			'UNIQUEID'=>array('text'=>'(id)','sql'=>true),
			'TITLE'=>array('text'=>'(varchar)','id3'=>'TIT2','sql'=>true),
			'ARTIST'=>array('text'=>'(varchar)','id3'=>'TPE1','sql'=>true),
			'ALBUM'=>array('text'=>'(varchar)','id3'=>'TALB','sql'=>true),
			'PART_OF_A_SET'=>array('text'=>'(num)','id3'=>'TPOS'),
			'BAND'=>array('text'=>'(varchar)','id3'=>'TPE2'),
			'TRACK'=>array('text'=>'(num)','id3'=>'TRCK','sql'=>true),
			'YEAR'=>array('text'=>'(yyyy)','id3'=>'TYER','or'=>'TYE','sql'=>true),
			'GENRE'=>array('text'=>'(varchar)','id3'=>'TCON','sql'=>true),
			'COMPOSER'=>array('text'=>'(varchar)','id3'=>'TCOM'),
			'ENCODED_BY'=>array('text'=>'(varchar)','id3'=>'TENC'),
			'COPYRIGHT'=>array('text'=>'(varchar)','id3'=>'TCOP'),
			'PUBLISHER'=>array('text'=>'(varchar)','id3'=>'TPUB'),
			'ORIGINAL_ARTIST'=>array('text'=>'(varchar)','id3'=>'TOPE'),
			'TYPE'=>array('text'=>'(num)','sql'=>true),
			'LENGTH'=>array('text'=>'(num){00:00}','sql'=>true),
			'PLAYS'=>array('text'=>'(num)','sql'=>true),
			'COMMENT'=>array('text'=>'(text)','type'=>'textarea','id3'=>'COMM','sql'=>true),
			'LANG'=>array('text'=>'(num)','sql'=>true),
			'USER_TEXT'=>array('text'=>'(varchar)','type'=>'textarea','id3'=>'TXXX'),
			'URL_USER'=>array('text'=>'(varchar)','id3'=>'WXXX'),
			'STATUS'=>array('text'=>'(num)','sql'=>true),
			'BITRATE'=>array('text'=>'(num){dynamic}')
		);
	public function home()
	{
		$this->post_directly=($this->action=='submit')?true:false;
		$j=$this->dir_detail($this->prefix);

		//$dir=self::$init['music.server'].'/'.$j['dir'];
		$mp3=$this->isAlbum(array(
			'object_path'=>$j['dir_path'],
			'object'=>$j['dir'],
			'object_md5'=>$j['dir_md5']
		));
		if($mp3){
			if(in_array($j['filename'],$mp3)){
				$list[]=$this->helper($j['dir_path'],$j['filename']);
				$z['zj'][]=$this->form($list,$j['object_md5']);
				$z['fid']=$this->fid;
				$z['msg']=sprintf('CK: (cm) (%s/%s/%s) Ok!',count($mp3),count($this->sql_task['INSERT']),count($this->sql_task['UPDATE']));
			}else{
				$z['msg']='CK: (cm) no selected mp3!';
			}
		}else{
			$z['msg']='CK: (cm) no mp3!';
		}
		return $z;
	}
	public function tag()
	{
		$this->post_directly=($this->action=='submit')?true:false;
		$j=$this->dir_detail($this->prefix);
		//$dir=self::$init['music.server'].'/'.$j['dir'];
		$this->object_path=rawurlencode($j['object']);
		$mp3=$this->isAlbum(array(
			'object_path'=>$j['dir_path'],
			'object'=>$j['dir'],
			'object_md5'=>$j['dir_md5']
		));
		if($mp3){
			if(in_array($j['filename'],$mp3)){
				$list[]=$this->helper($j['dir_path'],$j['filename'],'id3');
				$z['zj'][]=$this->TagForm($list,$j['object_md5'],'tagPost');
				$z['fid']=$this->fid;
				$z['msg']=sprintf('CM: (%s/%s/%s) Ok!',count($mp3),count($this->sql_task['INSERT']),count($this->sql_task['UPDATE']));
			}else{
				$z['msg']='CM: no selected mp3!';
			}
		}else{
			$z['msg']='CM: no mp3!';
		}
		return $z;
	}
	public function tagPost($DirectData=NULL)
	{
		$j=$this->dir_detail($this->prefix);
		$file=$j['object_path'];

		if(is_array($DirectData)){
			$data=$DirectData;
		}elseif($ddf=json_decode($_POST['data'],true)){
			foreach($ddf as $e => $d)$j[$d['name']][]=$d['value'];
			foreach($j as $e => $b){
				foreach($b as $i=> $d)$data[$i][$e][]=$d;
			}

			//$z['msg']="postTag ".json_encode($j);
		}

		foreach($data as $d){
			//$set_query=get::query(array_filter($d));
			$z['msg']=$this->writer($file,array_filter($d));
		}
		//
		/*
		if($error){
			$z['msg']=implode(', ',$error).' error!';
		}else{
			$UT=count($this->sql_task['UPDATE']);
			$IT=count($this->sql_task['INSERT']);
			$z['msg']="Update:{$UT}, Insert:{$IT} success...";
		}
		*/
		//$z['msg']=$this->writer($file,NULL);

		//$z['msg']="postTag ".json_encode($data);
		//print_r($data);
		return $z;
	}
	public function album()
	{
		$j=$this->dir_detail($this->prefix);
		$this->post_directly=($this->option=='submit')?true:false;
		if($mp3=$this->isAlbum($j)){
			foreach($mp3 as $file)$audio[]=$this->helper($j['object_path'],$file);
			$z['zj'][]=$this->form($audio);
			$z['fid']=$this->fid;
			$z['msg']=sprintf('CM: (%s/%s/%s) Ok!',count($mp3),count($this->sql_task['INSERT']),count($this->sql_task['UPDATE']));
		}else{
			$z['msg']='CM: no mp3!';
		}
		return $z;
	}
	private function helper($dir,$file,$is='sql')
	{
		$tag=$this->reader($dir.'/'.$file);
		$tag['PATH']=$file;
		$tag['TITLE']=($tag['TITLE'])?$tag['TITLE']:pathinfo($file, PATHINFO_FILENAME);
		//$tag['TRACK']=$tag['TRACK_NUMBER'];
		foreach($this->mp3_requirement as $i => $d){
			if(isset($d[$is])){
				//$name=strtolower($n=$v['id3']);
				$data[0][$i]=isset($tag[$i])?$tag[$i]:$this->album_data[$i];
				$name=$i.'[]';
				if(isset($d['type'])){
					$input=array('t'=>$d['type'], 'd'=>array('name'=>$i,'html'=>$data[0][$i]));
				}else{
					$input=array('t'=>'input', 'd'=>array('type'=>'text','name'=>$i,'value'=>$data[0][$i]));
				}
				$j[]=array('t'=>'p', 'd'=>array('title'=>$i),
					'l'=>array(
						$input,array('t'=>'label', 'd'=>array('html'=>$d['text']))
					)
				);
			}
		}
		if($this->post_directly==true){
			$m=$this->post($data);
			$this->mp3_msg=$m['msg'];
		}
		return array('t'=>'div','d'=>array('class'=>'abc'),  'l'=>$j);
	}
	public function post($DirectData=NULL)
	{
		if(is_array($DirectData)){
			$data=$DirectData;
		}elseif($ddf=json_decode($_POST['data'],true)){
			foreach($ddf as $e => $d){
				$name=$d['name'];
				$j[$name][]=$d['value'];
			}
			foreach($j as $e => $b){
				foreach($b as $i=> $d){
					$data[$i][$e]=$d;
				}
			}
		}
		foreach($data as $d){
			$set_query=get::query(array_filter($d));
			$UNIQUEID=$d['UNIQUEID'];
			$PATH=addslashes($d['PATH']);
			$s=new sql("SELECT * FROM $this->db_track WHERE PATH='$PATH' AND UNIQUEID='$UNIQUEID'");
			if ($s->total){
				$query = "UPDATE $this->db_track SET $set_query WHERE PATH='$PATH' AND UNIQUEID='$UNIQUEID'";
			} else {
				$query = "INSERT INTO $this->db_track SET $set_query";
			}
			$msg=new sql($query);
			$message=strtok($query," ");
			if(is_numeric($msg->msg)){
				$this->sql_task[$message][]=$msg->msg;
			}else{
				$error[]=$msg->msg;
			}
		}
		if($error){
			$z['msg']=implode(', ',$error).' error!';
		}else{
			$UT=count($this->sql_task['UPDATE']);
			$IT=count($this->sql_task['INSERT']);
			$z['msg']="Update:{$UT}, Insert:{$IT} success...";
		}
		return $z;
	}
	private function form($list,$fid=NULL,$subin='post')
	{
		$j['p']=$list;
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Submit'))
			)
		);
		$this->fid='MP3-'.($fid?$fid:$this->album_data['UNIQUEID']);
		$msg=($this->mp3_msg)?$this->mp3_msg:count($list).' tracks found!';
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>$msg));
		return array('t'=>'form',
			'd'=>array('class'=>'d1 mp3','id'=>$this->fid,'method'=>'post','action'=>"mp3/$subin"),
			'l'=>$j['p']
		);
	}
	private function TagForm($list,$fid=NULL)
	{
		$j['p']=$list;
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Submit'))
			)
		);
		$this->fid='MP3TAG-'.($fid?$fid:$this->album_data['UNIQUEID']);
		$msg=($this->mp3_msg)?$this->mp3_msg:count($list).' tracks found!';
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>$msg));
		return array('t'=>'form',
			'd'=>array('class'=>'d1 mp3','id'=>$this->fid,'method'=>'post','action'=>"mp3/tagPost?prefix=".$this->object_path),
			'l'=>$j['p']
		);
	}
	private function reader($file)//getid3_mp3(
	{
		$getID3=new getID3;
		$ID3=$getID3->analyze($file);
		$d=$ID3['tags'];
		if(($t=$d['id3v2']) || ($t=$d['ape']) || ($t=$d['id3v1']))foreach($t as $n =>$i)$tag[strtoupper($n)]=$i[0];
		$tag['LENGTH']=$ID3['playtime_string'];
		$tag['BITRATE']=$ID3['bitrate'];
		$tag['TRACK']=$tag['TRACK_NUMBER'];
		//$tag['DISCNUMBER']=$tag['PART_OF_A_SET'];//DISC_NUMBER,PART_OF_SET
		//$tag['ENCODEBY']=$tag['ENCODED_BY'];
		$tag['COPYRIGHT']=$tag['COPYRIGHT_MESSAGE'];
		//$tag['ORIGINALARTIST']=$tag['ORIGINAL_ARTIST'];
		//$tag['URL']=$tag['URL_USER'];
		return $tag;
	}
	private function writer($file,$data)
	{
		//$this->ini_error(-1);
		require_once(self::$info['dir.class'].'getid3/write.php');

		$Encoding = 'UTF-8';
		//$getID3 = new getID3;
		//$getID3->setOption(array('encoding'=>$Encoding));

		$tagwriter = new getid3_writetags;
		$tagwriter->filename = $file;

		//$tagwriter->tagformats = array('id3v1', 'id3v2.3');
		$tagwriter->tagformats = array('id3v2.3');

		// set various options (optional)
		$tagwriter->overwrite_tags = true;
		$tagwriter->tag_encoding = $Encoding;
		$tagwriter->remove_other_tags = true;
		$tagwriter->tag_data = $data;

		// write tags
		if ($tagwriter->WriteTags()) {
			return 'Successfully wrote tags';
			if (!empty($tagwriter->warnings))return 'There were some warnings: '.implode(' ', $tagwriter->warnings);
		} else {
			return 'Failed to write tags: '.implode(' ', $tagwriter->errors);
		}

	}
}