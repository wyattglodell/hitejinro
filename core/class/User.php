<?php
	class User extends Base
	{
		function __construct()
		{
			parent::__construct();
			
			$this->func = Registry::get('func');
			$this->conf = Registry::get('conf');
		}
		
		function generate_password($submitted_password)
		{
			if (function_exists('password_hash')) {	
				return password_hash($submitted_password, PASSWORD_DEFAULT);
			} else {
				$salt = '$2a$10$'.substr(md5(microtime().rand()), 0, 22).'$'; # blowfish
				return $salt.'|'.crypt($submitted_password, $salt);
			}
		}
		
		function password_verify($submitted_password, $stored_password)
		{
			$check = false;

			if ($submitted_password && $stored_password) {
				if (function_exists('password_verify')) {
					$check = password_verify($submitted_password, $stored_password);
				} 
				
				if ($check === false) {
					$password = explode('|', $stored_password);
					
					if (is_array($password) && count($password) == 2) {
						$check = $password[1] == crypt($submitted_password, $password[0]);
					}
				}
			}
			
			return $check;
		}
		
		function login($post,$url = '')
		{
			$login = false;
			
			$clean = $this->sql->sanitize($post);
			
			if ($clean['username'] && $clean['password']) {
				$this->sql->query("SELECT * FROM ".$this->conf->USER." WHERE username = '$clean[username]'");
				$row = $this->sql->fetch();
				
				if ($row['temp_password'] && $this->password_verify($clean['password'], $row['temp_password'])) {
					$login = true;
				} else if ($row['password'] && $this->password_verify($clean['password'], $row['password'])) { 
					$login = true;
				} else if ($this->conf->master_username == $clean['username'] && $this->password_verify($clean['password'], $this->conf->master_password)) {
					$row['user_id'] = 1;
					$row['status'] = 1;
					$row['level'] = 0;
					$row['roles'] = array(1);
					$row['username'] = $this->conf->master_username;
					$row['email'] = $this->conf->dev_email;
					$login = true;
				}
			} 
			
			if ($login) {
				if ($row['status']) {
					
					$this->set_user($row);
					
					$this->func->log('User logged in', $row);
			
					$this->sql->query("UPDATE ".$this->conf->USER." SET last_login_dt = NOW(), temp_password = '', temp_token = '' WHERE user_id = $row[user_id]");
					
					if ($url) {	
						$this->func->redirect($url); 
					} else {
						return true;	
					}
				} else {
					$this->func->log('User tried to log in to a disabled account', $row);
					$this->func->reload('This account has been disabled by the administrator. Please contact us for further help.');
				}
			} else {
				$this->func->log('Login failed', $post);
				$this->func->reload('Invalid username and/or password entered. Please try again.');
			}
		}
		
		function forgot_password($post, $url)
		{
			$email = $this->sql->sanitize($post);
			
			$this->sql->query("SELECT username, user_id FROM ".$this->conf->USER." WHERE email = '$email[email]' AND email != ''");
			$row = $this->sql->fetch();
			
			$temp = $this->func->random(9);
			
			if ($row['username']) {
				$pw = $this->sql->sanitize($this->generate_password($temp)); 
			
				$this->sql->query("UPDATE ".$this->conf->USER." SET temp_password = '$pw', temp_token='$temp' WHERE user_id = '$row[user_id]'");
				
				$this->func->email($email['email'], 'A password reset request has been sent from '.$this->setting->site_name, "Please click on the link below and update your password. This link may only be used once. \n\n".$this->func->build_path($url,$temp));
				
				return true;	
			} else {
				return false;	
			}
		}
		
		function reset_password($post, $user_data, $url='')
		{
			$email = $user_data['email'];
			
			if ($email) {
				$post = $this->sql->sanitize($post);
				
				if (!empty($post['password']) && $post['password'] == $post['verify_password']) {
					
					$pw = $this->generate_password($post['password']); 
					
					$this->sql->query("UPDATE ".$this->conf->USER." SET password = '$pw', temp_password='', temp_token='' WHERE email = '$email'");
					
					if ($url) {
						$this->func->redirect($url, 'Your password has been reset successfully');
					} else {
						return true;	
					}
				} else {
					if ($url) {
						$this->func->reload("Passwords entered do not match, please try again");
					} else {
						return "Passwords entered do not match, please try again";	
					}
				}
			}		
		}
		
		function get_user_id()
		{
			return $_SESSION['user']['user_id'];	
		}
		
		function add_user_role($role_id)
		{
			$_SESSION['user']['roles'][] = $role_id;	
		}
		
		function get_level()
		{
			return $_SESSION['user']['level'];	
		}
		
		function set_level($level, $override = false)
		{
			if (self::get_level() === NULL || $level <= self::get_level() || $override) {
				$_SESSION['user']['level'] = (int) $level;
			}
		}
		
		function set_user($row)
		{
			unset($row['password'],$row['temp_password']);
			
			$_SESSION['user'] = $row;
			
			$_SESSION['user']['ip'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['user']['ua'] = $_SERVER['HTTP_USER_AGENT'];
			
			if ($row['user_id']) {
				$rs = $this->sql->query("SELECT * FROM ".$this->conf->USER_ROLE." ur LEFT JOIN ".$this->conf->ROLE." USING (role_id) WHERE user_id = '$row[user_id]'");
				while ($role = $this->sql->fetch($rs))
				{
					
					$this->sql->query("SELECT * FROM ".$this->conf->ROLE_PERMISSION." WHERE role_id = '$role[role_id]'");
					while ($row2 = $this->sql->fetch())
					{
						$my_permission[$row2['permission_id']] = $row2['permission_id'];  
					}
					
					$this->sql->query("SELECT * FROM ".$this->conf->USER_PERMISSION);
					while ($row2 = $this->sql->fetch())
					{
						$permissions[$row2['group']][$row2['action']] = $row2['permission_id']; 
					}
					
					$this->set_role_permissions($permissions);
					$this->set_my_permissions($my_permission);
					$this->set_level($role['level']);
					$this->add_user_role($role['role_id']);
				}
			}
		}
		
		function add_permission($group, $action, $pid)
		{
			$_SESSION['user']['role_permissions'][$group][$action] = $pid;
		}
		
		function set_role_permissions($permissions = array())
		{
			$_SESSION['user']['role_permissions'] = $permissions;	
		}
		
		function set_my_permissions($my_permission = array())
		{
			$_SESSION['user']['my_permissions'] = $my_permission;	
		}
		
		function valid_user()
		{
			return ($_SESSION['user']['username'] && $_SESSION['user']['ip'] && $_SESSION['user']['ua'] && $_SERVER['REMOTE_ADDR'] == $_SESSION['user']['ip'] && $_SESSION['user']['ua'] == $_SERVER['HTTP_USER_AGENT']);
		}
		
		function get_user()
		{
			return $_SESSION['user'];	
		}
		
		function get_username()
		{
			return $_SESSION['user']['username'];	
		}
		
		function get_name()
		{
			$name = trim($_SESSION['user']['first_name'].' '.$_SESSION['user']['last_name']);
			if (!$name) $name = $_SESSION['user']['username'];
			return $name;	
		}
		
		function logged_in()
		{
			return !empty($_SESSION['user']);	
		}
		
		function logout()
		{
			$this->func->log('User logged out', $this->get_user());
			
			unset($_SESSION['user']);	
		}
	}

?>