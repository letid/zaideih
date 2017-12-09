<?php 
class login extends zotune
{
	/*
	public function __construct()
	{
		$this->tlp_email_new_password 		= parent::$info['dir.template']."email/new-password";
		$this->tlp_email_verification 		= parent::$info['dir.template']."email/verification";
	}
	*/
	public function home()
	{
		$this->tlp_email_new_password 		= parent::$info['dir.template'].'email/new-password';
		$this->tlp_email_verification 		= parent::$info['dir.template'].'email/verification';
		$referer_page= $_SESSION[parent::$info['page.link']];
		if (isset($_POST['login']))
		{
			if (isset($_POST['username']) && isset($_POST['password']))
			{
				$_urn 		= $_POST['username'];
				$_urp 		= call_user_func(parent::$init['pwd.encryption'], $_POST['password']);
				$sql_login = new sql("SELECT * FROM {$this->db_users} WHERE username='$_urn' AND password='$_urp'",'fetch_object');
				if($sql_login->total)
				{
					$user_id 		= $sql_login->id;
					$user_username 	= $sql_login->username;
					$user_password 	= $sql_login->password;
					$user_fullname 	= $sql_login->fullname;
					$user_level 	= $sql_login->level;
					$user_email 	= $sql_login->email;
					$user_loged 	= $sql_login->loged + 1;
					$user_status 	= $sql_login->status;
					$ip 			= $this->ip;
					if ($user_status > 0)
					{
						//$infouser = base64_encode("$user_id|$user_username|$user_password|$user_fullname|$user_level|$user_email");
						setcookie(parent::$init['user.cookie.name'],serialize(array($user_id,$user_password)),time()+1209600);
						unset($_SESSION['tmp_user_status'],$_SESSION['tmp_user_id']);
						new sql("UPDATE {$this->db_users} SET loged='$user_loged', ip='$ip', dlog=NOW() WHERE id='$user_id'");
						if(defined('$this->db_log'))
						{
							new sql("INSERT INTO {$this->db_log} SET userid='$user_id', ip='{$ip}'");
						}
						unset($_SESSION[parent::$init['user.speed.name']]);
						header("Location: {$referer_page}");
					}
					else
					{
						$_SESSION['tmp_user_id'] 		= $user_id;
						$_SESSION['tmp_user_username'] 	= $user_username;
						$_SESSION['tmp_user_fullname'] 	= $user_fullname;
						$_SESSION['tmp_user_email'] 	= $user_email;
						$_SESSION['tmp_user_status'] 	= $user_status;
					}
				}
				else
				{
					self::$data['login.messages'] = new html('div',$this->ztl('Wrong password or username.'),array('class' =>'msg error'));
				}
			}
			else
			{
				self::$data['login.messages'] = new html('div',$this->ztl('Your must username and password to login.'),array('class' => 'msg error'));
			}
		}

		if (isset($_GET['send-new-verification-code']))
		{
			$uid = ($_GET['send-new-verification-code'])?$_GET['send-new-verification-code']:$_SESSION['tmp_user_id'];
			list ($status,$msg) = $this->send_new_verification_code($uid);
			self::$data['account.verification'] = new html('div', $msg, array("class" => "box verification {$status}"));
		}
		if (isset($_GET['verification']))
		{
			list ($status,$msg) = $this->verification($_GET['verification']);
			if (is_array($this->verification_option))
			{
				self::$info['require.consigns']['+']['verification_option'] = $this->verification_option;
			}
			self::$data['account.verification'] = new html('div', $msg, array("class" => "box verification {$status}"));
		}
		if (isset($_SESSION['tmp_user_status']) and isset($_SESSION['tmp_user_id']))
		{
			$this->LINK = new html("a",$this->ztl("New verification code",true), array("href"=>parent::$info['page.link.full.url']."?send-new-verification-code"));
			$this->NAME 	= $_SESSION['tmp_user_fullname'];
			$this->EMAIL 	= $_SESSION['tmp_user_email'];
			$this->ID 		= $_SESSION['tmp_user_id'];

			$message = $this->ztl("Your account requested verification.");
			self::$data['login.messages'] = new html('div',$message,array("class"=>"msg verificationrequired"));
		}
		else if (empty(self::$data['login.messages']))
		{
			$message = $this->ztl("Please enter your username and password to login.",true);
			self::$data['login.messages'] = new html('div',$message,array("class"=>"msg description message"));
		}
		if(isset($_GET['forgot-password']))$this->forgot_password(@$_POST['xusername'],@$_POST['xemail']);	  
		if(isset($_GET['update']))$this->update($_GET['update']);	  
	}
	public function update($q)
	{
		$this->code=$q;
		$this->update_status='disabled';
		$x=explode('-', base64_decode($q));
		if ($id=$x[0] && $code=$x[1])
		{
			$sql = new sql("SELECT * FROM {$this->db_users} WHERE id=$id AND code='$code'",'fetch_object');
			if($sql->total)
			{
				$this->username	= $sql->username;
				$this->email	= $sql->email;
				$this->update_status='enabled';
				if(isset($_POST['login-update'])){
					if ($username=$_POST['xusername'] and $password=$_POST['xpassword'] and $email=$_POST['xemail'])
					{
						if (is::valid_email($email))
						{
							$eC = new sql("SELECT * FROM {$this->db_users} WHERE email='$email' AND id != $id");
							if($eC->total)
							{
								$message = $this->ztl('Email is already exists.');
							}
							else
							{
								$uC = new sql("SELECT * FROM {$this->db_users} WHERE username='$username' AND id != $id");
								if($uC->total)
								{
									$message = $this->ztl('Username is not available.');
								}
								else
								{
									$pwd = call_user_func(parent::$init['pwd.encryption'], $password);
									$code = get::new_pwd();
									$this->code=base64_encode($id.'-'.$code);
									$this->username	= $username;
									$this->email	= $email;
									$update=new sql("UPDATE {$this->db_users} SET 
										username='$username',
										email='$email',
										password='$pwd', 
										huaipi='$password',
										code='{$code}' 
										WHERE id=$id");
									if($update->affected_rows){
										$message = $this->ztl('Your login info have been successfully updated!');
									}
									
								}
							}
						}
						else
						{
							$message = $this->ztl('Please provide a valid Email.');
						}
					}
					else
					{
						$message = $this->ztl('All the fields must be filled in.');
					}
				}
				else
				{
					$message = $this->ztl('You are ready to change your login info!');
				}
			}
			else
			{
				$message = $this->ztl('Verification code is invalid.');
			}
		}
		else
		{
			$message = $this->ztl('Verification code is empty.');
		}
		self::$data['msg.login.update'] = new html('div',$message,array('class'=>'msg description message'));
		$this->ztf('form.login.update');
	}
	public function forgot_password($username,$email)
	{
		if ($username and $email)
		{
			$sql = new sql("SELECT * FROM {$this->db_users} WHERE username='$username' AND email='$email'","fetch_object");
			if ($sql->total)
			{
				$no_replay			= parent::$init['pro.email.noreply'];
				$this->PASSWORD 	= get::new_pwd();
				$this->CODE	 		= get::new_pwd();
				$this->EMAIL 		= $email;
				$password 			= call_user_func(parent::$init['pwd.encryption'], $this->PASSWORD);

				$this->subject 		= $this->ztl("New Password for your ACCOUNT.");
				$this->USERID 		= $sql->id;
				$this->NAME 		= $sql->fullname;
				$this->USERNAME 	= $sql->username;

				if ($this->mailing($this->EMAIL,$this->subject,NULL,$this->tlp_email_new_password))
				{
					new sql("UPDATE {$this->db_users} SET password='$password', huaipi='{$this->PASSWORD}', code='{$this->CODE}' WHERE id='{$sql->id}'");
					$msg_forgot_password = new html('div',$this->ztl('New Password has been sent.'), array('class'=>'msg success'));
					
				}
				else
				{
					$msg_forgot_password = new html('div',$this->ztl('Failed to send new Password.'), array('class'=>'msg error'));
				}
			}
			else
			{
				$msg_forgot_password = new html('div',$this->ztl('Wrong Username or E-mail.',true), array('class'=>'msg error'));
			}
		}
		else
		{
			$msg_forgot_password = new html('div',$this->ztl('It happens to everyone.',true), array('class' => 'msg message'));
		}
		self::$data['msg.forgot.password'] = $msg_forgot_password;
		$this->ztf('form.forgot.password');
	}
	public function send_new_verification_code($uid)  
	{
		if ($uid)
		{
			$sql = new sql("SELECT * FROM {$this->db_users} WHERE id='$uid'","fetch_object");
			if ($sql->total)
			{
				if (isset($_SESSION['tmp_user_email']) and ($_SESSION['tmp_user_email'] == $sql->email))
				{
					
					$this->subject 		= $this->ztl('Verification code for your ACCOUNT.');
					$this->CODE 		= get::new_pwd();
					$this->USERID 		= $sql->id;
					$this->NAME			= $sql->fullname;
					$this->EMAIL		= $sql->email;
					$this->USERNAME 	= $sql->username;

					if ($this->mailing($this->EMAIL,$this->subject,NULL,$this->tlp_email_verification))
					{
						new sql("UPDATE {$this->db_users} SET code='{$this->CODE}' WHERE id='{$sql->id}'");
						return array('sent', $this->ztl('Verification code is sent.'));
					} 
					else
					{
						return array('error', $this->ztl('Failed to send new verification code.'));
					}
				}
			}
			else
			{
				return array('error', $this->ztl('Error on sending new verification code.'));
			}
		}
		else
		{
			return array('error', $this->ztl('Error on sending new verification code.'));
		}
	}
	public function verification($xcode)  
	{
		$codes = explode("-", $xcode);
		$id = @$codes[0];
		$code = @$codes[1];
		if ($id and $code)
		{
			$sql = new sql("SELECT * FROM {$this->db_users} WHERE id='$id' AND code='$code'","fetch_object");
			if ($sql->total)
			{
				$user_id 			= $sql->id;
				$user_username 		= $sql->username;
				$user_password 		= $sql->password;
				$user_fullname 		= $sql->fullname;
				$user_level 		= ($sql->level == 0)?'1':$sql->level; 
				$user_email 		= $sql->email; 
				$user_status 		= $sql->status +1;

				unset($_SESSION['tmp_user_id']);
				unset($_SESSION['tmp_user_username']);
				unset($_SESSION['tmp_user_fullname']);
				unset($_SESSION['tmp_user_email']);
				unset($_SESSION['tmp_user_status']);
						
				new sql("UPDATE {$this->db_users} SET level='$user_level', status='$user_status' WHERE id='$user_id'");

				$this->USERID 		= $user_id;
				$this->NAME 		= $user_fullname;
				$this->USERNAME 	= $user_username;
				$this->EMAIL 		= $user_email;
				$this->CODE 		= $code;

				$this->verification_option = array($user_id,$user_level,$user_status,$user_username);
				return array('success', $this->ztl("Verification completed."));
			}
			else
			{
				return array('invalid', $this->ztl('Verification code is invalid.'));
			}
		}
		else
		{
			return array('empty', $this->ztl('Verification code is empty.'));		  
		}
	}
}
?>