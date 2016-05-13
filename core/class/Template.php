<?php
	class Template extends Base
	{
		protected $template_dir;
		protected $media_root;
		protected $css_root;
		protected $js_root;
		protected $default_template;
		protected $data = array();
		protected $local_base;
		protected $template = array();
		protected $template_full_path = array();
		protected $cache_time = 0;
		protected $custom_template_dir;
		protected $js = array();
		protected $css = array();
		protected $last_css = array();
		protected $last_js = array();
		protected $js_type = array();

		function __construct()
		{
			parent::__construct();
			
			$this->media_root = $this->conf->public;
			$this->css_root = $this->conf->public.'/css';
			$this->js_root = $this->conf->public.'/js';
			
			$this->core_media_root = $this->conf->base;
			$this->base_css_root = $this->conf->base.'/css';
			$this->base_js_root = $this->conf->base.'/js';
			
			$this->core_template_dir = $this->conf->core.'/template/';
			$this->custom_template_dir = $this->conf->custom.'/template/';
		}

		function assign($name, $value, $override = NULL)
		{
			if ($this->debug && !$this->file_src[$name]) {
				$debug = debug_backtrace();
				$this->file_src[$name]['file'] = $debug[0]['file'];
				$this->file_src[$name]['line'] = $debug[0]['line'];
			}
		
			if ($override || !isset($this->data[$name])) {
				$this->data[$name] =& $value;
			} else if ($override === NULL) {
				if ($this->debug) {
					$debug = debug_backtrace();
					$first = "It was first assigned in ".$this->file_src[$name]['file'].":".$this->file_src[$name]['line'];
					$last = "It was reassigned in ".$debug[0]['file'].":".$debug[0]['line'];
					
					die("<p>\$$name is being redeclared.</p><p>$first</p><p>$last</p><p>Override it or rename it.</p>");
				} else {	
					die("\$$name is being redeclared.");
				}
			}	
		}	
		
		function get_var($name)
		{
			return $this->data[$name];
		}
		
		function append($name, $value)
		{
			if (!is_array($this->data[$name])) {
				$this->data[$name] = array();	
			}
			
			$this->data[$name][] = $value;
		}
		
		function add_css($root, $css, $last=false)
		{
			$full_path = substr($css, 0, 4) != 'http' && substr($css,0,1) != '/' ? $root.'/'.$css : $css;
		
			if ($last) {
				$this->last_css[$full_path] = $full_path;
			} else {
				$this->css[$full_path] = $full_path;
			}	
		}
		
		function add_js($root, $js, $type, $last=false)
		{
			$full_path = substr($js, 0, 4) != 'http' && substr($js,0,1) != '/' ? $root.'/'.$js : $js;

			if ($last) {
				$this->last_js[$full_path] = $full_path;
			} else {
				$this->js[$full_path] = $full_path;
			}
			
			$this->js_type[$full_path] = $type;
		}
		
		function css($css, $last=false)
		{
			$this->add_css($this->css_root,$css,$last);
		}
		
		function base_css($css, $last=false)
		{
			$this->add_css($this->base_css_root,$css,$last);
		}

		function clear_js()
		{
			$this->js = array();
		}
		
		function js($js, $type = 'javascript', $last=false)
		{
			$this->add_js($this->js_root,$js,$type,$last);
		}
		
		function base_js($js, $last=false)
		{
			$this->add_js($this->base_js_root,$js, 'javascript', $last);
		}
		
		
		
		function & get_data()
		{
			return $this->data;
		}
				
		function set_template($id, $tpl)
		{
			$this->template[$id] = $tpl;
			
			if (file_exists($this->custom_template_dir.$tpl)) {
				$this->template_full_path[$id] = $this->custom_template_dir.$tpl;
			} else {
				$this->template_full_path[$id] = $this->core_template_dir.$tpl;
			}
		}
		function js_compress($string) 
		{
			#$string = preg_replace('~^//.*?$~m', '', $string);
			$string = preg_replace('~\/\*(.*?)\*\/~s', '', $string);
			/*$string = preg_replace('~^[\s]+$~m', '', $string);
			$string = preg_replace('~({)\s+~s', '$1', $string);
			$string = preg_replace('~(;)\s+~s', '$1', $string);
			$string = preg_replace('~\s+(})~', '$1', $string);
			$string = preg_replace('~\)\s+~', ')', $string);
			$string = preg_replace('~^\s+$~m', ')', $string);*/

			return trim($string);
    	}		
		function css_compress($string)
		{
			$string = str_replace('../../js/fancybox', '../../base/js/fancybox', $string);
			$string = preg_replace('~/\*[^*]*\*+([^/][^*]*\*+)*/~', '', $string); #comments
			$string = preg_replace('~(\:|;)\s+~', '$1', $string);
			$string = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $string);	
			/*
						$string = preg_replace('~(\s{2,}+)~s', ' ', $string);
			$string = preg_replace('~\/\*(.*?)\*\/~s', '', $string);
			$string = preg_replace('~(})(\s+)([^\s\t])~s', '$1$3', $string);
			$string = preg_replace('~([^\s\t])(\s+)({)~s', '$1$3', $string);
			$string = preg_replace('~({)(\s+)([^\s\t])~s', '$1$3', $string);
			$string = preg_replace('~(,)(\s+)([^\s\t])~s', '$1$3', $string);
			$string = preg_replace('~(;)(\s+)~s', '$1', $string);
			$string = preg_replace('~(:)(\s+)~s', '$1', $string);
*/
			
			
			
			return trim($string);
		}
		
		function display()
		{
			$tpl = new stdClass;
			$cache = false;
			
			if ($this->setting->css_caching == 'On' && !User::logged_in()) {	
				$cache = true;
			}

			if ($cache) {
				# CSS CACHING
				$hash = substr(md5(implode('', (array) $this->css). implode('', (array) $this->last_css)), 0 , 10);
				$file = $this->conf->public_cache_dir.'/'.$hash.'.css';
			
				if (file_exists($file)) {
					$time = filemtime($file) + $this->setting->cache_expiration;
					
					if ($time >= time()) {
						$this->data['css_inc'] = "<link href='".$this->conf->cache."/$hash.css' type='text/css' rel='stylesheet' />";
					} else {
						$cached_css_name = $this->conf->cache.'/'.$hash.'.css';
						$this->data['css_inc'] = "<link href='$cached_css_name' type='text/css' rel='stylesheet' />";
						$cache_css_request = true;	
					}
				} else {
					$cached_css_name = $this->conf->cache.'/'.$hash.'.css';
					$this->data['css_inc'] = "<link href='$cached_css_name' type='text/css' rel='stylesheet' />";
					$cache_css_request = true;	
				}
				
				# JS CACHING
				$hash = substr(md5(implode('', (array) $this->js). implode('', (array) $this->last_js)), 0 , 10);
				$file = $this->conf->public_cache_dir.'/'.$hash.'.js';
				
				if (file_exists($file)) {
					$time = filemtime($file) + $this->setting->cache_expiration;	
					if ($time >= time()) {
						$this->data['js_inc'] = "<script type='text/javascript' src='".$this->conf->cache."/$hash.js'></script>";
					} else {
						$cached_js_name = $this->conf->cache.'/'.$hash.'.js';
						$this->data['js_inc'] = "<script type='text/javascript' src='$cached_js_name'></script>";
						$cache_js_request = true;	
					}
				} else {
					$cached_js_name = $this->conf->cache.'/'.$hash.'.js';
					$this->data['js_inc'] = "<script type='text/javascript' src='$cached_js_name'></script>";
					$cache_js_request = true;	
				}
			}
			
			$csses = array_merge ((array) $this->css, (array) $this->last_css);
			
			$doc_root = $this->conf->path ? substr($this->conf->root, 0,  strpos($this->conf->root, $this->conf->path)) : $this->conf->root;
			
			if ($csses) {
				foreach ($csses as $k=>$v)
				{
					if ($cache_css_request) {
						$url = substr($v, 0, 4) == 'http' || substr($v, 0, 2) == '//' ? $v : $doc_root.$v;
						$css_string .= file_get_contents($url);
					} else if (!$cache) {
						$this->data['css_inc'] .= "<link href='$v' type='text/css' rel='stylesheet' />\n";
					}
				}
			}
			
			if ($cache_css_request && $cached_css_name) {
				if (!is_dir($this->conf->cache_file)) {
					mkdir($this->conf->cache_file);	
				}
				
				$css_string = $this->css_compress($css_string);
				file_put_contents($doc_root.$cached_css_name, $css_string);
			}
			
			$jses = array_merge ((array) $this->js, (array) $this->last_js);
			
			if ($jses) {
				foreach ($jses as $v)
				{
					if ($cache_js_request) {
						$url = substr($v, 0, 4) == 'http' || substr($v, 0, 2) == '//' ? $v : $doc_root.$v;
						$js_string .= file_get_contents($url);
					} else if (!$cache) {

						$this->data['js_inc'] .= "<script". ($this->js_type[$v] ? " type='text/".$this->js_type[$v]."'" : "") ." src='$v'></script>\n";
					}
				}
			}

			if ($cache_js_request && $cached_js_name) {
				if (!is_dir($this->conf->cache_file)) {
					mkdir($this->conf->cache_file);	
				}
				
				$js_string = $this->js_compress($js_string);
				file_put_contents($doc_root .$cached_js_name, $js_string);
			}
			
			if ($this->body_classes) {
				$this->assign('body_classes', implode(' ',$this->body_classes));
			}
			
			if ($this->template_full_path) {
				$this->parse_template('base',$this->template_full_path['base'],$tpl);
			}
						
			echo $tpl->base; 
		}
		
		function retrieve_template($tpl_id, $tpl_path, $depth)
		{
			if ($tpl_path && $tpl_id) {
				if (file_exists($tpl_path) && is_file($tpl_path)) {
					$temp = file_get_contents($tpl_path);
					$this->template_list[$depth][] = $tpl_id;
				
					if ($temp) {
						preg_match_all('~\$tpl->([a-zA-Z_]+)~', $temp, $match);
						
						if ($match[1]) {
							$depth = $depth + 1;
							foreach ($match[1] as $v)
							{
								$this->retrieve_template($v, $this->template_full_path[$v], $depth);
							}
						} 				
					}
				}
			}
		}
		
		function parse_template($tpl_id, $tpl_path, &$tpl)
		{
			if ($this->show_vars) {
				echo '<pre>'; print_r($this->data); die;	
			}
			
			if ($this->js_vars) {
				$this->data['js_vars'] = json_encode($this->js_vars);	
			}
			
			if ($this->head_inc) {
				$this->data['head_inc'] = implode('', $this->head_inc);	
			}
			
			$this->template_list = array();
			
			$this->retrieve_template($tpl_id, $tpl_path, 0);
			
			krsort($this->template_list);
			extract($this->data);
			
			if ($this->template_list) {
				foreach ($this->template_list as $_v)
				{
					foreach ($_v as $tpl_id)
					{
						if ($this->debug) {
							$_name = "<div class='template-name'>$tpl_id</div>";
						}
						
						ob_start();
							include($this->template_full_path[$tpl_id]);
						$_html = ob_get_clean();
						$tpl->$tpl_id = $_name.$_html;
					}
				}
			}
		}
		
		function head_inc($string)
		{
			$this->head_inc[] = $string;	
		}
		
		function js_var($key,$value)
		{
			$this->js_vars[$key] = $value;	
		}
			
		function body_classes($class)
		{
			$this->body_classes[] = $class;
		}
				
		function debug()
		{
			$this->debug = true;
		}
		
		function cache($sec = 0)
		{
			$this->cache_time = $sec;
		}
		
		function show_vars()
		{
			$this->show_vars = true;	
		}
						
		function fancybox()
		{
			$this->base_css($this->conf->base.'/js/fancybox/fancybox.css');
			$this->base_js('fancybox/fancybox.pack.js');
		}
		
		function jcarousel()
		{
			$this->base_css($this->conf->base.'/js/jcarousel/skin.css');
			$this->base_js('jcarousel.js');
		}
		
		function jquery_ui()
		{
			$this->base_js('jquery-ui.min.js');
			$this->base_css('jquery-ui.min.css');
		}
		
		function datetime_picker()
		{
			$this->base_js('jquery-datetimepicker.js');
			$this->base_css('jquery-datetimepicker.css');
		}
		
		function fileupload()
		{
			$this->base_css('fileupload.css');
			$this->base_js('fileupload.js');
			
	  		$s = array('g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10);
			$sizes = array();
			
			foreach (array(ini_get('post_max_size'), ini_get('upload_max_filesize')) as $size)
			{
	  			 $size = trim($size);
				 $sizes[] = intval( $size) * ($s[strtolower(substr( $size,-1))] ?: 1);
			}

			$max_upload_size = min($sizes);

			$this->assign('max_upload_size', $max_upload_size);
			
			$base = log($max_upload_size) / log(1024);
			$suffixes = array('', 'k', 'M', 'G', 'T');   

			$this->assign('max_upload_size_formatted', round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)]);
		}
	}
?>