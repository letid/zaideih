<?php 
class registration extends zotune
{
	public function home()
	{
		$register_status = NULL;
		$info = new info();
		
		$xtitle = isset($_POST['xtitle'])?$_POST['xtitle']:NULL;
		$xgender = isset($_POST['xgender'])?$_POST['xgender']:NULL;
		$xbeh = isset($_POST['xbeh'])?$_POST['xbeh']:NULL;
		$xcountry = isset($_POST['xcountry'])?$_POST['xcountry']:NULL;
		$xstateorprovince = isset($_POST['xstateorprovince'])?$_POST['xstateorprovince']:NULL;
		$xzogam = isset($_POST['xzogam'])?$_POST['xzogam']:NULL;
		
		$xdob = isset($_POST['xdob'])?$_POST['xdob']:NULL;
		$xaddress = isset($_POST['xaddress'])?$_POST['xaddress']:NULL;
		$xcity = isset($_POST['xcity'])?$_POST['xcity']:NULL;
		$xpostcode = isset($_POST['xpostcode'])?$_POST['xpostcode']:NULL;

		$xusername = isset($_POST['xusername'])?$_POST['xusername']:NULL;
		$xpassword = isset($_POST['xpassword'])?$_POST['xpassword']:NULL;
		$xfullname = isset($_POST['xfullname'])?$_POST['xfullname']:NULL;
		$xemail = isset($_POST['xemail'])?$_POST['xemail']:NULL;
		$xprofile = isset($_POST['xprofile'])?$_POST['xprofile']:NULL;

		if (isset($_POST['register']))
		{
			if (!$xusername)
			{
				$register_status[] = $this->ztl('Username',true);
				$fill_mask_username = $this->ztl('Required.',true);
			}
			if (!$xpassword)
			{
				$register_status[] = $this->ztl('Password',true);
				$fill_mask_password = $this->ztl('Empty.',true);
			}
			if (!$xfullname)
			{
				$register_status[] = $this->ztl('Name',true);
				$fill_mask_name = $this->ztl('Required.',true);
			}
			if ($xdob)
			{
				if (!is::valid_date($xdob,date("Y")))
				{
					$register_status[] = $this->ztl('a valid Date',true);
					$fill_mask_dob = $this->ztl('Invalid.',true);
				}
			}
			else
			{
				$register_status[] = $this->ztl('Date of birth',true);
				$fill_mask_dob = $this->ztl('Required.',true);
			}
			if ($xpostcode)
			{
				if (!is_numeric($xpostcode))
				{
					$register_status[] = $this->ztl('a valid Postcode',true);
					$fill_mask_postcode = $this->ztl('Postcode is not valid!',true);
				}
			}
			if ($xemail)
			{
				if (!is::valid_email($xemail))
				{
					$register_status[] = $this->ztl('a valid E-mail',true);
					$fill_mask_email = $this->ztl('Email is not valid.',true);
				}	
			}
			else 
			{
				$register_status[] = $this->ztl('Email',true);
				$fill_mask_email = $this->ztl('Required.',true);
			}
			if (!$xprofile){
				$register_status[] = $this->ztl('About yourself or feedback');
				$fill_mask_msg = $this->ztl('Empty.',true);
			}
			
			//REGISTRATION START
			if (is_array($register_status))
			{
				$this->value = implode(', ',$register_status);
				$msg = $this->ztl('Please enter VALUE.');
				parent::$data['registration.messages'] = new html('div',$msg, array('class'=>'msg error'));
			}
			else
			{
				if ($info->checkUsersData(array('row'=>'username','value'=>$xusername)))
				{
					$fill_mask_username = $this->ztl('Exists.',true);
					parent::$data['registration.messages'] = new html('div',$this->ztl('Username is not available.',true), array('class'=>'msg error'));
				}
				else if ($info->checkUsersData(array('row'=>'email','value'=>$xemail)))
				{
					$fill_mask_email = $this->ztl('Exists.',true);
					parent::$data['registration.messages'] = new html('div',$this->ztl('Email is already exists.'), array('class'=>'msg error'));
				}
				else
				{
					$newpassword = call_user_func(parent::$init['pwd.encryption'], $xpassword);
					$newfullname = addslashes($xfullname);
					$newprofile = addslashes($xprofile);
					$newaddress = addslashes($xaddress);
					$newcity = addslashes($xcity);

					$this->subject 		= $this->ztl('Welcome to SITE.');
					
					
					$this->PASSWORD 	= $xpassword;
					$this->USERNAME 	= $xusername;
					
					$this->CODE	 		= get::new_pwd();
					$this->EMAIL 		= $xemail;
					$this->NAME 		= $xfullname;
					

					$register_user = new sql("INSERT INTO {$this->db_users} SET 
					  username ='$xusername', password = '$newpassword', huaipi = '$xpassword',
					  khua = '$xzogam', gender = '$xgender', fullname = '$newfullname',
					  dob = '$xdob',  email = '$xemail', profile = '$newprofile',
					  beh = '$xbeh', title = '$xtitle', country = '$xcountry',
					  state = '$xstateorprovince', address = '$newaddress', postcode = '$xpostcode', city = '$newcity', 
					  status = '1', code = '{$this->CODE}', ip='$this->ip', dreg = NOW()");

					$this->USERID 		= $register_user->insert_id;
					$eml_template 		= parent::$info['dir.template']."email/registration";

					$status = ($this->mailing($this->EMAIL,$this->subject,NULL,$eml_template))?'sent':'unsent';

					$this->value = new html('a',$this->ztl('Login',true), array('class'=>'login', 'href'=>parent::$data['www.login']));
					$WouldULike2 = $this->ztl('Registration completed, Would you like to DO?');

					parent::$data['registration.messages'] = new html('div',$WouldULike2, array('class' => "msg complete $status"));
					$xusername = NULL; $xpassword = NULL; $xfullname =NULL; $xemail =NULL; $xprofile=NULL;
				}
			}
		}

		
		parent::$data['yyyy-mm-dd'] = "yyyy-mm-dd";
		parent::$data['fill.mask.username'] = isset($fill_mask_username)?$fill_mask_username:'*';
		parent::$data['fill.mask.password'] = isset($fill_mask_password)?$fill_mask_password:'*';
		parent::$data['fill.mask.fullname'] = isset($fill_mask_fullname)?$fill_mask_fullname:'*';
		parent::$data['fill.mask.email'] = isset($fill_mask_email)?$fill_mask_email:'*';
		parent::$data['fill.mask.profile'] = isset($fill_mask_profile)?$fill_mask_profile:'*';
		
		parent::$data['fill.mask.dob'] = isset($fill_mask_dob)?$fill_mask_dob:'*';
		parent::$data['fill.mask.address'] = isset($fill_mask_address)?$fill_mask_address:NULL;
		parent::$data['fill.mask.city'] = isset($fill_mask_city)?$fill_mask_city:NULL;
		parent::$data['fill.mask.postcode'] = isset($fill_mask_postcode)?$fill_mask_postcode:NULL;

		parent::$data['xusername'] = $xusername;
		parent::$data['xpassword'] = $xpassword;
		parent::$data['xfullname'] = $xfullname;
		parent::$data['xemail'] = $xemail;
		parent::$data['xprofile'] = $xprofile;

		parent::$data['xdob'] = $xdob;
		parent::$data['xaddress'] = $xaddress;
		parent::$data['xcity'] = $xcity;
		parent::$data['xpostcode'] = $xpostcode;
		parent::$data['xstateorprovince'] = $xstateorprovince;

		parent::$data['form.option.nametitle'] = $this->form_select_option(parent::$init['nametitle'],NULL,NULL,$xtitle,true);
		parent::$data['form.option.gender'] = $this->form_select_option(parent::$init['gender'],NULL,NULL,$xgender,true);

		parent::$data['form.option.beh'] = $info->form_option_beh(array('beh_id'=> $xbeh));
		parent::$data['form.option.zogam'] = $info->form_option_zogam(array('zogam_id'=> $xzogam));
		parent::$data['form.option.country'] = $info->form_option_country(array('country_code'=> $xcountry));
	}
}
?>