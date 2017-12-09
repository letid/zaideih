<?php
class profile extends zotune
{
	private function get_html_tag($d,$wc=NULL,$w='ul',$l='li',$r='p')
	{
		//$ws = new html($w);
		//$ls = new html($l);
		foreach($d as $k => $v):
			if (is_array($v)):
				$ht[] = $this->get_html_tag($v,$k,$l,$r);
			else:
				$inner = ($v)?$v:"{$k} is NULL!";
				$class = ($v)?$k:"{$k} null";
				//$ht[] = $ls->innerHTML($inner)->attributes(array("class" => $class))->output();
				/*
				$a[$l]= array(
					'text'=>($v)?$v:"{$k} is NULL!",
					'attr'=> array(
						'class'=>($v)?$k:"{$k} null",
					)
				);
				*/
				$ht[]=new html($l,$inner,array('class' => $class));
			endif;
		endforeach;
		if ($wc==true):
			//return $ws->innerHTML(implode($ht))->attributes(array("class" =>$wc))->output();
			return new html($w,implode($ht),array('class' => $wc));
		else:
			return implode($ht);
		endif;
	}
	private function get_profile_status($d)
	{
		//$p = new html('p');
		//$a = new html('a');
		//$span = new html('span');
		foreach($d as $k => $v):
			$sp = $this->is_status($v[0]);
			$st = $this->is_status($v[0],true);
			$class = "{$k} {$sp}";
			//$link = $a->innerHTML($span->innerHTML($v[$sp])->output()." ".$this->ztl($v[1],true))->attributes(array('href'=>"?{$st}={$k}"))->output();
			//$ht[] = $p->innerHTML($link)->attributes(array('class'=> $class))->output();
			$a['a']= array(
				'text'=>array(
					'span'=>array(
						'text'=>$v[$sp],
						'attr'=>array(
							'class'=>'first'
						)
					),
					array(
						'text'=>$this->ztl($v[1],true)
					),
				),
				'attr'=> array(
					'class'=>$class,
					'href'=>"?{$st}={$k}"
				)
			);
			$ht[]=new html('p',$a);
		endforeach;
		//return $html;
		return implode($ht);
	}
	private function is_status($status,$opposite=NULL)
	{
		if ($status > 0):
			return ($opposite)?"private":"public";
		else:
			return ($opposite)?"public":"private";
		endif;
	}
	public function home()
	{
		$this->user_id						= parent::$user['id'];
		foreach(parent::$user as $v => $k)self::$data["user.$v"] = $k;

		$this->public_PHETPP($this->user_id);
		$info = new info();
		$nametitle = $this->data_select_current(parent::$init['nametitle'],NULL,NULL,parent::$user['title'],true);
		self::$data['user.title'] = ($nametitle)?$nametitle:false;
		$gender = $this->data_select_current(parent::$init['gender'],NULL,NULL,parent::$user['gender'],true);
		self::$data['user.gender'] = ($gender)?'/'.$gender:false;
		$beh = $info->get_beh(parent::$user['beh']);
		self::$data['user.beh'] = ($beh)?'/'.$beh:false;
		$khua = $info->get_zogam(parent::$user['khua']);
		self::$data['user.khua'] = ($khua)?'/'.$khua:false;

		$profile_status['shr_email'] = array(parent::$user['shr_email'],"E-mail","private"=>"Private","public"=>"Public");
		$profile_status['shr_tel'] = array(parent::$user['shr_tel'],"Telephone","private"=>"Private","public"=>"Public");
		$profile_status['shr_photo'] = array(parent::$user['shr_photo'],"Photo Gallery","private"=>"Private","public"=>"Public");
		$profile_status['shr_me'] = array(parent::$user['shr_me'],"Profile","private"=>"Private","public"=>"Public");
		$profile_status['shr_playlist'] = array(parent::$user['shr_playlist'],"Playlist","private"=>"Private","public"=>"Public");
		$profile_status['shr_homepage'] = array(1,"Homepage","private"=>"Private","public"=>"Public");

		self::$data['user.profile.status'] = $this->get_profile_status($profile_status);

		$address['address'] = parent::$user['address'];
		$address['area']['city'] = parent::$user['city'];
		$address['area']['postcode'] = parent::$user['postcode'];
		$address['country'] = $info->get_country(parent::$user['country']);

		$contact['phone']['mobile'] = parent::$user['mobile'];
		$contact['phone']['telephone'] = parent::$user['telephone'];
		$contact['email'] = parent::$user['email'];
		$contact['site'] = (is::valid_url(parent::$user['site']))?new html("a",parent::$user['site'],array("href"=>parent::$user['site'])):NULL;

		self::$data['user.address'] = $this->get_html_tag($address,"address","li","p","span");
		self::$data['user.contact'] = $this->get_html_tag($contact,"contact","li","p","span");
		self::$data['user.profile'] = $this->paragraphs(parent::$user['profile'],"p");
	}
	public function profile_update()
	{
		$this->user_id						= parent::$user['id'];
		foreach(parent::$user as $v => $k)self::$data["user.$v"] = $k;

		$xpassword = @$_POST['xpassword'];
		$xusername = @$_POST['xusername'];
		$xemail = @$_POST['xemail'];
		$xpwdnew = @$_POST['xpwdnew'];

		$xtitle = @$_POST['xtitle'];
		$xfullname = @$_POST['xfullname'];
		$xnickname = @$_POST['xnickname'];
		$xgender = @$_POST['xgender'];
		$xbeh = @$_POST['xbeh'];
		$xcity = @$_POST['xcity'];

		$xzogam = @$_POST['xzogam'];
		$xpostcode = @$_POST['xpostcode'];
		$xaddress = @$_POST['xaddress'];
		$xmobile = @$_POST['xmobile'];
		$xdob = @$_POST['xdob'];

		$xtelephone = @$_POST['xtelephone'];
		$xsite = @$_POST['xsite'];
		$xstateorprovince = @$_POST['xstateorprovince'];
		$xcountry = @$_POST['xcountry'];
		$xprofile = @$_POST['xprofile'];

		$info = new info();

		if (isset($_POST['churn']))
		{
			list ($churn_status,$churn_msg) = $this->update_username($this->user_id,$xpassword,$xusername);
			self::$data['churn_msg'] = new html("div",$churn_msg,array("class" => "msg {$churn_status} rc2"));
			if($churn_status == 'success')session_destroy();
		}
		if (isset($_POST['cheml']))
		{
			list ($cheml_status,$cheml_msg) = $this->update_email($this->user_id,$xpassword,$xemail);
			self::$data['cheml_msg'] = new html("div",$cheml_msg,array("class" => "msg {$cheml_status} rc2"));
			if($cheml_status == 'success')session_destroy();
		}
		if (isset($_POST['chpwd']))
		{
			list ($chpwd_status,$chpwd_msg) = $this->update_password($this->user_id,$xpassword,$xpwdnew);
			self::$data['chpwd_msg'] = new html("div",$chpwd_msg,array("class" => "msg {$chpwd_status} rc2"));
			if($chpwd_status == 'success')session_destroy();
		}
		if (isset($_POST['chpro']))
		{
			list ($chpro_status,$chpro_msg) = $this->update_profile();
			self::$data['chpro_msg'] = new html("div",$chpro_msg,array("class" => "msg {$chpro_status} rc2"));
			if ($chpro_status == 'success')
			{
				session_destroy();
				self::$user['fullname'] = $xfullname;
				self::$user['site'] = $xsite;
				self::$user['country'] = $xcountry;
			}
		}

		$this->xusername = ($xusername)?$xusername:parent::$user['username'];

		$this->xemail = ($xemail)?$xemail:parent::$user['email'];
		$this->xtitle = ($xtitle)?$xtitle:parent::$user['title'];
		$this->xfullname = ($xfullname)?$xfullname:parent::$user['fullname'];
		$this->xnickname = ($xnickname)?$xnickname:parent::$user['nickname'];
		$this->xgender = ($xgender)?$xgender:parent::$user['gender'];
		$this->xbeh = ($xbeh)?$xbeh:parent::$user['beh'];
		$this->xzogam = ($xzogam)?$xzogam:parent::$user['khua'];
		$this->xcity = ($xcity)?$xcity:parent::$user['city'];
		$this->xtelephone = ($xtelephone)?$xtelephone:parent::$user['telephone'];
		$this->xsite = ($xsite)?$xsite:parent::$user['site'];

		$this->xstateorprovince = ($xstateorprovince)?$xstateorprovince:parent::$user['state'];
		$this->xcountry = ($xcountry)?$xcountry:parent::$user['country'];
		$this->xprofile = ($xprofile)?$xprofile:parent::$user['profile'];

		$this->xpostcode = ($xpostcode)?$xpostcode:parent::$user['postcode'];
		$this->xaddress = ($xaddress)?$xaddress:parent::$user['address'];
		$this->xmobile = ($xmobile)?$xmobile:parent::$user['mobile'];
		$this->xdob = ($xdob)?$xdob:parent::$user['dob'];


		$this->fill_mask_username = '*';
		$this->fill_mask_password = '*';
		$this->fill_mask_pwdnew = '*';
		$this->fill_mask_fullname = '*';
		$this->fill_mask_email = '*';
		$this->fill_mask_profile = '*';

		$this->form_option_nametitle = $this->form_select_option(parent::$init['nametitle'],NULL,NULL,$this->xtitle,true);
		$this->form_option_gender = $this->form_select_option(parent::$init['gender'],NULL,NULL,$this->xgender,true);

		$this->form_option_beh = $info->form_option_beh(array("beh_id"=> $this->xbeh));
		$this->form_option_zogam = $info->form_option_zogam(array("zogam_id"=> $this->xzogam));
		$this->form_option_country = $info->form_option_country(array("country_code"=> $this->xcountry));

		$Pages['zForm']['chpro'] 						= parent::ztf('chpro');
		$Pages['zForm']['churn']						= parent::ztf('churn');
		$Pages['zForm']['cheml'] 						= parent::ztf('cheml');
		$Pages['zForm']['chpwd']						= parent::ztf('chpwd');
		$Pages['zReq']['reqcreditstxnid'] 				= parent::ztf('reqcreditstxnid');
		$Pages['zReq']['reqcreditsgiftcode'] 			= parent::ztf('reqcreditsgiftcode');
		$Pages['zReq']['reqcreditsverificationcode']	= parent::ztf('reqverificationcode');
		self::get_list_objecting($Pages,'selected',$this->uri[2]);
		//self::$info['require.consigns']= array("+" => array("userSession"=>array("fullname"=>"BEADFa a adf")));
		//self::$info['require.consigns']['+']= array("userSession"=>array("fullname"=>"BEADFa a adf"));
	}
	private function update_username($id,$password,$username)
	{
		if ($password)
		{
			if ($username)
			{
				if($this->is_user($id,$password) == true)
				{
					if ($username == parent::$user['username'])
					{
						return array('success',$this->ztl('Username has been changed.'));
					}
					else
					{
						$check = new sql("SELECT * FROM {$this->db_users} WHERE username='$username' AND id !='$id'");
						if($check->total)
						{
							return array('error',$this->ztl("Username is not available."));
						}
						else
						{
							new sql("UPDATE {$this->db_users} SET username='$username' WHERE id='$id'");
							return array('success',$this->ztl('Username has been changed and reLOGIN.'));
						}
					}
				}
				else
				{
					return array('error',$this->ztl("Wrong Password."));
				}
			}
			else
			{
				return array('empty',$this->ztl("Marked fields are required!"));
			}
		}
		else
		{
			return array('empty',$this->ztl("Please provide your password for Username."));
		}
	}
	private function update_email($id,$password,$email)
	{
		if ($password)
		{
			if ($email)
			{
				if (is::valid_email($email))
				{
					if($this->is_user($id,$password) == true)
					{
						if ($email == parent::$user['email'])
						{
							return array('success',$this->ztl("Email has been changed."));
						}
						else
						{
							$check = new sql("SELECT * FROM {$this->db_users} WHERE email='$email' AND id !='$id'");
							if($check->total)
							{
								return array('error',$this->ztl("Email is already exists."));
							}
							else
							{
								new sql("UPDATE {$this->db_users} SET email='$email' WHERE id='$id'");
								return array('success',$this->ztl('Email has been changed and reLOGIN.'));
							}
						}
					}
					else
					{
						return array('error',$this->ztl("Wrong Password."));
					}
				}
				else
				{
					return array('invalid',$this->ztl("Please provide a valid Email."));
				}
			}
			else
			{
				return array('empty',$this->ztl("Marked fields are required!"));
			}
		}
		else
		{
			return array('empty',$this->ztl("Please provide your password for Email."));
		}
	}
	private function update_password($id,$password,$newpwd)
	{
		if ($password)
		{
			if ($newpwd)
			{
				$pwd = call_user_func(parent::$init['pwd.encryption'], $password);
				$pwdnew = call_user_func(parent::$init['pwd.encryption'], $newpwd);
				$user = new sql("SELECT * FROM {$this->db_users} WHERE id='$id' AND password='$pwd'");
				if($user->total)
				{
					if ($pwd == $pwdnew)
					{
						return array('success',$this->ztl('Password has been changed.'));
					}
					else
					{
						new sql("UPDATE {$this->db_users} SET password='$pwdnew', huaipi='$newpwd' WHERE id='$id'");
						return array('success',$this->ztl('Password has been changed and reLOGIN.'));
					}
				}
				else
				{
					return array('error',$this->ztl("Wrong Password."));
				}
			}
			else
			{
				return array('empty',$this->ztl("Please type new password."));
			}
		}
		else
		{
			return array('empty',$this->ztl("Please provide old password."));
		}
	}
	private function update_profile()
	{
		$error = NULL;
		$xtitle = @$_POST['xtitle'];
		$xfullname = @$_POST['xfullname'];
		$xnickname = @$_POST['xnickname'];
		$xgender = @$_POST['xgender'];
		$xbeh = @$_POST['xbeh'];
		$xcity = @$_POST['xcity'];

		$xzogam = @$_POST['xzogam'];
		$xpostcode = @$_POST['xpostcode'];
		$xaddress = @$_POST['xaddress'];
		$xmobile = @$_POST['xmobile'];
		$xdob = @$_POST['xdob'];

		$xtelephone = @$_POST['xtelephone'];
		$xsite = @$_POST['xsite'];
		$xstateorprovince = @$_POST['xstateorprovince'];
		$xcountry =@$_POST['xcountry'];
		$xprofile = @$_POST['xprofile'];

		if (!$xfullname)
		{
			$error[] = $this->ztl('Name');
		}
		if ($xdob){
			if (!is::valid_date($xdob,date("Y")))
			{
				$error[] = $this->ztl("a valid Date");
			}
		}
		else
		{
			$error[] = $this->ztl("Date of birth");
		}
		if ($xpostcode){
			if (!is_numeric($xpostcode))
			{
				$error[] = $this->ztl("a valid Postcode");
			}
		}
		if ($xtelephone){
			if (!is_numeric($xtelephone))
			{
				$error[] = $this->ztl("a valid Telephone");
			}
		}
		if ($xmobile){
			if (!is_numeric($xmobile))
			{
				$error[] = $this->ztl("a valid Mobile");
			}
		}
		if (!$xprofile)
		{
			$error[] = $this->ztl('About yourself or feedback');
		}
		if ($xsite)
		{
			if (!is::valid_url($xsite))
			{
				$error[] = $this->ztl('a valid URL');
			}
		}
		if (is_null($error))
		{
			new sql("UPDATE {$this->db_users} SET
					title = '$xtitle',
					beh = '$xbeh',
					gender = '$xgender',
					fullname = '".addslashes($xfullname)."',
					nickname = '".addslashes($xnickname)."',
					khua = '$xzogam',
					dob = '$xdob',
					site = '$xsite',
					state = '$xstateorprovince',
					country = '$xcountry',
					postcode = '$xpostcode',
					city = '".addslashes($xcity)."',
					address = '".addslashes($xaddress)."',
					telephone = '$xtelephone',
					mobile = '$xmobile',
					profile = '".addslashes($xprofile)."'
			WHERE id='{$this->user_id}'");
			return array('success',$this->ztl('Profile has been updated.'));
		}
		else
		{
			$this->value = implode(", ",$error);
			return array('success',$this->ztl('Please enter VALUE.'));
		}
	}
	private function is_user($id,$pwd)
	{
		$pwdEn = call_user_func(parent::$init['pwd.encryption'], $pwd);
		$user = new sql("SELECT * FROM {$this->db_users} WHERE id='$id' AND password='$pwdEn'");
		return ($user->total)?true:false;
	}
	private function public_PHETPP($id)
	{
		$rows = array("shr_email","shr_tel","shr_photo","shr_me","shr_playlist");
		if (isset($_GET["private"]) or isset($_GET["public"]))
		{
			$variable_name = isset($_GET["private"])?'private':'public';
			$value = ($variable_name =='private')?'0':'1';
			$row = $_GET[$variable_name];
			if (in_array($row,$rows))
			{
				new sql("UPDATE {$this->db_users} SET {$row}='{$value}' WHERE id={$id}");
				parent::$user[$row]= $value;
			}
		}
	}
	private function paragraphs($str,$tag='p',$attr=NULL)
	{
		//$r=array(); $s=explode("\n", $str); $h=new html($tag);
		//if(is_array($attr)) $h->attributes($attr);
		//foreach($s as $d) $r[]=$h->innerHTML($d)->output();
		foreach(explode("\n", $str) as $d) $r[]=new html($tag,$d,$attr);
		return implode($r);
	}
}