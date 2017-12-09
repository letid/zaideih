<?php
class google_translate_api
{
	var $googleapis = 'https://www.googleapis.com/language/translate/v2';
	protected $api_key;

	public function __construct($key)
	{
		$this->api_key = $key;
	}
	public function translate($q,$source=NULL,$target)
	{
		$parameters = array(
			'key'    => $this->api_key,
			'target' => $target,
			'q'      => $q //rawurlencode($q)
		);
		if($source)$parameters['source']=$source;
		$parameter = http_build_query($parameters);
		$json = json_decode(@file_get_contents($this->googleapis.'?'.$parameter),true);
		$this->text = @$json['data']['translations'][0]['translatedText'];
		if ($this->text):
			$this->lang = @$json['data']['translations'][0]['detectedSourceLanguage'];
			return true;
		endif;
		/*
		$d = curl_init($this->googleapis);
		//curl_setopt($d, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($d, CURLOPT_HEADER, 0);
		curl_setopt($d, CURLOPT_POSTFIELDS, $parameter);
		$data = curl_exec($d);
		curl_close($d);
		*/
	}
}
?>