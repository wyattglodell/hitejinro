<?php
	class Controller extends Base
	{
		private $page;
		private $params;
		
		function parse($params)
		{	
			$params['request'] = ltrim($params['request'], '/');
			
			$ps = explode('/', $params['request']);
			
			$this->params = $this->clean_params($ps);
			
			$get = new Get($this->params);
			Registry::set($get);

			foreach ($this->params as $k=>$v)
			{
			   $get->{chr($k+97)} = $v;
			}
           
            $get->z = $get->all();

			foreach ($params as $k=>$v)
			{
				if (preg_match('~[a-zA-Z0-9_]{2,}~', $k)) {
					if (!get_magic_quotes_gpc()) {
						$get->$k = $v;
					} else {
						$get->$k = stripslashes($v);
					}
				}
			}
		}
		
		function clean_params($params)
		{
			foreach ((array)$params as $v)
			{
				if (preg_match('~[^a-zA-Z0-9-_:]+~', $v)) {
					return array();
				}
			}
			
			return $params;
		}
		
		function parse_cache_request_match($subject, $rule)
		{
			$rule = trim(str_replace('*', '(.*?)', $rule), '/');
			$subject = trim($subject, '/');
			
			return preg_match('~^'.$rule.'$~', $subject);
		}
		
		function run()
		{
			$conf = Registry::get('conf');
			$get = Registry::get('get');
			
			if ($conf->db_host && $conf->db_username && $conf->db_password && $conf->db_name && !$sql) {
				$sql = new MySQL($conf->db_host, $conf->db_username, $conf->db_password, $conf->db_name); # host un pw db
				$sql->connect(); 
				$sql->set_env($conf->dev_mode);
				
				Registry::set($sql, 'sql');	
			}
			
			if ($sql && $get->z != 'install-setting') {
				$setting = new Setting;
				$setting->set_tbl($conf->SITE_SETTING);
				$setting->load();
				
				Registry::set($setting);
				
				$session = new Session; # start the DB session
			}
			
			if (!isset($get->a) || !trim($get->a) || $get->a == 'bootstrap') {
				$get->a = ($setting->home_url ? $setting->home_url : 'home');
			}

			if ($setting->site_maintenance == 'On' && $get->a != $conf->admin_alias) {
				if ($get->a == 'letmein') { # allows backdoor access if maintenance is on
					$_SESSION['maintenance_view'] = true;
				} else if (!$_SESSION['maintenance_view']) {
					include ($this->conf->core.'/inc/maintenance.php'); 
					exit;
				}
			}

			$cacheable = $cache_this_request = false;
			$run_bootstrap = true;
			
			if ($setting->cache_expiration && $setting->pages_to_cache) {
				foreach (explode("\n",$setting->pages_to_cache)  as $rule)
				{
					if ($this->parse_cache_request_match($get->z, $rule)) {
						$cacheable = true;	
						break;
					}
				}
			}
			
			if ($cacheable) {
				$request = $get->z;
				
				$sql->query("SELECT data FROM $conf->CACHE_PAGE WHERE request = '".$sql->sanitize($request)."' AND host = '".$_SERVER['HTTP_HOST']."' AND UNIX_TIMESTAMP(expire) > '".time()."'");
				$data = $sql->fetch();

				if ($data['data']) {
					$run_bootstrap = false;
					echo $data['data'];
				} else {
					$cache_this_request = true;	
				}
			} 
			
			if ($cache_this_request) {
				ob_start();
			}
			
			if ($run_bootstrap) {
				if (class_exists('Func')) {
					$func = new Func;
				} else {
					$func = new BaseFunc;
				}

				$url = $func->build_path($conf->path, $get->z);
				
				foreach ($_GET as $k=>$v)
				{
					if (!in_array($k, array('destination','rewrite','request'))) {
						$qs .= $k.'='.urlencode($v).'&';	
					}
				}
				
				if (!$url) {
					$url = '/';	
				}
				
				$conf->set('current_url', $url);
				$conf->set('current_uri', $func->href($url, $qs));
				
				Registry::set($func, 'func');
				
				$user = new User;
				Registry::set($user, 'user');
				
				if ($conf->fastboot && in_array($get->a, (array)$conf->fastboot)) {
					$func->load_data($get->a);
				}  else {
					$func->load_data('bootstrap');
				}
			}
			
			if ($cache_this_request) {
				$html = ob_get_clean();
				
				$sql->query("REPLACE INTO $conf->CACHE_PAGE (data, host, expire, request) VALUES ('".$sql->sanitize($html)."','".$_SERVER['HTTP_HOST']."','".date('Y-m-d H:i:s', time() + $setting->cache_expiration)."','".$sql->sanitize($request)."')");
				
				echo $html;
			}
		}
	}
?>