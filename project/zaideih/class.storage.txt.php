<?php 
class txt extends storage
{
	public function home()
	{
		$z['msg']="CK: txt!";
		$j=$this->dir_detail($this->prefix);
		$content=$this->getFileContent($j['object_path']);
		$z['zj'][]=$this->form($content,$j);
		$z['fid']=$this->fid;
		return $z;
	}
	public function post()
	{
		$is='Created';
		$j=$this->dir_detail($this->prefix);
		$filename=rawurldecode($_GET['file']);
		$file=$j['object_path'].'/'.$filename;
		if($fileContent = $_GET['content']){
			if($this->isFileExists($file))$is='Modified';
			$z['msg']=sprintf('%s: %s',$is,$filename);
			$this->writefile($file,$fileContent);
		}else{
			$z['msg']="Nothing to write";
		}
		return $z;
	}
	private function writefile($file,$content,$acl='public-read',$type='text/plain')
	{
		$options=array("gs"=>array('acl'=>$acl,'Content-Type'=> $type));
		$ctx = stream_context_create($options);
		file_put_contents($file, $content, 0, $ctx);
	}
	private function form($content,$j)
	{
		$j['p'][]=array('t'=>'p', 'd'=>array('title'=>'?'), 
			'l'=>array(
				array('t'=>'textarea', 'd'=>array('name'=>'content','html'=>$content))
			)
		);
		$j['p'][]=array('t'=>'p', 'd'=>array('title'=>'?'), 
			'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'text','name'=>'prefix','value'=>$j['dir']))
			)
		);
		$j['p'][]=array('t'=>'p', 'd'=>array('title'=>'?'), 
			'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'text','name'=>'file','value'=>$j['filename']))
			)
		);
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'submit'),'l'=>array(
				array('t'=>'input', 'd'=>array('type'=>'submit','name'=>'submit','value'=>'...Submit'))
			)
		);
		$this->fid='TXT-'.$j['object_md5'];
		$j['p'][]=array('t'=>'p', 'd'=>array('class'=>'msg','html'=>' '));
		return array('t'=>'form', 
			'd'=>array('class'=>'d1 file','id'=>$this->fid,'method'=>'get','action'=>'txt/post'), 
			'l'=>$j['p']
		);
	}
}
?>