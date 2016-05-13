<?php
	$_file_root = $_file_root ? $_file_root : rtrim(dirname($_SERVER['SCRIPT_FILENAME']), DIRECTORY_SEPARATOR);

	$_path = $_path ? $_path : rtrim(dirname($_SERVER['SCRIPT_NAME']), DIRECTORY_SEPARATOR);	
	
	$request = $_GET['rewrite'] == 1 ? '' : '/?request=';
	
	setlocale(LC_MONETARY, 'en_US'); # for use with money_format('%n', $float)
	date_default_timezone_set('America/Los_Angeles');
	
	
	if (file_exists($_file_root.'/custom/inc/core-config.php')) { 
		include ($_file_root.'/custom/inc/core-config.php');
	}
	
	if (!function_exists('deep_scan')) {
		function deep_scan($folder, & $tree, $overwrite = false)
		{
			$folder = rtrim($folder, '/').'/';
			
			if (file_exists($folder)) {
				foreach (scandir($folder) as $item)
				{
					if ($item != '.' && $item != '..') {
						if (is_dir($folder.$item)) {
							deep_scan($folder.$item, $tree, $overwrite);
						} else if (!$tree[$item] || $overwrite) {
							$tree[$item] = $folder;	
						} 
					}
				}
			}
		}
	}

	if (!function_exists('r')) {
		function r($array)
		{
			if (!count($array)) die('array is empty');
			echo '<pre>'; print_r($array); die;	
		}
	}
	
	if (!function_exists('class_autoload')) {
		function class_autoload($name)
		{	
			global $_file_root;
			
			static $tree = array();
			
			$core = $_file_root.'/core/class/';
			$custom = $_file_root.'/custom/class/';
	
			$class = $name.'.php';
			
			if (!$tree) {
				deep_scan($custom, $tree);
				deep_scan($core, $tree);
			}
			
			if ($tree[$class]) {
				include ($tree[$class].'/'.$class);
			} 
		}
	}
	
	if (!function_exists('user_level')) {
		function user_level($level)
		{
			return User::get_level() <= $level;	
		}
	}
	
	if (!function_exists('user_access')) {
		function user_access($action, $group = '')
		{
			$conf = Registry::get('conf');
			$sql = Registry::get('sql');
			
			$user_data = User::get_user();
	
			if (empty($user_data['my_permissions']) && $user_data['level'] !== 0) {
				return false;	
			} 
			
			if (!$group) $group = 'General';
			
			if ( !isset($user_data['role_permissions'][$group][$action]) ) { # first time seeing this permission, add to table
				$sql->query("SELECT permission_id FROM $conf->USER_PERMISSION WHERE action = '".$sql->sanitize($action)."' AND `group` = '".$sql->sanitize($group)."'");
				$row = $sql->fetch();
	
				if (!$row['permission_id']) {
					$sql->query("INSERT INTO $conf->USER_PERMISSION (action, `group`) VALUES ('".$sql->sanitize($action)."', '".$sql->sanitize($group)."')");
					$pid = $sql->insert_id();
				} else {
					$pid = $row['permission_id'];	
				}
	
				User::add_permission($group, $action, $pid);
			} 
	
			if ($user_data['level'] === 0) { # return true for super admins
				return true;	
			}
	
			if (isset($user_data['my_permissions'][   $user_data['role_permissions'][$group][$action]  ])) {
				return true;
			}
					
			return false;
		}
	}
	
	spl_autoload_register('class_autoload');
	
	 
	$conf = new Config; # load conf object
	Registry::set($conf, 'conf');

	# SITE CONFIGURATION SETTINGS
	$conf->set('root', $_file_root);
	$conf->set('path', $_path);	
	$conf->set('core', $_file_root.'/core');
	$conf->set('custom', $_file_root.'/custom');
	
	$conf->set('public_dir', '/public');
	$conf->set('public_file', $_file_root.$conf->public_dir);
	$conf->set('public', $_path.$conf->public_dir);
	$conf->set('base', $_path.'/base');
	
	$conf->set('cache_file', $conf->public_file.'/cache');
	$conf->set('cache', $conf->public.'/cache');
	
	$conf->set('http', 'http://'.$_SERVER['HTTP_HOST'].$_path.$request);
	$conf->set('https', 'http'.($_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_path.$request);
	$conf->set('ssl', 'http'.($conf->use_ssl ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_path.$request);
	$conf->set('base_http', 'http'.($_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['HTTP_HOST']);
	
	if (file_exists($conf->custom.'/inc/config.php')) {
		require_once($conf->custom.'/inc/config.php');
	}
	
	$conf->set('admin_alias', 'admin');
	$conf->set('admin', $_path.'/'.$conf->admin_alias);
	
	$conf->set('filemanager_alias', 'filemanager');
	$conf->set('filemanager_module', $conf->base.'/'.$conf->filemanager_alias);
	$conf->set('filemanager_upload', $conf->path.'/'.$conf->filemanager_alias.'/filemanager-upload');
	$conf->set('filemanager', $conf->path.'/'.$conf->filemanager_alias.'/filemanager');
	$conf->set('filemanager_ajax', $conf->path.'/'.$conf->filemanager_alias.'/filemanager-ajax');
	
	$conf->set('thumbnail_prefix', 'imgcache_');		
	$conf->set('thumbnails', array('small' => '450x450@aspect'));

	$conf->set('upload_dir', '/upload'); # user folder, make sure it's 777
	$conf->set('upload_file', $conf->public_file.$conf->upload_dir);		
	$conf->set('upload', $conf->public.$conf->upload_dir);

	$conf->set('editor', $conf->public.'/modules/ckeditor/ckeditor.js');
	
	$conf->set('img', $conf->public.'/img');	
		
	$conf->set('captcha', $conf->public.'/modules/captcha');		
	$conf->set('captcha_file', $conf->public_file.'/modules/captcha');		
	
	$conf->set('log_file', $conf->custom.'/inc/error.log');
	
	$conf->set('sql_datetime', '%m/%d/%Y %T');
	$conf->set('sql_date', '%m/%d/%Y');
	$conf->set('php_datetime', 'm/d/Y H:i:s');
	$conf->set('php_date', 'm/d/Y H:i:s');
	$conf->set('sql_formatted_datetime', '%b %d, %Y %r');
	$conf->set('sql_formatted_date', '%b %d, %Y');
	$conf->set('php_formatted_datetime', 'M d, Y h:i:s A');
	$conf->set('php_formatted_date', 'M d, Y');
	# DATABASE TABLES
	
	$conf->set('PAGE', $conf->db_prefix.'page');
	$conf->set('LOG', $conf->db_prefix.'log');
	$conf->set('SITE_SETTING', $conf->db_prefix.'site_setting');
	$conf->set('USER', $conf->db_prefix.'user');
   	$conf->set('USER_PERMISSION', $conf->db_prefix.'user_permission');
   	$conf->set('ROLE_PERMISSION', $conf->db_prefix.'user_role_permission');
   	$conf->set('USER_ROLE', $conf->db_prefix.'user_user_role');	
   	$conf->set('ROLE', $conf->db_prefix.'user_role');	
	$conf->set('WEBFORM', $conf->db_prefix.'webform');
	$conf->set('WEBFORM_FIELD',$conf->db_prefix.'webform_field');
	$conf->set('WEBFORM_SUBMISSION', $conf->db_prefix.'webform_submission');
	$conf->set('WEBFORM_SUBMISSION_FIELD', $conf->db_prefix.'webform_submission_field');
	$conf->set('CACHE', $conf->db_prefix.'cache');
	$conf->set('CACHE_PAGE', $conf->db_prefix.'cache_page');
	$conf->set('SESSION', $conf->db_prefix.'session');	
	$conf->set('DELETE_ARCHIVE', $conf->db_prefix.'delete_archive');	
	
	if ((!isset($conf->installed) || $conf->installed === false) && trim($_GET['request'],'/') != 'install') {
		header("Location: $conf->http/install"); exit;
	}
			
	if (isset($conf->dev_mode)) { 
		if ($conf->dev_mode) {
			ini_set('display_errors', false);
			error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);			
		} else {
			ini_set('display_errors', false);
		}
	}
	
	define ('STRIP', 0x0000); # used with MySQL objects to mask bits for sanitize settings
	define ('ESCAPE', 0x0001);
	define ('ENTITY', 0x0010);
	define ('DECODE', 0x0100);
	define ('STRIPSLASHES', 0x1000);
?>