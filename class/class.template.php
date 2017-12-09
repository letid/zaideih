<?php
class template extends zotune
{
	var $tpl_prefix = 'html'; //prefix
	var $tpl_final = false; //isfinal
	var $tpl_pattern = tpl_pattern_sign; //'/[{](.*?)[}]/e','/[$](.*?)[;]/e'
	public function language($language,$key=NULL,$devising=true)
	{
		if($language):
			$output = $this->generator(defined($language)?constant($language):$language);
			if ($key) $this->{$key} = $output;
			return $output;
		else:
			return "language->text->Empty!";
		endif;
	}
	public function tag($tag,$key=NULL,$devising=true)
	{
		if($tag):
			$output = $this->generator($tag);
			if($key)$this->{$key} = $output;
			return $output;
		else:
			return "html->content->Undefined!";
		endif;
	}
	public static function html($template,$key=NULL,$devising=true)
	{
		if($template):
			if($devising == true):
				$is_template = $this->get_file_devising($template,@$_SESSION['tpl'],$this->tpl_prefix);
			else:
				$is_template = $template;
			endif;
			if($is_template):
				$output = $this->generator(file_get_contents($is_template));
				if ($key) $this->{$key} = $output;
				return $output;
			else:
				$tpl_tmp = explode("/",$template);
				$tpl = end($tpl_tmp);
				return "file->template->{$tpl}->Notfound!";
			endif;
		endif;
	}
	private function generator($tpl)
	{
		// REMOVE THIS ON PHP > 5.3
		$thie = $this;
		return preg_replace_callback(
				$thie->tpl_pattern,
				function($M)use($thie) {
					if(isset($thie->{$M[1]})):
						return $thie->{$M[1]};
					elseif(isset($thie->config[$M[1]])):
						return $thie->config[$M[1]];
					elseif(defined($M[1])):
						$con_data = constant($M[1]);
						return preg_replace_callback(
								$thie->tpl_pattern,
								function($L)use($thie) {
									return $thie->{$L[1]}?$thie->{$L[1]}:@$thie->config[$L[1]];
								}, $con_data
							);
					elseif(ctype_upper($M[1]{0})):
						return $M[1];
					else:
						return ($thie->tpl_final)?NULL:$M[0];
					endif;
				}, $tpl
			);
	}
}