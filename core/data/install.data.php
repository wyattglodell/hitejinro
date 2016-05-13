<?php
	if (!isset($conf->dev_mode) || $conf->dev_mode) {
		if (is_dir($conf->custom) ) {
			$func->redirect($conf->http, 'This site has already ran the installation');
		} else {
			$install = true;
			
			if (!is_writable($conf->root)) {
				$func->set_msg('Site root is not writable');
				$install = false;
			}
					
			if ($_POST['action'] == 'install' && $install) {
				$host = $_POST['database_host'];
				$un = $_POST['database_user'];
				$pw = $_POST['database_pass'];
				$db = $_POST['database_name'];
				$prefix = $_POST['table_prefix'];
				$dev_email = $_POST['dev_email'];
				$master_username = $_POST['master_username'];
				$master_password = $_POST['master_password'];
				
				if ($dev_email && $master_username && $master_password) {
					if ($host && $un && $pw && $db) {
						$test = @mysql_connect($host,$un,$pw);
						if ($test) {
							$test = @mysql_select_db($db);
							
							if ($test) {
								
								$mkdir = array(
									$conf->custom,$conf->custom.'/class',$conf->custom.'/inc',$conf->custom.'/data',
									$conf->custom.'/template',$conf->custom.'/data/admin',$conf->public_file,$conf->public_file.'/css',
									$conf->public_file.'/js',$conf->public_file.'/img',$conf->public_file.'/upload',$conf->public_file.'/modules',
									$conf->custom.'/data/admin/administrator', $conf->cache_file
									
								);
								
								foreach ($mkdir as $v)
								{
									if (!is_dir($v)) {
										mkdir($v);
										chmod($v, 0777);
									}
								}
												
								$config_file = file_get_contents($conf->core.'/install/config.php');
								$config_file_replace = array(
									'master_username' => $master_username,
									'master_password' => User::generate_password($master_password),
									'dev_email' => $dev_email,
									'prefix' => $prefix,
									'db_host' => $host,
									'db_name' => $db,
									'db_username' => $un,
									'db_password' => $pw
								);
								
								foreach ($config_file_replace as $k=>$v)
								{
									$config_file = str_replace('{'.$k.'}', $v, $config_file);
								}
															
								$files = array(
									$conf->custom.'/.htaccess' => "deny from all",
									$conf->custom.'/inc/config.php' => $config_file,
									$conf->public_file.'/upload/.htaccess' => "RemoveHandler .php .phtml .php3 .php5\nRemoveType .php .phtml .php3 .php5\nphp_flag engine off"
								);
								
								foreach ($files as $file=>$content)
								{
									$f = fopen($file, 'w');
									chmod($file, 0777);
									fwrite($f, $content);
																		
								}
								
								$copy = array( 
									'main.css' => $conf->public_file.'/css/main.css',
									'custom.js' => $conf->public_file.'/js/custom.js',
									'Func.php' => $conf->custom.'/class/Func.php'
								);
								
								foreach ($copy as $k=>$v)
								{
									copy($conf->core.'/install/'.$k, $v);
									chmod($v, 0777);
								}
								
								if (class_exists('PharData')) {
									foreach (array('ckeditor','captcha') as $v)
									{
										copy($conf->core.'/install/'.$v.'.tgz', $conf->public_file.'/'.$v.'.tgz');
										
										$pd = new PharData($conf->public_file.'/'.$v.'.tgz');
										$pd->decompress(); 
									 
										$pd = new PharData($conf->public_file.'/'.$v.'.tar');
										$pd->extractTo($conf->public_file.'/modules'); 
										unlink($conf->public_file.'/'.$v.'.tar');
										unlink($conf->public_file.'/'.$v.'.tgz');
									}
									$func->redirect($conf->http.'/install-setting', 'Database and files setup.');
								} else {
									$func->redirect($conf->http.'/install-setting', 'Database and files setup. PharData does not exist, manually extract editor.tgz and captcha.tgz to /public/modules');
								}
								
								
							} else {
								$tpl->assign('form', $_POST);
								$func->set_msg('Invalid database name provided');
							}
						} else {
							$tpl->assign('form', $_POST);
							$func->set_msg('Invalid database information provided');
						}
					} else {
						$tpl->assign('form', $_POST);
						$func->set_msg('Need all the database information');
					}
				} else {
					$tpl->assign('form', $_POST);
					$func->set_msg('Need all the the master login and/or email');
				}
			}
		
			$tpl->set_template('header', '');
			$tpl->set_template('footer', '');
			$tpl->set_template('content', 'install.tpl.php');
		}
	} else {
		$func->redirect($conf->http, '');
	}
?>