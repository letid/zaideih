<?php 
class other extends api
{
	public function home()
	{
		return call_user_func(array($this,$this->action));
	}
	public function track()
	{
		
		$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'b1 msg','data-icon'=>'?','html'=>'...are there something wrong? Zaideih is much delighted to be inform on any track that does not play or not playing properly...'));
		if($this->isAuthorization('tag'))$j['li'][]=array('t'=>'li', 'l'=>array(
			array('t'=>'a', 
				'd'=>array('href'=>"track/editor/$this->laid",'class'=>'CON zA', 'data-role'=>'form',
				'html'=>'....Tag Editor'))
			)
		);
		/*
		if($this->isAuthorization('tag'))$j['li'][]=array('t'=>'li', 'l'=>array(
			array('t'=>'a', 
				'd'=>array('href'=>"other/albumeditor/$this->laid",'class'=>'CON zA', 'data-role'=>'form',
				'html'=>'....Album Editor'))
			)
		);
		if($this->isAuthorization('tag'))$j['li'][]=array('t'=>'li', 'l'=>array(
			array('t'=>'a', 
				'd'=>array('href'=>"other/artisteditor/$this->laid",'class'=>'CON zA', 'data-role'=>'form',
				'html'=>'....Artist Editor'))
			)
		);
		
		if($this->isAuthorization('privacy'))$j['li'][]=array('t'=>'li', 'l'=>array(
			array('t'=>'a', 
				'd'=>array('href'=>"other/trackeditor/$this->laid",'class'=>'CON zA', 'data-role'=>'form',
				'html'=>'check Privacy report'))
			)
		);
		*/
		$j['li'][]=array('t'=>'li', 'l'=>array(
			array('t'=>'a', 
				'd'=>array('href'=>"blog/feedback/$this->laid",'class'=>'CON zA', 'html'=>'....Feedback for Zaideih'))
			)
		);
		$j['li'][]=array('t'=>'li', 'l'=>array(
			array('t'=>'a', 
				'd'=>array('href'=>"contribution/audio/$this->laid",'class'=>'CON zA', 'html'=>'....report Audio status'))
			)
		);
		$j['li'][]=array('t'=>'li', 'l'=>array(
			array('t'=>'a', 
				'd'=>array('href'=>"contribution/privacy/$this->laid",'class'=>'CON zA', 'html'=>'....report Privacy'))
			)
		);
		/*
		$j['li'][]=array('t'=>'li', 'l'=>array(
			array('t'=>'a', 
				'd'=>array('href'=>"privacy/get/$this->laid",'class'=>'CON zA', 'data-role'=>'form',
				'html'=>'....report Privacy get'))
			)
		);
		*/
		$z['zj'][]=array('t'=>'ol', 'd'=>array('class'=>'d3 ds'), 'l'=>$j['li']);
		$z['zj'][]=array('t'=>'p', 'd'=>array('class'=>'b1 msg','data-icon'=>'!','html'=>'...please see our privacy policy outlines before you make any submittion...'));
		if($this->isAuthorization('tag'))$z['zj'][]=array('t'=>'ol', 'd'=>array('class'=>'d3 da'), 'l'=>array(
				array('t'=>'li','d'=>array('data-icon'=>'tr'), 'l'=>array(
					array('t'=>'a', 
						'd'=>array('href'=>'track/all/1','class'=>'CON zA', 'html'=>'...check user suggested tag for Tracks, it is not detail but you have to click to redirected to the Track page...'))
					)
				),
				array('t'=>'li','d'=>array('data-icon'=>'ar'), 'l'=>array(
					array('t'=>'a', 
						'd'=>array('href'=>'artist/all/2','class'=>'CON zA', 'html'=>'...check user suggested tag for Artists, it is not detail but you have to click to redirected to the Artist page...'))
					)
				),
				array('t'=>'li','d'=>array('data-icon'=>'al'), 'l'=>array(
					array('t'=>'a', 
						'd'=>array('href'=>'album/all/3','class'=>'CON zA', 'html'=>'...check user suggested tag for Albums, it is not detail but you have to click to redirected to the Album page...'))
					)
				),
				array('t'=>'li','d'=>array('data-icon'=>'al'), 'l'=>array(
					array('t'=>'a', 
						'd'=>array('href'=>'privacy/all','class'=>'CON zA', 'html'=>'...check reported Privacy, as it mention above the page have to be redirected...'))
					)
				),
			)
		);
		return $z;
	}
}
?>