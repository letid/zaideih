<?php
class audio extends api
{
	private $cache_length = 60;
	public function home()
	{
		$this->cache_expire = gmdate("D, d M Y H:i:s", time() + $this->cache_length);
		self::trackInfo($this->laid,array('p'=>true,'s'=>true));
		return call_user_func(array($this,$this->uri[2]));
	}
	public function play__()
	{
		/*
		$opts = array(
		  'http' => array(
			'method' => 'GET',
			'header' => "Content-Type: audio/mp3\r\n" .
						"Accept-Ranges: bytes\r\n".
						"Content-Length: 1\r\n"
		  )
		);
		$context = stream_context_create($opts);
		fopen($this->trackUrl, 'rb',false,$context)
		*/
		if($stream = fopen($this->trackUrl, 'rb')){
			header('Accept-Ranges: bytes');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: $this->trackSize');
			header('Content-Type: audio/mp3');
			header('Transfer-Encoding: chunked');
			header("Cache-Control: public, max-age=$this->cache_length, must-revalidate");
			header("Expires: $this->cache_expire");
			header('Pragma: cache');
			header("Cache-Control: max-age=$this->cache_length");
			header("User-Cache-Control: max-age=$this->cache_length");
			fpassthru($stream);
		}else{
			return "Unable to open {$this->laid} remote stream {$this->trackUrl}!";
		}
	}
	public function download()
	{
		if(isset($_SESSION['download']) || $_SESSION['download']==$this->laid){
			unset($_SESSION['download']);
			$this->trackName=rawurlencode($this->track->TITLE.'.mp3');
			header("Expires:$this->cache_length");
			header("Pragma:cache");
			header("Accept-Ranges:bytes");
			header("Cache-Control:public,max-age=$this->cache_length");
			header("User-Cache-Control:max-age=$this->cache_length");

			header("Content-Transfer-Encoding: Binary");
			header("Content-length: $this->trackSize");
			header("Content-disposition: attachment; filename=$this->trackName");
			readfile($this->trackUrl);
		}else{
			parent::noAcess();
		}
	}
	public function audio_fopen()
	{
		set_time_limit(0);
		$strContext=stream_context_create(array('http'=>array('method'=>'GET','header'=>"Accept-language: en\r\n" )));
		$fpOrigin=fopen($this->track, 'rb', false, $strContext);

		header("Content-type:audio/mpeg");
		header("Content-Length: $this->tracksize");
		header("Accept-Ranges:bytes");

		header("Cache-Control:public, max-age=$this->cache_length");
		header("Expires:$this->cache_expire");
		header("Pragma:cache");
		header("Cache-Control:max-age=$this->cache_length");
		header("User-Cache-Control:max-age=$this->cache_length");

		while(!feof($fpOrigin)){
		  $buffer=fread($fpOrigin, 4096);
		  echo $buffer;
		  flush();
		}
		fclose($fpOrigin);
	}
	public function audio_header()
	{
		ob_start();
			header("Location:$this->track");
			//header("Content-type:application/octet-stream");
			header("Content-Length:1");
			header("Transfer-Encoding:chunked");
			//header("Expires:$this->cache_length");
			header("Pragma:cache");
			header("Accept-Ranges:bytes");
			//header("Cache-Control:public,max-age=$this->cache_length");
			//header("User-Cache-Control:max-age=$this->cache_length");
		ob_end_clean();
	}
    public function play()//_readfile
	{
		header("Accept-Ranges:bytes");
			header("Content-type:audio/mpeg");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: $this->trackSize");
			header("Expires:$this->cache_length");
			header("Pragma:cache");
			header("Cache-Control:public,max-age=$this->cache_length");
			header("User-Cache-Control:max-age=$this->cache_length");
			//header("Content-disposition: attachment; filename=$this->trackname");
			readfile($this->trackUrl);
	}
}