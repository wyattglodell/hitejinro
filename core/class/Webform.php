<?php
	class Webform extends Base
	{
		protected $post = array();
		
		function __construct($id)
		{
			parent::__construct();
			
			$this->func = Registry::get('func');
			
			$this->id = $this->sql->sanitize($id);
			
			$this->sql->query("SELECT * FROM ".$this->conf->WEBFORM." WHERE slug = '$this->id'");
			$this->webform = $this->sql->fetch();		
		}
		
		function load()
		{						
			$this->sql->query("SELECT * FROM ".$this->conf->WEBFORM_FIELD." WHERE webform_id = '".$this->webform['webform_id']."'");
			while ($row = $this->sql->fetch())
			{
				$this->field[$row['name']] = $row;
				
				if ($row['subtype'] == 'states') {
					$this->list['list_'.$row['name']] = $this->func->get_list('states');
				} else if ($field['subtype'] == 'months') {
					$this->list['list_'.$row['name']] = $this->func->get_list('months');
				} else if ($field['subtype'] == 'custom' && $row['options']) {
					$list = array();
					$temp = explode("\n", trim($row['options']));
					foreach ($temp as $v)
					{
						if ($v) {
							$r = explode('|',$v);
							$list[$r[0]] = $r[1];
						}
					}
					$this->list['list_'.$row['name']] = $list;
				} 			
			}
		}	
				
		function get_post()
		{
			
			return array_merge((array)$this->post,(array)$this->list);
		}
		
		function submit(&$post)
		{			
			if ($this->webform['captcha']) {
				if (!$_SESSION['securimage_code_value']) {
					$msg = 'Your CAPTCHA has expired or you need to enable session cookies on your browser';
				} else {
				  	include($this->conf->captcha_file."/securimage.php");
  					$img = new Securimage();
  					$valid = $img->check($post['captcha']);
  					if(!$valid) {
						$msg = 'Invalid CAPTCHA code entered';
					} 
				}
			} 
		
			if ($this->field) {
				foreach ($this->field as $name=>$row)
				{
					$this->post[$name] = htmlspecialchars($post[$name], ENT_QUOTES);
					
					foreach ($row as $k=>$v)
					{
						if ($k == 'unique' && $v) {
							$temp = $this->sql->sanitize($post[$name]);
							$name = $this->sql->sanitize($name);
							
							$this->sql->query("
								SELECT COUNT(*) FROM ".$this->conf->WEBFORM_SUBMISSION." ws 
								LEFT JOIN ".$this->conf->WEBFORM_SUBMISSION_FIELD." wsf ON ws.submission_id = wsf.submission_id
								WHERE ws.webform_id = {$this->webform['webform_id']} AND wsf.name = '$name' AND wsf.value = '$temp'");
								
							if ($this->sql->result()) {
								$msg = 'This submission has already been recieved';
							}
						} else if ($k == 'required' && $v && !trim($post[$name])) {
							$msg = 'Please fill in all the required fields';
							$missing[] = $name;
						} else if ($k == 'subtype' && $v == 'email') {
							if (function_exists('filter_var')) {
								if (!filter_var($post[$name], FILTER_VALIDATE_EMAIL)) {
									$msg = 'Invalid email address entered';
								}
							}
						} 
					} 
				}
			}

			if ($msg) {
				$r['msg'] = $msg;
				$r['webform_js'] = $this->get_missing_js($missing);
			} else {
				$this->sql->query("INSERT INTO ".$this->conf->WEBFORM_SUBMISSION." (webform_id,submission_date,ip) VALUES ('".$this->webform['webform_id']."',NOW(),'".$_SERVER['REMOTE_ADDR']."')");
				$insert_id = $this->sql->insert_id();
				
				if ($this->post) {
					foreach ($this->post as $k=>$v)
					{
						$string .= ucwords(str_replace('_',' ', $k)).': '.$v."\n";
						
						$query[] = "('$insert_id','".$this->sql->sanitize($k)."','".$this->sql->sanitize($v)."')";
					}
					
					if ($query) {
						
						
						$this->sql->query("INSERT INTO ".$this->conf->WEBFORM_SUBMISSION_FIELD." (submission_id,name,value) VALUES ".implode(',',$query));
					
						if ($this->webform['email']) {
							$to = $this->webform['email'] == 'default' ? $this->setting->contact_email : $this->webform['email'];
							$from_name = $this->webform['from_name'] == 'default' ?  $this->setting->contact_name : $this->webform['from_name'];
							$from_email = $this->webform['from_email'] == 'default' ?  $this->setting->from_email : $this->webform['from_email'];
							$subject = $this->webform['subject'] ? $this->webform['subject'] : 'Webform Submission from '. $this->setting->site_name;
							
							$header = "FROM: $from_name <$from_email>";
							$this->func->email($to, $subject, $string, $header);						
						}
						
						$this->func->log('Webform ['.$this->webform['name'].'] submission ('.$insert_id.')', $this->post);

						$this->func->reload('Your form submission has been received successfully');
					}
				}
			}
			
			return $r;
		}
		
		function get_missing_js($missing)
		{
			if ($missing) {
				$string .= "<script type='text/javascript'>";
				$string .= "$(function() {";
				foreach ($missing as $v)
				{
					$string .= "$('#webform-".$this->id."-$v label').addClass('missing');\n";
				}	
				$string .= "});";
				$string .= '</script>';
			}
			
			return $string;
		}
		
		
		function get_field(&$field)
		{
			if ($field['type']) {
				$html = $this->{"field_".$field['type']}($field);
			}
			
			return $html;
		}

		function get_submit()
		{
			$str .= "\n\t\t<li class='field-submit'>\n\t\t\t<label>&nbsp;</label>\n\t\t\t<input type='submit' name='submit' value='Submit' />\n\t\t\t<input type='reset' value='Clear' />\n\t\t\t<span class='req'>*</span>Required Fields";
			$str .= "\n\t\t</li>";
					
			return $str;
		}

		
		function get_captcha()
		{
			if ($this->webform['captcha']) {
$str = "
		<li><label>Captcha</label>
			<div id='captcha_box'>
				<img id='siimage' src='<?=\$captcha?>/securimage_show.php?sid=<?php echo md5(time()) ?>' />
				
				<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0' width='19' height='19' id='SecurImage_as3'>
					<param name='allowScriptAccess' value='sameDomain' />
					<param name='allowFullScreen' value='false' />
					<param name='movie' value='<?=\$captcha?>/securimage_play.swf?audio=<?=\$captcha?>/securimage_play.php&bgColor1=#777&bgColor2=#fff&iconColor=#000&roundedCorner=5' />
					<param name='quality' value='high' />
					<param name='bgcolor' value='#ffffff' />
					<embed src='<?=\$captcha?>/securimage_play.swf?audio=<?=\$captcha?>/securimage_play.php&bgColor1=#777&bgColor2=#fff&iconColor=#000&roundedCorner=5' quality='high' bgcolor='#ffffff' width='19' height='19' name='SecurImage_as3' align='middle' allowScriptAccess='sameDomain' allowFullScreen='false' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' />
				</object>
				<a tabindex='-1' href='#' title='Refresh Image' onclick=\"document.getElementById('siimage').src = '<?=\$captcha?>/securimage_show.php?sid=' + Math.random(); return false\"><img src='<?=\$captcha?>/images/refresh.gif' alt='Reload Image' onclick='this.blur()'  /></a>
				<div class='br clear'></div>
				<input type='text' name='captcha' value='' /> Enter Code
			</div>		
		</li>
";
				return $str;										
			}
		}
		
		
		function field_text(&$field)
		{
			$req = $field['required'] ? "<span class='req'>*</span>" : '';
			return "<li id='webform-$this->id-$field[name]'>\n\t\t\t<label for='webform-$field[name]'>$req$field[label]</label>\n\t\t\t<input id='webform-$field[name]' type='text' name='$field[name]' value='<?=\$form['{$field['name']}']?>'/>\n\t\t</li>";
		}
		
		function field_select(&$field)
		{
			$req = $field['required'] ? "<span class='req'>*</span>" : '';
			$str = "<li id='webform-$this->id-$field[name]'>\n\t\t\t<label for='$field[name]'>$req$field[label]</label>
			<select name='$field[name]'>
				<option value=''> -- Select -- </option>
				<?php
					foreach(\$form['list_{$field['name']}'] as \$k=>\$v)
					{
						\$sel = \$form['{$field['name']}'] == \$k ? \"selected='selected'\" : '';
						echo \"<option value='\$k' \$sel>\$v</option>\";
					}
				
				?>
			</select>
		</li>";
						
			return $str;
		
		}
		
		function field_textarea(&$field)
		{
			$req = $field['required'] ? "<span class='req'>*</span>" : '';
			return "<li id='webform-$this->id-$field[name]'>\n\t\t\t<label for='$field[name]'>$req$field[label]</label>\n\t\t\t<textarea name='$field[name]' rows='5' cols='30'><?=\$form['{$field['name']}']?></textarea>\n\t\t</li>";
		}
		
		function get_tpl()
		{
			$html = '';
			
			$html .= "<?=\$webform_js?>\n\n";
			
			$html .= '<form method="post" action="<?=$url?>" id="webform-'.$this->webform['slug'].'">';
			$html .= "\n\t<input type='hidden' name='action' value='submit-{$this->webform['slug']}' />";
			$html .= "\n\t<ul class='form-ul'>";
			
			$this->sql->query("SELECT * FROM ".$this->conf->WEBFORM_FIELD." WHERE webform_id = {$this->webform['webform_id']} ORDER By weight");
			while ($row = $this->sql->fetch())
			{
				$html  .= "\n\t\t".$this->get_field($row);
			}
			$html  .= $this->get_captcha(); 
			$html  .= $this->get_submit(); 
			
				
			$html .= "\n\t</ul>\n</form>";
			return $html;
		
		}	
		
		function get_data()
		{
			return "<?php
	\$webform = new Webform('".$this->webform['slug']."');
	\$webform->load();

	if (\$_POST['action'] == 'submit-".$this->webform['slug']."') {
		\$result = \$webform->submit(\$_POST);
		\$tpl->assign('webform_js', \$result['webform_js']);
		\$func->set_msg(\$result['msg']);
	}

	\$tpl->assign('form', \$webform->get_post());			
	\$tpl->set_template('content','webform-".$this->webform['slug'].".tpl.php');
?>";
		}	
	}
?>