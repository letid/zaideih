<?php
class device
{
	public function __construct($check,$index,$tpl)
	{
		$this->cdc = $check;
		$this->cdi = $index;
		$this->cdt = $tpl;
		$this->is = @$_GET['browse'];
	}
	public function check()
	{
		if($this->is):
			if(array_search($this->is, $this->cdc)):
				$is_device[$this->cdi[0]] = $this->is;
				foreach($this->cdi as $dlv):
					if(isset($_GET[$dlv])):
						$is_device[$dlv] = $_GET[$dlv];
					endif;
				endforeach;
			else:
				unset($_SESSION['detected_device']);
			endif;
		endif;

		if(!isset($_SESSION['detected_device'])):
			require_once 'Mobile_Detect.php';
			$device = new Mobile_Detect();
			$is_device[$this->cdi[0]] = ($device->isMobile()?($device->isTablet()?$this->cdc['tab']:$this->cdc['mob']):$this->cdc['desk']);
			unset($this->cdi[0]);
			foreach($device->getRules() as $name => $regex):
				if ($device->{"is$name"}()):
					$dk = current($this->cdi);
					$is_device[$dk] = strtolower($name);
					if(($dkk = array_search($dk, $this->cdi)) !== false):
						unset($this->cdi[$dkk]);
					endif;
				endif;
			endforeach;
		endif;

		if(isset($is_device) and is_array($is_device)):
			$is_device_key = array_keys($is_device);
			$is_device_value = array_values($is_device);
			foreach($this->cdt as $tplk => $tplv):
				$is_device_tpl[$tplk] = str_replace($is_device_key,$is_device_value,$this->cdt[$tplk]);
			endforeach;
			krsort($is_device_tpl);
			$_SESSION['tpl'] = $is_device_tpl;
			$_SESSION['detected_device'] = $is_device;
			$_SESSION['device'] = get::device();
			//$browser = get_browser(null, true);
			//$_SESSION['detected_browser'] = array(1=>$browser['platform'],2=>$browser['browser']);
		endif;
	}
}