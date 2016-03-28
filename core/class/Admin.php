<?php
	class Admin extends Base
	{
		protected $breadcrumb = array();
		public $admin_filter = array();
		
		function __construct()
		{	
			parent::__construct();
			
			$this->func = Registry::get('func');		
			$_SESSION['thumbnails'] = array();	
		}
		function set_primary_key($pk)
		{
			$this->pk = $pk;
			
			if ($this->tpl) {
				$this->tpl->assign('primary_key', $pk);
			}
		}
		
		function thumbnail($name,$ratio='aspect',$width=0,$height=0)
		{
			$_SESSION['thumbnails'][$name] = $width.'x'.$height.'@'.$ratio;
		}
		
		function set_tbl($tbl)
		{
			$this->tbl = $tbl;
		}
				
		function set_keyword($str)
		{
			$this->keyword = $str;
			
			if ($this->tpl) {
				$this->tpl->assign('management_header', $str);
				$this->tpl->assign('keyword', $str);
			}
		}
		
		function allow_add($flag=false)
		{
			$this->allow_add = $flag;
			
			if ($this->tpl) {
				$this->tpl->assign('allow_add',$flag);
			}
		}
		
		function allow_edit($flag=false)
		{
			$this->allow_edit = $flag;
			
			if ($this->tpl) {
				$this->tpl->assign('allow_edit',$flag);
			}
		}
		
		function allow_delete($flag=false,$log=true)
		{
			$this->allow_delete = $flag;
			
			if ($this->tpl) {
				$this->tpl->assign('allow_delete',$flag);
			}
		}
		
		function set_page($str)
		{
			$this->page = $this->func->build_path($str);
		}
		
		function set_manager($str)
		{
			$this->manager = $str;
		}
		
		function get_manager()
		{
			return $this->manager;
		}
						
		function clean_admin_name($str)
		{
			$item_name = strpos($str, ':') !== false ? substr($str, strpos($str, ':')+1) : $str;
			return ucwords(str_replace(array('_','-'), ' ', $item_name));
		}
		
		function admin_menu_ignore($type='admin')
		{
			return array('login','image-preview', 'logout', 'reset-password', 'webform-fields','webform-submissions','webform-generate','users_permissions');
		}
		
		function build_menu($nav,$ignore=array())
		{
			$http = $this->conf->admin;	
			
			$menu = array();
			
			$user = new User;
			$get = Registry::get('get');
			
			if (method_exists($this->func, 'admin_menu_icons')) {
				$css_icons = $this->func->admin_menu_icons();
			}
			
			foreach ($nav as $k=>$v) 
			{
				if (!is_array($v) && strpos($v, '.data.php') === false) continue;
				
				$v = str_replace('.data.php', '', $v);
				$k = str_replace('.data.php', '', $k);
				$subpage = $get->b;
				
				if (!in_array($k,$ignore)) {
					$li = '';
					
					if (is_array($v)) {
						$active_child = false;
						$child = '';
						
						
						
						foreach ($ignore as $ig)
						{
							if ($k.':'.$ig == $get->b) { # child item is hidden, use parent
								$subpage = $this->conf->admin_menu_parent;
								break;
							}
						}
						
						foreach ($v as $key=>$val)
						{
							if (!ctype_digit($k) && $val != $k && !in_array($val, $ignore)) {	
								if (!is_array($val) && (user_access('Access '.$this->clean_admin_name($val), 'Administrator'))) {
									$here = $subpage == "$k:$val" ? 'here' : '';
									
									if ($here) {
										$active_child = true;
									}
									
									if ($k != 'administrator') {
										$this->admin_menu_raw["$k:$val"]['name'] = $this->clean_admin_name($val);
										$this->admin_menu_raw["$k:$val"]['icon'] = $css_icons[$key];
									}
									
									$child .= "<li><a class='menu-$val $here' href='$http/$k:$val'><span class='icon ".$css_icons[$key]."'></span>".$this->clean_admin_name($val)."</a></li>";
								}
							} 
						}
						
						if ($child) {
							$child = "<ul class='sub'>".$child.'</ul>';
							
							$here = $subpage == "$k:$k" || $active_child ? 'here' : '';
							
							if (in_array($k, $v)) { # make it a link
								$this->admin_menu_raw["$k:$k"]['name'] = $this->clean_admin_name($k);
								$this->admin_menu_raw["$k:$k"]['icon'] = $css_icons[$k];
							
								$li .= "<a class='$here main' href='$http/$k:$k'><span class='icon ".$css_icons[$k]."'></span>".$this->clean_admin_name($k)."<span class='arrow icon-arrow-down'></span></a>";
							} else { 
								$li .= "<a class='$here main'><span class='icon ".$css_icons[$k]."'></span>".$this->clean_admin_name($k)."<span class='arrow icon-arrow-down'></span></a>";
							}	
							
							$li .= $child;
						}
					} else if (user_access('Access '.$this->clean_admin_name($v), 'Administrator')) {
						foreach ($ignore as $ig)
						{
							if ($ig == $get->b) { # child item is hidden, use parent
								$subpage = $this->conf->admin_menu_parent;
								break;
							}
						}
						
						$here = $subpage == $v ? 'here' : '';
						$this->admin_menu_raw[$v]['name'] = $this->clean_admin_name($v);
						$this->admin_menu_raw[$v]['icon'] = $css_icons[$v];
						
						$li .= "<a href='$http/$v' class='$here main'><span class='icon ".$css_icons[$v]."'></span>".$this->clean_admin_name($v)."</a>";
					}
					
					if ($li) {
						$menu[] = '<li>'.$li.'</li>';
					}
				} 
			}
			
			return $menu;		
		}
		
		function menu_sort($a,$b)
		{
			echo $a.'-'.$b.'<br/>';
		}
		
		function deep_ksort(&$arr) 
		{
			ksort($arr);
			foreach ($arr as &$a) 
			{
				if (is_array($a) && !empty($a)) {
					$this->deep_ksort($a);
				}
			}
		} 
				
		function array_merge_recursive_distinct( &$array1, &$array2 )
		{
			$merged = $array1;
			
			foreach ( $array2 as $key => &$value )
			{
				if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
					$merged [$key] = $this->array_merge_recursive_distinct ( $merged [$key], $value );
				} else {
					$merged [$key] = $value;
				}
			}
			
			return $merged;
		}
		
		function get_file_system($folder)
		{		
			$r = array();
			foreach (scandir($folder) as $item) 
			{
				if ($item != '.' && $item != '..' && $item != '_notes') {
					if (is_dir($folder.'/'.$item)) {
						$r[$item] = $this->get_file_system($folder.'/'.$item);
					} else {
						$r[str_replace('.data.php', '', $item)] = $item;
					}
				}
			}
			
			return $r;
		}
		
		function admin_manager_menu()
		{
			$custom = $core = $menu = array();
			$ignore = $this->admin_menu_ignore();
			$ignore2 = $this->func->admin_menu_ignore();
			
			if (is_array($ignore2) && $ignore2) {
				$ignore = array_merge($ignore, $ignore2);	
			}
			
			if (is_dir($this->conf->custom.'/data/admin')) {
				$custom = $this->get_file_system($this->conf->custom.'/data/admin'); # scan manager folder to see what we have.
			}
			
			$core = $this->get_file_system($this->conf->core.'/data/admin'); # scan manager folder to see what we have.

			$nav = $this->array_merge_recursive_distinct($core, $custom);

			#unset($nav['administrator']);
			
			$this->deep_ksort($nav);

			$menu = $this->build_menu($nav,$ignore);
			
			return "<ul class='admin-menu menu'>".implode('', $menu)."</ul>";
		}

		
		function toggle($id=0)
		{		
			if ($this->data[$this->toggle] && $this->data[$this->toggle]['toggle'] == 'toggle') {			
				$this->sql->query("
					UPDATE $this->tbl 
						SET `$this->toggle` = CASE `$this->toggle`
							WHEN 0 THEN 1
							ELSE 0
						END 
					WHERE $this->pk = ".intval($id ? $id : $this->id)
				);	
				
				$this->msg = $this->keyword.' has been toggled successfully.';
					
				$this->post_process($this->msg, array('toggle'=>$this->toggle, $this->pk=>intval($id ? $id : $this->id)));	
			} else {
				$this->post_process('Failed to toggle', array('toggle'=>$this->toggle, $this->pk=>intval($id ? $id : $this->id)), 'notice');	
			}
		}
		
		function insert($data, $return = false)
		{
			if ($this->allow_add) {	
				$raw_data = $data;			
				$data = $this->sql->sanitize($data);
				
				foreach ($data as $k=>$v)
				{
					if ($k && $k != 'group_fields') {
						$col[] = "`".$k."`";
						$val[] = "'$v'";
					} 
				}

 				$this->sql->query("INSERT INTO $this->tbl (".implode(',', $col).") VALUES (".implode(',', $val).")");
				
				$id = $this->sql->insert_id();
				
				if ($data['group_fields']) {
					foreach ($data['group_fields'] as $group=>$rows)
					{
						$tbl = $rows['tbl'];
						
						if ($rows['data']) {
							foreach ($rows['data'] as $index=>$data_gf)
							{
								
								$c = $vals = array();
								
								foreach ($data_gf as $key=>$val)
								{
									if (is_array($val)) {
										$val = $this->sql->sanitize(json_encode($raw_data['group_fields'][$group]['data'][$index][$key]));
									}
									
									if ($key) {
										$c[] = "`".$key."`";
										$vals[] = "'$val'";
									} 
								}
								
								$this->sql->query("INSERT INTO $tbl ($this->pk, ".implode(',', $c).", weight) VALUES ($id, ".implode(',', $vals).", '".intval($index)."')");
							}
						}
					}
				}
				
				
				$data['insert_id'] = $id;
				$this->msg = 'A new '.strtolower($this->keyword).' has been inserted';	
				
				if ($return) {
					return array('msg'=>$this->msg, 'data'=>$data, 'status'=>true);
				} else {
					$this->post_process($this->msg, $data);	
				}
			} else {
				if ($return) {
					return array('msg'=>'Tried to insert but inserting is disabled', 'data'=>$data, 'status'=>false);
				} else {
					$this->post_process('Tried to insert but inserting is disabled', $data, 'notice');
				}
			}
		}
		
		function update($data, $return = false)
		{
			if ($this->allow_edit) {		
				$raw_data = $data;			
				$data = $this->sql->sanitize($data);

				foreach ($data as $k=>$v)
				{
					if ($k && $k != 'group_fields') {
						$field[] = "`$k` = '$v'";
					} 
				}
				
				if ($field) {
					$this->sql->query("UPDATE $this->tbl SET ".implode(',',$field)." WHERE $this->pk = $this->id");
				}
				
				if ($data['group_fields']) {
					foreach ($data['group_fields'] as $group=>$rows)
					{
						$tbl = $rows['tbl'];
						if ($tbl == $this->tbl) die("Group field table cannot be the same as the primary table");
						
						$this->sql->query("DELETE FROM $tbl WHERE `$this->pk` = '$this->id'");
						
						if ($rows['data']) {
							foreach ($rows['data'] as $index=>$data_gf)
							{
								$c = $vals = array();
								
								if (count($rows['data'])-1 <= $index) { # if it's the last row and there's no values, don't save it.
									if (!trim(implode('', $v))) {
										break;	
									}
								}
								
								foreach ($data_gf as $key=>$val)
								{
									if (is_array($val)) {
										$val = $this->sql->sanitize(json_encode($raw_data['group_fields'][$group]['data'][$index][$key]));
									}
									
									if ($key) {
										$c[] = "`".$key."`";
										$vals[] = "'$val'";
									} 
								}
								
								$this->sql->query("INSERT INTO $tbl ($this->pk, ".implode(',', $c).", weight) VALUES ($this->id, ".implode(',', $vals).", ".intval($index).")");
							}
						}
					}
				}
				
				
				$this->msg = 'A '.strtolower($this->keyword).' has been updated';
				
				$data['update_id'] = $this->id;
				
				if ($return) {
					return array('msg'=>$this->msg, 'data'=>$data, 'status'=>true);
				} else {
					$this->post_process($this->msg, $data);	
				}
			} else {
				if ($return) {
					return array('msg'=>'Tried to update but updating is disabled', 'data'=>$data, 'status'=>false);
				} else {
					$this->post_process('Tried to update but updating is disabled', $data, 'notice');
				}
			}
		}
		
		function delete($return = false)
		{
			if ($this->allow_delete && $this->ids) {
				$ids = explode(',', $this->ids);

				foreach ($ids as $id)
				{
					if ($id) {
						$this->sql->query("SELECT * FROM $this->tbl WHERE $this->pk = ".intval($id)."");
						$data = $this->sql->fetch();
						
						$this->sql->query("DELETE FROM $this->tbl WHERE $this->pk = ".intval($id)."");
						
						if ($this->dependency) {
							foreach ($this->dependency as $tbl)
							{
								$this->sql->query("SELECT * FROM $tbl WHERE $this->pk = ".intval($id)."");
								while ($row = $this->sql->fetch())
								{
									$data['dependent_tables'][$tbl][] = $row;
								}
								
								$this->sql->query("DELETE FROM $tbl WHERE $this->pk = ".intval($id)."");
							}
						}
					}
				}
				
				$this->func->copy_delete_data($this->tbl, $data);
				
				$this->msg = $this->keyword.' has been removed';

				if ($return) {
					return array('msg'=>$this->msg, 'data'=>$data, 'status'=>true);
				} else {
					$this->post_process($this->msg, $data);	
				}
			} else {
				if ($return) {
					return array('msg'=>'Tried to delete but deleting is disabled', 'data'=>$data, 'status'=>false);
				} else {
					$this->post_process('Tried to delete but deleting is disabled', $data, 'notice');
				}
			}
		}
		
		function add_field($name, $label = '', $opt = '', $map=array())
		{			
			if (!$label) {
				$label = ucwords(str_replace('_',' ',$name));
			}
			
			$this->data[$name]['label'] = $label;
			
			if ($opt) {
				$options = explode('|', $opt);
				
				foreach ($options as $v)
				{
					if (in_array($v, array('left','center','right'))) {
						$this->data[$name]['align'] = $v;
					}
					
					if (in_array($v, array('sort','nosort'))) {
						$this->data[$name]['sort'] = $v == 'sort' ? true : false;
					}
					
					if (is_numeric($v) || $v == '*') {
						$this->data[$name]['width'] = $v;
					}
					
					if (substr($v,0,3) == 'cut') {
						$this->data[$name]['cut'] = substr($v, 3);
					}
					
					if ($v == 'date') {
						$this->data[$name]['format'] = 'date';
					} else if  ($v == 'datetime') {
						$this->data[$name]['format'] = 'datetime';
					} else if  ($v == 'time') {
						$this->data[$name]['format'] = 'time';
					}
					
					if ($v == 'money') {
						$this->data[$name]['format'] = 'money';
					}
					
					if ($v == 'percent') {
						$this->data[$name]['format'] = 'percent';
					}
					
					if (substr($v,0,3) == 'img') {
						$temp = end(explode('=',$v));
						
						$height = $temp[1] ? intval($temp[1]) : 16;
						
						$this->data[$name]['img'] = $height;
					}
					
					if (substr($v,0,8) == 'callback') {
						$this->data[$name]['callback'] = end(explode('=',$v)); # callback=function_name
					}
					
					if ($v == 'strip') {
						$this->data[$name]['strip'] = true;
					}
					
					if ($v == 'edit') {
						$this->data[$name]['edit'] = true;
					}
					
					if ($v == 'html') {
						$this->data[$name]['html'] = true;
					}
					if ($map) {
						$this->data[$name]['map'] = $map;
					}
					
					if (!$this->data[$name]['align']) {
						$this->data[$name]['align'] = 'left';	
					}
				}
			}			
		}
		
		function add_toggle($name, $label, $active_icon='', $inactive_icon='', $active_class='',$inactive_class='', $link = true)
		{
			$this->data[$name]['label'] = $label;
			$this->data[$name]['align'] = 'center';
			$this->data[$name]['valign'] = 'middle';
			$this->data[$name]['width'] = 25;
			$this->data[$name]['active_icon'] = $active_icon ? $active_icon : 'icon-circle2';
			$this->data[$name]['inactive_icon'] = $inactive_icon ? $inactive_icon : 'icon-circle2';
			$this->data[$name]['active_class'] = $active_class ? $active_class : 'toggle-status-active';
			$this->data[$name]['inactive_class'] = $inactive_class ? $inactive_class : 'toggle-status-inactive';
			$this->data[$name]['toggle'] = true;			
			$this->data[$name]['clickable'] = $link;			
		}
		
		function add_link($url, $param, $btn, $header='',$class='')
		{
			if (preg_match('~\.(gif)|(jpg)|(png)~i', $btn)) {
				$this->data[$url.$param]['btn'] = "<img src='".$this->conf->icon."/$btn' />";
			} else {
				$this->data[$url.$param]['btn'] = "<span class='icon $btn'></span>";
			}
			
			$this->data[$url.$param]['param'] = $param;			
			$this->data[$url.$param]['class'] = $class.' field-link';			
			$this->data[$url.$param]['url'] = $url;			
			$this->data[$url.$param]['label'] = $header;
			$this->data[$url.$param]['width'] = 25;
			$this->data[$url.$param]['align'] = 'center';
		}	
		
		function format($type, $value)
		{
			if ($type == 'date') {
				if (substr($value, 0, 4) == '0000') {
					$ret = 'Never';
				} else {
					$ret =  date('M d, Y', strtotime($value.' '.$this->conf->db_timezone));
				}
			} else if ($type == 'datetime') {
				if (substr($value, 0, 4) == '0000') {
					$ret = 'Never';
				} else {
					$ret =  date('M d, Y g:i:s A', strtotime($value.' '.$this->conf->db_timezone));
				}				
			} else if ($type == 'time') {
				$ret = $this->func->time($value);	
			} else if ($type == 'money') {
				$ret = money_format('%2n', $value);
			} else if ($type == 'percent') {
				$ret = $value.'%';
			}
			
			return $ret;
		}
		
		function get_fields(& $data)
		{
			$r['header'] = $this->data;
			$delete = array();
			
			if ($data) {
				foreach ($data as $k=>$v) 
				{
					$row_id = $v[$this->pk];
					foreach ((array) $this->data as $key=>$val)
					{
						$string = $v[$key];
						
						if ($val['callback']) {
							if (function_exists($val['callback'])) {
								$string = call_user_func($val['callback'], $v, $key);
								
								if ($string === false) {
									unset($r['data'][$row_id]);
									continue 2;
								}
							} else {
								$this->func->set_msg('The callback function for '.$val['label'].' - '.$val['callback'].' does not exist');
							}
						} 
					
						if ($val['format']) {
							$string = $this->format($val['format'], $string);
						} 
												
						if ($val['cut']) {
							$temp = strip_tags($string);
							$len = strlen($temp);
							
							if ($len > $val['cut']) {
								$string = substr($temp ,0, $val['cut']).'..';
							} else {
								$string = $temp;
							}
						} 
						
						if ($val['strip']) {
							$string = strip_tags($string);
						} 							
						
						
						if ($val['url']) {
							$string = "<a class='$val[class]' confirm_text='".strtolower($val['label'])." this item' href='".$this->page($val['param'].$row_id, $val['url'])."'>$val[btn]</a>";
						} else if ($val['toggle']) {
							
							$status = $string ? 'active' : 'inactive';
							$alt_status = !$string ? 'inactive' : 'active';
							
							if ($string) {
								$string = "<span status='active' class='icon $val[active_icon] $val[active_class]' active='$val[active_icon]' inactive='$val[inactive_icon]' active_class='$val[active_class]' inactive_class='$val[inactive_class]'></span>";
							} else {
								$string = "<span status='inactive' class='icon $val[inactive_icon] $val[inactive_class]' active='$val[active_icon]' inactive='$val[inactive_icon]' active_class='$val[active_class]' inactive_class='$val[inactive_class]'></span>";
							}
							
							if ($val['clickable']) {
								$string = "<a class='control type-toggle ajax' href='".$this->page("action=toggle&id=$row_id&toggle=$key")."'>".$string."</a>";
							}

						} else if ($val['img']) {
							$string = $string ? "<a class='popup' href='".$this->conf->path.$string."'><img src='".$this->func->get_img($string, 'small')."' height='".$val['img']."'/></a>" : '';
						} else if (!$val['html']) {
							$string = htmlspecialchars($string);
						} 
						
						if ($val['map']) {
							$string = $val['map'][$string];
						}
						
						if ($val['edit']) {
							$string = "<a href='".$this->page("action=edit&id=$row_id")."'>".($string ? $string : 'Edit')."</a>";
						}
						
						$r['data'][$row_id][$key] = stripslashes($string);
					}
					
				}
			}
			
			
			
			return $r;
		}
		
		function add_dependency($tbl)
		{
			$this->dependency[] = $tbl;	
		}
		
		function form_field($name, $label, $type, $opt = '', $list = array())
		{			
			if (is_string($opt)) {
				if ($type != 'html') {
					$temp = explode('|', $opt);
					$opt = array();
					foreach ($temp as $v)
					{
						if (strpos($v, ':')) {
							list($key, $val) = explode(':', $v);
							$opt[$key] = $val;
						} else {
							$opt[$v] = true;	
						}
					}
				} else {
					$html = $opt;
					$opt = array();
					
					$opt['html'] = $html;	
				}
				
			}
			
			if ($list) {
				$opt['list'] = $list;
			}
		
		
			if ($opt['repeat'] && $opt['table']) {
				$this->add_dependency($opt['table']);
			}
			
			$group_name = $opt['group'] ? $opt['group'] : $name;
			
			if ($opt['repeat'] || $opt['multiple']) {
				if ($opt['table']) {
					$this->form_groups[$group_name] = $opt;
				} else {
					if ($this->display_admin_error !== false) {
						$this->func->set_msg('No table defined for '.$label.' but set to repeat');
					}
				}
			}
			
			if ($opt['tab']) {
				$this->form_tabs[$opt['tab']][$group_name] = true;	
			} else {
				$this->form_tabs['General'][$group_name] = true;	
			}

			$this->form[$group_name][$name]['label'] = $label;
			$this->form[$group_name][$name]['type'] = $type;
			$this->form[$group_name][$name]['option'] = $opt;
		}
		
		function error($var)
		{
			$this->display_admin_error = $var;	
		}
		
		function reset_form()
		{
			$this->form = array();	
		}
		
		function set_edit_query($str)
		{
			$this->edit_query = $str;
		}	
		
		function build_form_table($group, $data = array())
		{
			$rows = $this->form[$group];

			$html = "<table cellspacing='0' class='form-tbl form-tbl-inner'>";
			
			$first = false;
				
			$temp_html = '';						
			
			if ($rows) {
				$width = floor(100 / count($rows));
			}
			
			$multi = count($rows) > 1;
			$index = 0;
			
			if (!$multi && $rows[$group]['type'] == 'hidden' ) {
				$index_all++;
				$html .= $this->field_html($rows[$group]['type'],$group,$data,$rows[$group]['option'], $index_all);
			} else {
				$repeat = $field_repeat = false;
				
				foreach ((array) $rows as $name=>$row)
				{	
					if ($row['option']['repeat']) {
						$repeat = true;
					} 
					
					if ($row['option']['field_repeat']) {
						$field_repeat = true;	
					}
					
					if ($row['option']['multiple']) {
						$multiple = true;	
					}
				}
				
				$keys = '';
				
				if ($rows) {
					foreach(array_keys($rows) as $v)
					{
						$keys .= "field-$v ";	
					}
				}
									
				$temp_html .= "<div class='field group-field'>";	
				
				do {
					$data_row = isset($data['children_data'][$group]) ? $data['children_data'][$group][$index] : $data;
					$index++;

					if ($multiple && isset($data['children_data'][$group])) {
						$data_row = array();
						
						foreach ((array) $data['children_data'][$group] as $k=>$v)
						{
							foreach ($v as $key=>$val)
							{
								if ($key == $group) {
									$data_row[$group][] = $val;	
								}
							}
						}
						
						unset($data['children_data'][$group]);
					}
				
					$temp_html .= "<table cellspacing='0' width='100%' class='form-group-tbl ".($repeat ? 'repeat' : '')." ".($multi ? 'multiple' : '')."'><tr>";
					
					if ($repeat) {
						$temp_html .= "<td width='30' class='move row-move'><span class='icon icon-menu10'></span></td>";
					}
					$col_count = 0;
					
					foreach ((array)$rows as $name=>$row)
					{	
						$index_all++;
						$col_count++;
						
						$required = (isset($row['option']['required']) && $row['option']['required']) ? "<span class='required'>*</span>" : '';
						
						$field_label = $multi ? "<label class='group-field-label'>$required$row[label]</label>" : '';
						
						$row_label = $multi ? $group: $required.$row['label'];
						
						$temp_html .= "<td width='$width%' class='field-col' col='$col_count'>";
						
						
						if (isset($row['option']['field_repeat']) && $row['option']['field_repeat']) {
							$temp_html .= $field_label;
							
							$field_index = 0;
							$field_rows = array();
							
							if ($data_row[$name]) {
								$field_rows = json_decode($data_row[$name]);
							} else if ($data_row) {
								$field_rows = $data_row;	
								
							}
							
							$temp_html .= "<div class='field group-field inner-group-field'>";
							
							do {
								$temp_html .= "<table cellspacing='0' width='100%' class='form-group-tbl form-field-group-tbl ".(($row['option']['repeat']) ? 'repeat' : '')." ".($multi ? 'multiple' : '')."'><tr>";
								
								if ($row['option']['repeat']) {
									$temp_html .= "<td width='30' class='move field-move'><span class='icon icon-menu10'></span></td>";
								}
				
								$temp_html .= "<td width='100%' class='field-col' col='$col_count'>";
								$temp_html .= "".$this->field_html($row['type'],$name,$field_rows[$field_index],$row['option'], $index_all, $index, $repeat);
								$temp_html .= "</td>";
								
								if ($row['option']['repeat']) {
									$temp_html .= "<td width='30' class='delete'><span class='icon icon-remove2'></span></td>";	
								}
								
								$temp_html .= "</tr></table>";
								
								$field_index++;
							} while ($field_rows[$field_index]);
							
							$temp_html .= "</div>";
							
							if ($row['option']['repeat']) {
								$temp_html .= "<div class='more-btn'><input type='button' class='add-another' value='+Add Another' /></div>";
							}
							
						} else {
							$temp_html .= "$field_label".$this->field_html($row['type'],$name,$data_row,$row['option'], $index_all,$index, $repeat);
						}
						
						$temp_html .= "</td>";
					}
					
					if ($repeat) {
						$temp_html .= "<td width='30' class='delete'><span class='icon icon-remove2'></span></td>";	
					}
					$temp_html .= "</tr></table>";
				} while (is_array($data) && isset($data['children_data'][$group][$index]));
				
				$temp_html .= "</div>";	
				
				if ($repeat) {
					$temp_html .= "<div class='more-btn'><input type='button' class='add-another add-another-row' value='+Add Another Row' /></div>";	
				}
				
				$html .= "<tr class='$keys'><th width='120'>$row_label</th><td class='group-field-td'>$temp_html</td></tr>";				
			}
			
			$html .= "</table>";
			
			return $html;
		}
				
		function form($data=array())
		{
			if ($this->form) {	
				$html = "<form method='post' action='".$this->page()."' id='admin-form'>";
				$html .= "<table cellspacing='0' class='form-tbl'><tr><td class='tab-list'>";	
				
				$index_all = 0;
				$first = true;
				
				foreach ($this->form_tabs as $tab_name=>$tabs)
				{
					$active = $first ? 'active' : '';
					$html .= "<div class='tab-name $active' tab_name='$tab_name'>$tab_name</div>";
					$first = false;
				}
				
				$html .= "</td><td class='tab-content-td'>";
				
				$first = true;
				
				foreach ($this->form_tabs as $tab_name=>$tabs)
				{
					$active = $first ? 'active' : '';
					$html .= "<div class='tab-content $active' tab_name='$tab_name'>";
					
					foreach ($tabs as $group=>$tab)
					{
						$html .= $this->build_form_table($group, $data);
					}
					
					$html .= '</div>';
				}
				
				$html .= "<input type='hidden' name='form_submitted' value='1' />";
				$html .= "<input type='hidden' name='action' value='submit_$this->action' />";
				$html .= "<input type='hidden' name='action_id' value='$this->id' />";
				
				if ($this->parent_key && $this->parent_key_id) {
					$html .= "<input type='hidden' name='$this->parent_id' value='".$this->parent_key_id."' />";
				} else if ($this->pid && $this->parent_id) {
					$html .= "<input type='hidden' name='$this->parent_id' value='$this->pid' />";
				}
				
				$html .= "<tr><td style='background: none;'></td><td class='submit-td'>";
				$html .= "<label>&nbsp;</label><input type='submit' class='btn' id='submit' value='Submit' />";
				
				if ($this->get->destination) {
					$html .= "&nbsp; <input type='button' class='btn' id='cancel' value='Cancel' onclick='window.location=\"".$this->func->base64_decode($this->get->destination)."\"'/>	";
				} else {
					$html .= "&nbsp; <input type='button' class='btn' id='cancel' value='Cancel' onclick='window.location=\"".$this->page()."\"'/>	";
				}
				
				$html .= "</td></tr>";
				$html .= '</table>';
				$html .= '</form>';
				
				$this->tpl->assign('html', $html);
			} else if ($this->action == 'edit') {
				$this->tpl->assign('form', $data);
			}
			
			$this->tpl->assign('action', $this->action);
			$this->tpl->assign('action_id', $this->id);
			
			$this->tpl->set_template($this->update_tpl_id,$this->update_tpl);
		}
				
		function field_html($type, $name, $data, $option, $index = 0, $row_index = 0, $repeat = false, $field_repeat = false)
		{
			$html = '';
			$index_name = $name;
			
			if ($repeat) {
				$name = $name.'['.$row_index.']';
			} 
			
			if (isset($option['multiple']) && $option['multiple']) {
				$name = $name.'[]';
			}
				
			if (isset($option['field_repeat']) && $option['field_repeat']) {
				$name = $name.'[]';
			}		
			
			if ($data) {
				if ((is_array($data) && !key($data)) || !is_array($data)) {
					$data = array($index_name=>$data);	
				} 
			} 
			
			if (isset($option['copy']) && $option['copy']) {
				$data[$index_name] = $data[ $option['copy'] ];
			}
		
			$conf = $this->conf;
			if (!is_array($option)) {
				$option_string = $option;
				unset($option);
			} 
			
			if (isset($option['default_value']) && $this->action == 'add') {
				$data[$index_name] = $option['default_value'];
			}
			
			
			if (isset($option['help']) && $option['help']) {
				$help_text = $option['help'];
				
				if ($option['help'] == 'none') {
					$help_text = '';	
				}
			}
			
			if (isset($option['final']) && $option['final'] && $data[$index_name]) { 
				$option_string .= " readonly='readonly' ";
			}

			$required = isset($option['required']) && $option['required'] ? 'required' : '';
			
			if (isset($option['empty']) && $option['empty'] && empty($data[$index_name])) {
				$data[$index_name] = $option['empty'];
			}
			
			switch ($type)
			{	
			
				case 'html' :
					$html = $option['html'];
					break;
				case 'weight' :
					if (!$data[$index_name]) {
						$data[$index_name] = $this->get_next_weight($index_name);
					}
				case 'money' :
					if ($data[$index_name]) {
						$data[$index_name] = preg_replace('~[^0-9.]~', '', $data[$index_name]);
					}
					
				case 'email' :
				case 'text' :
					$html = "<input $option_string type='text' name='$name' value='".htmlspecialchars($data[$index_name], ENT_QUOTES)."' class='text $required' />";
					break;
				
				case 'time' :
				
					if ($this->action == 'add') {
						$time = date('h:i');
						$meridiem = date('A');
					} else {
						$temp = explode(' ', $this->func->time($data[$index_name]));
						$time = $temp[0];
						$meridiem = $temp[1];
					}
					

					$html = "<input  $option_string type='text' name='{$name}[time]' value='".$time."' class='text time $required' />";
					
					$html .= " <select name='{$name}[meridiem]'><option value='AM' ".($meridiem == 'AM' ? "selected='selected'" : '').">AM</option><option value='PM' ".($meridiem == 'PM' ? "selected='selected'" : '').">PM</option></select>";
				
					break;	
					
				case 'list':

					if (isset($option['list'])) {
						$children = $option['list'];
					} else if (is_array($option)) {
						$children = $option;
					}
					
					$multiple = $option['multiple'] ? 'checkbox' : 'radio';
					
					if ($children) {
						foreach ($children as $k=>$v)
						{
							if (is_array($v)) {	
								$html .= "<div class='checkbox-parent'>'$k'</div>";	
								
								foreach ($v as $key=>$val)
								{
									if (!is_array($data[$index_name])) {
										$sel = $data[$index_name] == $key ? "checked='checked'" : '';
									} else {
										$sel = in_array($key, $data[$index_name]) ? "checked='checked'" : '';
									}
									
									$html .= "<label class='checkbox-field'><input $option_string type='$multiple' name='$name' value='$key' $sel class='checkbox $required' /> $val</label><br>";
								}
							} else {
								if (!is_array($data[$index_name])) {
									$sel = $data[$index_name] == $k ? "checked='checked'" : '';
								} else {
									$sel = in_array($k, $data[$index_name]) ? "checked='checked'" : '';
								}
								
								$html .= "<label class='checkbox-field'><input $option_string type='$multiple' name='$name' value='$k' $sel class='checkbox $required' /> $v</label><br>";
							}
						}
					}
					
				
					break;
				case 'select' :
					if (isset($option['list'])) {
						$children = $option['list'];
					} else {
						$children = $option;
					}
																
					if ($option['ignore_self']) {
						unset($children[$this->id]);
					}
					
					if ($this->action == 'add' && $this->get->$name && !$data[$index_name]) {
						$data[$index_name] = $this->get->$name;
					}
					if (isset($option['multiple']) && $option['multiple']) {	
						$size = $option['size'] ? $option['size'] : 5;						
						$multiple = "multiple='multiple' size='$size'";
						$name = str_replace('[]', '', $name).'[]';
					} else {
						$multiple = "";
					}
					
					if (isset($option['conditions']) && is_array($option['conditions'])) {
						$conditional_json = "conditions='".str_replace("'", "\'", json_encode($option['conditions']))."'";
					}
					
					if (isset($option['parent']) && $option['parent']) {
						$html = "<select $conditional_json $multiple $option_string name='$name' base_name='".str_replace('[]', '', $name)."' class='select $required has_parent' parent='".$option['parent']."'>";
					} else {
						$html = "<select $conditional_json $multiple $option_string name='$name' base_name='".str_replace('[]', '', $name)."' class='select $required'>";
					}
					
					if (!isset($option['multiple']) || !$option['multiple']) {	
						$html .= "<option value='' class='option'> -- Please Select -- </option>";
					}
					
					if ($this->admin_filtered[$index_name] && !$data[$index_name]) {
						$data[$index_name] = $this->admin_filtered[$index_name];	
					}
					
					if ($children) {
						foreach ($children as $k=>$v)
						{
							if (is_array($v)) {	
								if (!$option['parent']) {
									$html .= "<optgroup label='$k'>";	
								}
								foreach ($v as $key=>$val)
								{
									if (!is_array($data[$index_name])) {
										$sel = $data[$index_name] == $key ? "selected='selected'" : '';
									} else {
										$sel = in_array($key, $data[$index_name]) ? "selected='selected'" : '';
									}
									
									$html .= "<option parent_id='$k'  value='$key' $sel>$val</option>";
								}
								
								if (!$option['parent']) {
									$html .= "</optgroup>";	
								}
							} else {
								if (!is_array($data[$index_name])) {
									$sel = $data[$index_name] == $k ? "selected='selected'" : '';
								} else {
									$sel = in_array($k, $data[$index_name]) ? "selected='selected'" : '';
								}
								$html .= "<option value='$k' $sel>$v</option>";
							}
						}
					}
					$html .= "</select>";				
					break;
				
				case 'document' :
				case 'image' :
				case 'file' :
					$multiple = $repeat || $option['field_repeat'] ? "multiple='multiple'" : '';	
				
					if ($data[$index_name]) {
						$html = "
							<div class='fm-file-row'>
								<input type='hidden' name='$name' class='fm-file' value='".$data[$index_name]."'>
								<div class='fm-img-preview'>
									<div class='fm-img'><img src='" . $this->conf->path.$data[$index_name] . "'></div>
									<div class='clear-img-btn'>".end(explode('/', $data[$index_name]))."<button type='button' class='fm-img-delete btn'>Remove</button></div>
									<div class='clear'></div>
								</div>
								
								<div class='progress-box'>
									<span class='progress-header'>Uploading</span>
									<div class='progress-bar-box'><div class='progress-bar'></div></div>
								</div>
								<a class='browse-file btn' style='display: none;'>Browse</a>
								<span class='file-input'><input class='upload-file' $multiple type='file' ></span>
								<div class='clear'></div>
							</div>
						";
					} else {
						$html = "
							<div class='fm-file-row'>
								<input type='hidden' name='$name' class='fm-file'>
								<div class='progress-box'>
									<span class='progress-header'>Uploading</span>
									<div class='progress-bar-box'><div class='progress-bar'></div></div>
								</div>
								<a class='browse-file btn'>Browse</a>
								<span class='file-input'><input class='upload-file' $multiple type='file' ></span>
								<div class='clear'></div>
							</div>";
					}
					break;	/*
				case 'file' :
					if ($data[$index_name]) {
						$html = "
							<div id='fm-file-row-$index' class='fm-file-row'>
								<input type='hidden' name='$name' class='fm-file' id='fm-file-$index' value='".$data[$index_name]."'>
								<div class='fm-img-preview'><div class='fm-img'><img src='" . $this->conf->path.$data[$index_name] . "' height='60' id='fm-img-$index'></div><div class='clear-img-btn'>".end(explode('/', $data[$index_name]))."<button type='button' class='fm-img-delete btn'>Remove</button></div></div>
								
								<input class='upload-file' type='file' >
								<iframe id='fm-iframe-$index' style='display: none;' frameborder='0' class='inline_upload' src='".$this->conf->filemanager."?fm_id=$index&fm_ext=$type&inline=1'></iframe>
							</div>
						";
					} else {
						$html = "
							<div id='fm-file-row-$index' class='fm-file-row'><input type='hidden' name='$name' class='fm-file' id='fm-file-$index'>
							<input class='upload-file' type='file' >
							<iframe id='fm-iframe-$index' frameborder='0' class='inline_upload' src='".$this->conf->filemanager."?fm_id=$index&fm_ext=$type&inline=1'>
							</iframe>
							</div>";
					}
					break;	
								*/		
				case 'upload' :
					if ($data[$index_name]) {
						$html = "
							<div id='fm-file-row-$index' class='fm-file-row'>
								<div class='fm-img-preview'><div class='fm-img'><img src='" . $this->conf->path.$data[$index_name] . "' height='60' id='fm-img-$index'></div><div class='clear-img-btn'>".end(explode('/', $data[$index_name]))."<button type='button' class='fm-img-delete btn'>Remove</button></div></div>
								<input type='hidden' name='$name' class='fm-file hide upload-file' id='fm-file-$index' value='".$data[$index_name]."'>
							</div>
						";
					} else {
						$html = "<div id='fm-file-row-$index' class='fm-file-row'><input class='upload-file' type='file' name='$name'></div>";
					}
					break;						
				case 'editor' :	
									
					$html = "<textarea name='$name' rows='8' cols='75' height='$option[height]' class='ckeditor $required'>".htmlspecialchars($data[$index_name], ENT_QUOTES)."</textarea>";
					break;
				case 'textarea' :
					if ($option['height']) {
						$height = "style='height: ".intval($option['height'])."px'";
					}
					
					$html = "<textarea $option_string name='$name' rows='8' cols='75' $height class='textarea $required'>".htmlspecialchars($data[$index_name], ENT_QUOTES)."</textarea>";
					break;
					
				case 'password' :
					$html = "
						<input type='password' style='display: inline; width: 0; height: 0; border: 0; padding: 0; margin: 0;' onfocus='$(this).next().focus()'/>
						<input autocomplete='off' $option_string type='password' name='$name' class='text password $required' />
						".(isset($help_text) ? "<span class='help'>$help_text</span>" : "<span class='help'>(Leave empty if no change)</span>")."
						<a class='link random-password'>Generate a Random Password</a>";
					break;
				case 'hidden' :
					$html = "<input $option_string type='hidden' name='$name' value='".htmlspecialchars($data[$index_name], ENT_QUOTES)."' />";
					break;
					
				case 'toggle' :
					$checked = $data[$index_name] ? "checked='checked'" : '';
					
					$html = "<input type='hidden' name='$name' value='0' /><input class='checkbox' type='checkbox' name='$name' value='1' $checked/>";
					break;
					
				case 'date' :
					if (!$data[$index_name] && $this->action == 'add') {
						$data[$index_name] = date('Y-m-d');	
					}
				
					$data[$index_name] = $this->func->date_to_normal($data[$index_name]);
					$html = "<input $option_string type='text' name='$name' value='".htmlspecialchars($data[$index_name], ENT_QUOTES)."' class='text datepicker $required' />";
					break;
						
			}
			
			if ($help_text) {
				$html .= "<div class='help'>$help_text</div>";	
			}
			
			return $html;
		}
		
		function get_next_weight($field = 'weight')
		{
			if ($this->parent_id ) {
				$pid = $this->parent_key_id ? $this->parent_key_id : $this->pid;
				
				if ($pid) {
					$where = "WHERE $this->parent_id = $pid";
				}
			}	
				
			$this->sql->query("SELECT MAX($field) FROM $this->tbl $where");
			return $this->sql->result(0) + 10;
		}
		
		function add_top_link($label,$icon,$url,$class,$title)
		{
			$this->top_link[$label]['icon'] = $icon;
			$this->top_link[$label]['url'] = $url;		
			$this->top_link[$label]['class'] = $class;
			$this->top_link[$label]['title'] = $title;
		}
		
		function get_top_link()
		{
			return $this->top_link;
		}
		
		function add_control($label,$url,$icon,$options='')
		{
			$this->control_link[$label]['label'] = $label;
			$this->control_link[$label]['icon'] = $icon;
			$this->control_link[$label]['url'] = $url;
			
			$this->control_link[$label]['options'] = $options;
		}
		
		function set_action($action)
		{
			$this->action = $this->sql->sanitize($action);
		}
			
		function set_action_id($id)
		{
			$this->id = (int) $id;
		}	
		
		function set_breadcrumb($parent,$name, $params = '')
		{			
			$label = $this->clean_admin_name($parent);		
			
			$this->breadcrumb[$parent] = "<a href='".$this->conf->admin."/$parent".($params ? "?$params" : '')."'>$label ".($name ? "($name)" : '')."</a>";
		}
		
		function set_description($text='')
		{
			$this->description_text = $text;
		}
		
		function load_tpl($tpl)
		{
			$this->tpl = $tpl;
		}
		
		function assign($name,$value)
		{
			$this->tpl->assign($name,$value);
		}
		
		function set_ordering($order)
		{
			if ($order) {
				$this->query_order = "ORDER BY $order";
			}
		}	
		
		function query_filter($filter)
		{
			$this->filter_where[] = $filter;
		}
		
		function set_data($data)
		{
			$this->view_data = $data;
		}
		
		function load_data()
		{
			if (isset($this->view_data) && is_array($this->view_data)) {
				$this->field_data = $this->view_data;
			} else {
				
				if ($this->parent_id && $this->parent_key_id) {
					$this->filter_where[] = "`$this->parent_id` = '$this->parent_key_id'";
				} else if ($this->parent_id && $this->pid) {
					$this->filter_where[] = "`$this->parent_id` = '$this->pid'";
				}	

				if ($this->search_filter) {
					$this->filter_where[] = "(".implode(' OR ', $this->search_filter).")";
				} 
				
				if ($this->filter_where) {
					$where = "WHERE ".implode(' AND ', $this->filter_where);
				}
				
				if ($this->num_per_page > 0) {
					$this->sql->query("SELECT COUNT(*) FROM $this->tbl $where $this->query_order");
					$num_rows = $this->sql->result(0);
					
					if ($num_rows) {
						$pg = $this->func->paginate($num_rows, $this->current_page, $this->num_per_page);
						if ($pg['limit_start'] || $pg['limit_length']) {
							$this->sql->query("SELECT * FROM $this->tbl $where $this->query_order LIMIT $pg[limit_start], $pg[limit_length] ");
							$this->field_data = $this->sql->fetch_all();
							
							if ($pg['pages']) {
								foreach ($pg['pages'] as $k=>$v)
								{
									$this->pages[$k] = $this->page(array('page'=>$v));
								}
							}
							
							$this->pagination_data = $pg;
							
							$this->page_prev_chapter = $this->page(array('page'=>$pg['prev_chapter']));
							$this->page_next_chapter = $this->page(array('page'=>$pg['next_chapter']));
							
							
						}
					}
				} else {
					$this->sql->query("SELECT * FROM $this->tbl $where $this->query_order");
					$this->field_data = $this->sql->fetch_all();
				}
			}
			
			if (function_exists('view_data')) {
				$this->field_data = call_user_func('view_data', $this->field_data);
			}
		}
		
		function set_parent($manager, $name, $tbl, $id, $key = '')
		{
			if (!$pid && $key && $this->get->$key) {
				$pid = (int) $this->get->$key;
				$this->parent_key = $key;
				$this->parent_key_id = $pid;
				$this->params[$this->parent_key] = $pid;
			} else if (!$pid && $this->pid) $pid = $this->pid;
			
			if ($pid) {
				$this->sql->query("SELECT $name FROM $tbl WHERE $id = $pid");
				$row = $this->sql->fetch();
				
				if ($this->parent_key && $this->pid) {
					$this->set_breadcrumb($manager, $row[$name], 'pid='.$this->pid); 
				} else {
					$this->set_breadcrumb($manager, $row[$name]); 
				}
				
				$this->parent_id = $id;

				$this->conf->set('admin_menu_parent', $manager);
				
				$this->tpl->assign('management_header', $this->keyword.' for '.$row[$name], true);
			} 
			
			if (!$row) {
				$this->func->log('Invalid '.ucwords($manager).' ID received','','notice');
				$this->func->redirect($this->conf->admin.'/'.$manager, 'Invalid '.ucwords($manager).' ID received');		
			}
		}
		
		function set_update_tpl($tpl, $id='admin')
		{
			$this->update_tpl = $tpl;
			$this->update_tpl_id = $id;
		}	
		
		function search_filter()
		{
			if ($this->action == 'filter' && $_POST['reset'] == 'reset') {
				unset($_SESSION['admin_filter'][$this->manager]);
			} else if ($this->action == 'filter' && $_POST['admin_search']) {
				$_SESSION['admin_filter'][$this->manager] = $this->sql->sanitize($_POST['admin_search']);
			} else if ($this->get->filter) {
				$temp = explode(':', $this->get->filter);
				if ($this->search[$temp[0]] && $temp[1]) {
					$_SESSION['admin_filter'][$this->manager][$temp[0]] = $this->sql->sanitize($temp[1]);	
				}
			}
		}

		function filter()
		{	
			
			if ($_SESSION['admin_filter'][$this->manager]) {
				foreach ((array) $_SESSION['admin_filter'][$this->manager] as $k=>$v)
				{
					if ($this->search[$k] && $v) {
						if (	$this->search[$k]['type'] == 'date') {
							$start = $v[0] ? "`$k` >= '".$this->func->date_to_sql($v[0])." 00:00:00'" : '';
							$end = $v[1] ? "`$k` <= '".$this->func->date_to_sql($v[1])." 23:59:59'" : '';
							
							if ($start || $end) {
								$this->filter_where[] = " $start ".($start && $end ? 'AND' : '')." $end ";
								$this->admin_filtered[$k] = $v;
							}
						} else if ($this->search[$k]['type'] == 'text' && trim($v)) {
							$this->filter_where[] = "`$k` LIKE '%".trim($v)."%'";
							$this->admin_filtered[$k] = $v;
						} else if ($this->search[$k]['type'] == 'select') {
							$this->filter_where[] = "`$k` = '$v'";
							$this->admin_filtered[$k] = $v;
						}
					}
				}
			}
		}
		
		function paginate($num_per_page = 100)
		{
			$this->num_per_page = $num_per_page;
		}
		
		function set_param()
		{
			$get = $this->get;
			$this->action = $get->action;
			$this->id = (int) $get->id;
			$this->ids = $get->ids;
			$this->pid = (int) $get->pid;
			$this->toggle = $get->toggle ? $this->sql->sanitize($get->toggle) : '';
			
			$this->current_page = (int) $get->page;
			
			if (!$this->action && $_POST['action']) {
				$this->action = $this->sql->sanitize($_POST['action']);
			}
			
			if (!$this->id && $_POST['action_id']) {
				$this->id = (int) $_POST['action_id'];
			}
			
			if ($this->current_page) {
				$this->params['page'] = $this->current_page;
			}

			if ($this->pid) {
				$this->params['pid'] = $this->pid;
			}
			
			if ($get->destination) {
				$this->params['destination'] = $get->destination;
			}
			
			if ($get->ajax) {
				$this->params['ajax'] = $get->ajax;
			}
		}
		
		function default_control()
		{
			if ($this->allow_delete) {
				$this->add_control('delete this item', $this->page(array('ajax'=>1,'action'=>'delete','ids'=>'')), 'icon-remove3', 'confirm type-delete ajax'); 
			}			
		}
		
		function add_search($field, $label, $type, $list = array())
		{
			$this->search[$field]['label'] = $label;
			$this->search[$field]['type'] = $type;
			$this->search[$field]['list'] = $list;
		}
		
		function search_html()
		{
			if ($this->search) {
				$search_html  = '<tr>';
				$count = 0;
				
				foreach ((array) $this->search as $field=>$row)
				{
					$search_html .= "<td><label>$row[label]</label>";
					
					if ($row['type'] == 'date') {
						$search_html .= "<input type='text' name='admin_search[$field][]' placeholder='Start Date' class='text date datepicker' value='".htmlspecialchars($this->admin_filtered[$field][0])."'> <input value='".htmlspecialchars($this->admin_filtered[$field][1])."' class='text date datepicker' type='text' name='admin_search[$field][]' placeholder='End Date'>"; 
					} else if ($row['type'] == 'text') {
						$search_html .= "<input type='text' name='admin_search[$field]' class='text string' value='".htmlspecialchars($this->admin_filtered[$field])."'>"; 
					} else if ($row['type'] == 'select') {
						$search_html .= "<select name='admin_search[$field]'><option value=''> -- None --</option>";
								
						foreach ((array)$row['list'] as $k=>$v)
						{
							$sel = $this->admin_filtered[$field] == $k ? "selected='selected'" : '';
							$search_html .= "<option value='$k' $sel>$v</option>";	
						}
						
						$search_html .= "</select>"; 
					}
					
					$search_html .= "</td>";
					
					if ($count % 6 == 5) {
						$search_html .= "</tr><tr>";	
					}
					
					$count++;
				}
				
				$search_html .= "</tr>";
				
				return $search_html;
			}
		}
				
		function page($param = '', $url = '')
		{
			$page = $url ? $url : $this->page;
			
			if ($this->params) {
				foreach ($this->params as $k=>$v)
				{
					if (is_array($param) && isset($param[$k])) {
						$p .= $k.'='.$param[$k].'&';
						unset($param[$k]);
					} else {
						$p .= $k.'='.$v.'&';
					}
				}
			}
						
			if ($param && is_array($param)) {
				foreach ($param as $k=>$v)
				{
					$p .= $k.'='.$v.'&';
				}
			}
			
			$p = !is_array($param) ? rtrim($p, '&').'&'.$param : rtrim($p, '&');
			
			return $this->func->href($page, $p);
		}
				
		function alias($string, $page_id, $additional='', $alias_field = 'alias')
		{
			$num = 1;
			$title = $string;
			
			if ($page_id) {
				$where = "AND $this->pk != ".intval($page_id);
			}
			
			if ($additional) {
				$where .= ' '.$additional;	
			}
			
			do {
				$title = $this->func->clean_url($string).$append;
				
				$this->sql->query("SELECT COUNT(*) FROM $this->tbl WHERE `$alias_field` = '$title' $where");
				$exist = $this->sql->result(0);
				
				if ($exist) {
					$append = '-'.($num++);
				}
			} while ($exist);
			
			return $title;
		}
		
		function full_alias($alias, $pid)
		{
			
			do {
				if ($row['parent_id']) {
					$parent_id = $row['parent_id'];
				} else {
					$title = $alias;
					$parent_id = (int) $pid;
				} 
				
				$this->sql->query("SELECT alias,parent_id FROM $this->tbl WHERE page_id = '$parent_id'");
				$row = $this->sql->fetch();
				
				if ($row['alias']) { 
					$title = rtrim($row['alias'],'/').'/'.ltrim($title,'/');
				}
				
			} while ($row['parent_id']);
			
			return trim($title, '/');		
		}
		
		function post_process($msg, $data='', $warning='')
		{	
			if (function_exists('post_process')) {
				$resp = call_user_func('post_process', $data, $msg, $warning);	
				
				if (is_array($resp) && count($resp)) {
					$msg = $resp[0]; 
					$warning = $resp[1];
				} else {
					$msg = $resp;	
				}
			}
			
			if ($msg) {
				$this->func->log($msg, $data, $warning);
			}
			
			if ($this->get->destination && !$this->get->ajax) {
				$this->func->redirect($this->func->base64_decode($this->get->destination), $msg);
			} else if ($this->get->ajax) {
				if ($warning) {
					echo json_encode(array('status'=>false,'message'=>$msg));
				} else {
					echo json_encode(array('status'=>true,'message'=>$msg));
				}
				
				exit;
			} else {
				$this->func->redirect($this->page(), $msg);	
			}
		}
		
		function process($post)
		{
			$data = array();

			if ($this->form && $post) {
				foreach ($post as $id=>$p)
				{	
					$field  = array();
					$repeat = false;
					foreach ($this->form as $group=>$rows)
					{
						if (isset($rows[$id]) && is_array($rows[$id])) {
							foreach ($rows as $row)
							{
								if ($row['option']['repeat']) {
									$repeat = true;	
								}
							}
							
							$field = $rows[$id];
							break;
						}
					}
				
					if ($field['option']['callback'] && function_exists($field['option']['callback'])) {
						$ret = call_user_func($field['option']['callback'], $post);
						
						if ($ret !== false) {
							$data[$id] = $ret;	
						}
					} else {	
						if ($field['type'] == 'date' && $p) {
							$is_array = false;
							
							if (is_array($p)) {
								$dates = $p;
								$is_array = true;
							} else {
								$dates[0] = $p;
							}
							
							foreach ($dates as $k=>$d)
							{
								$d = $this->func->date_to_sql($d);
								
								$test = explode('-', $d);
								
								if (count($test) != 3 || !mktime(0, 0, 0, $test[1], $test[2], $test[0])) {
									$this->form_return($field['label'].' '.$d.' is an invalid date format, please use MM/DD/YYYY');
									break;
								} else {
									$dates[$k] = $d;
								}
							}
							
							$p = $is_array ? $dates : $dates[0];
						} else if ($field['type'] == 'money' && $p) {
							$data[$id] = preg_replace('~[0-9.]~', '', $p);
						}  else if ($field['type'] == 'email' && $p) {
							if (!filter_var($p, FILTER_VALIDATE_EMAIL)) {
								$this->form_return('The '.$field['label'].' requires a valid email address');
							}
						} else if (isset($field['option']['required']) && $field['option']['required']) {
							if (is_array($p)) {
								$missing = true;
								
								foreach ($p as $k=>$v)
								{
									if (is_array($v)) {
										foreach ($v as $key=>$val)
										{
											if ($val) {
												$missing = false;
												break 2;	
											}
										}
									} else if ($v) {
										$missing = false;
										break;	
									}
								}
								
								if ($missing) {
									$this->form_return($field['label'].' is a required field');
								}
							} else if (!$p) {
								$this->form_return($field['label'].' is a required field');
							} 
						}
						
						
						if (isset($field['option']['unique']) && $field['option']['unique'] && $this->action == 'submit_add') {
							$this->sql->query("SELECT COUNT(*) FROM $this->tbl WHERE `".$this->sql->sanitize($id)."` = '".$this->sql->sanitize($p)."'");
							if ($this->sql->result()) {
								$this->form_return('The '.$field['label'].' '.$p.' is already taken, choose another value');
							}
						}
						
						if ($field['option']['maxlen'] && $field['option']['maxlen'] < strlen(trim($p))) {
							$this->form_return('The '.$field['label'].' is too long. Current length ('.strlen(trim($p)).'). Maximum length allowed ('.$field['option']['maxlen'].')');
						}
						
						if ($field['option']['int'] && $field['option']['int'] != intval($field['option']['int'])) {
							$this->form_return('The '.$field['label'].' is not a valid integer value');
						}
						
						
							
						if ((isset($field['option']['multiple']) && $field['option']['multiple']) || $repeat) {
							$row = array();

							if (is_array($p)) {
								foreach ($p as $k=>$v)
								{
									if ($field['option']['group']) {
										$group_fields[$field['option']['group']][$k][$id] = $v;
									} else {
										$group_fields[$id][$k][$id] = $v;
									}
								}
							}
							
						} else if ($field['type'] == 'time') {
							$p = $this->func->time($p['time'].' '.$p['meridiem']);
						} else if ($field['type'] == 'password') {
							if (!empty($p)) {
								$data[$id] = User::generate_password($p);
							}
						} else if ($field['option']['alias']) {
							$source = $data[$field['option']['alias']] ? $data[$field['option']['alias']] : $post[$field['option']['alias']];

							if ($source && ($field['option']['refresh'] || !$p)) {
								if (intval($this->pid) && $this->parent_id) {
									$and = " AND `".$this->sql->sanitize($this->parent_id)."` = '".intval($this->pid)."'"; 
								}
								
								$data[$id] = $this->alias($source, $this->id, $and, $id); 	
							} else {
								$data[$id] = $p;
							}
						} else if ($field['option']['clone']) {
							if (!$p) {
								$data[$id] = $post[$field['option']['clone']];
							}
						} else if ($field['type'] != 'html') {
							$data[$id] = $p;	
						}
					}
				}
				if ($group_fields) {
					foreach ($group_fields as $group=>$rows)
					{
						$first = reset($this->form[$group]);
						
						if ($first['option']['table']) {
							$data['group_fields'][$group]['tbl'] = $first['option']['table'];
		
							if ($rows) { 
								foreach ($rows as $k=>$v)
								{
									$empty = true;
									
									foreach ($v as $key=>$val)
									{
										if (is_array($val)) {
											foreach ($val as $key2=>$val2)
											{
												if ($val2) {
													$empty = false;	
													
												} else {
													unset($rows[$k][$key][$key2]);	
												}
											}
											
											$rows[$k][$key] = array_values($rows[$k][$key]);
											
										} else if ($val) {
											$empty = false;
										}
									}
									
									if ($empty) {
										unset($rows[$k]);	
									}
								}
							}
							
							$rows = array_values($rows);

							$data['group_fields'][$group]['data'] = $rows;
						} else {
							if ($this->display_admin_error !== false) {
								die('No table defined for group '.$group);	
							}
						}
					}
				}
			}

			return $data;
		}
		
		function form_return($msg)
		{
			$this->func->set_msg($msg);
			
			if (!$this->old_action) {
				$this->old_action = $this->action;
			
				$this->set_action('return'); # = 'return';
			}
		}
		
		function action()
		{
			if (function_exists('admin_action')) {
				call_user_func('admin_action');	
			} else {
				if ($_POST['form_submitted']) {
					foreach ($this->form as $group=>$row)
					{
						foreach ($row as $k=>$v)
						{
							$post[$k] = $_POST[$k];
						}	
					}
					
					if ($this->parent_id) {
						if ($_POST[$this->parent_id]) {
							$post[$this->parent_id] = $_POST[$this->parent_id];
						} else {
							$this->form_return("No Parent ID received");	
						}
					} 
					
					if (function_exists('pre_process')) {
						$post = call_user_func('pre_process', $post);	
					}
	
					$processed = $this->process($post);
				} else {
					if ($_SESSION['admin_form_return_data']) {
						$processed = $_SESSION['admin_form_return_data'];
						unset($_SESSION['admin_form_return_data']);	
					}
					
					if (!$processed && $this->action == 'edit') {
						$this->sql->query("SELECT * FROM $this->tbl WHERE $this->pk = $this->id");
						$processed = get_magic_quotes_gpc() ? $this->sql->sanitize($this->sql->fetch(), STRIP) : $this->sql->fetch();
						
						if ($this->form_groups) {
							foreach ($this->form_groups as $k=>$v)
							{
								if ($v['table']) {
									$this->sql->query("SELECT * FROM $v[table] WHERE $this->pk = $this->id ORDER BY weight");
									$processed['children_data'][$k] = $this->sql->fetch_all();	
								}
							}
						}
					}
					
					if ($processed['group_fields']) {
						foreach ($processed['group_fields'] as $gfk=>$gf)
						{
							$processed['children_data'][$gfk] = $gf['data'];	
						}
					}
					
					if (function_exists('pre_process')) {
						$processed = call_user_func('pre_process', $processed);	
					}
				}
				
				
				$this->filter();
	
				if ($this->action == 'return') {
					$_SESSION['admin_form_return_data'] = $processed;
	
					if ($this->old_action == 'submit_add') {
						$this->func->redirect($this->page("action=add"));
					} else if ($this->old_action == 'submit_edit') {
						$this->func->redirect($this->page("action=edit&id=".$this->id));
					}
				} 
				
				if ($this->action == 'edit' || $this->action == 'add') {	
					$this->form($processed);
				} else if ($this->action == 'toggle') {
					$this->toggle();
				} else if ($this->action == 'submit_add') {
					$this->insert($processed);
				} else if ($this->action == 'submit_edit') {
					$this->update($processed);
				} else if ($this->action == 'delete') {
					$this->delete();
				} else {
					$this->load_data();		
				}	
			}
		}
		
		function run()
		{
			if (strpos($this->manager, ':')) {
				$parent = reset(explode(':', $this->manager));	
				array_unshift($this->breadcrumb, "<a>$parent</a>");
			}
			
			array_unshift($this->breadcrumb, "<a href='".$this->conf->admin."'>Home</a>");
			
			if ($this->keyword) {
				array_push($this->breadcrumb, "<a>$this->keyword</a>");
			}
			
			$this->tpl->assign('fields', $this->get_fields($this->field_data));
			
			$this->tpl->assign('manager_url', $this->page());
			
			$this->tpl->assign('add_url', $this->page(array('action'=>'add')));
			$this->tpl->assign('delete_url', $this->page(array('action'=>'delete','ids'=>'')));
			$this->tpl->assign('edit_url', $this->page(array('action'=>'edit','id'=>'')));
			
			$this->tpl->assign('current_page', $this->current_page ? $this->current_page : 1);
			
			$this->tpl->assign('pages', $this->pages);
			$this->tpl->assign('pagination', $this->pagination_data);
			$this->tpl->assign('prev_chapter', $this->page_prev_chapter);
			$this->tpl->assign('next_chapter', $this->page_next_chapter);
			
			$this->tpl->assign('search_html', $this->search_html());
			
			$this->tpl->assign('description_text', $this->description_text);
			$this->tpl->assign('control_links', $this->control_link);
			$this->tpl->assign('top_links', $this->get_top_link());
			$this->tpl->assign('admin_breadcrumb', implode(" <span class='icon icon-arrow-right3'></span> ", $this->breadcrumb));
		}
	}
?>