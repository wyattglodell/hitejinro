<?php /*
	#==============================================================================#	
	# fields used for displaying data
	#==============================================================================#	
	
	$admin->add_field(name, label, option|option2, map);
	
	Options:
		description: parameter
		width: 100
		sortable: sort | nosort
		align: left | right | center
		substr: cut100
		strip html: strip
		image: img=<height in px> (ex: img=50)
		HTML: html
		callback: callback=custom_display
		date: Jan 01, 2011
		datetime: Jan 01, 2011 12:00 PM
		time: 12:00 PM
		money: $100.00
		edit: Editable Link
		function custom_display(&$data) # returning false will remove the entire row from display
		{
			$conf =  Registry::get('conf');
			$un = $data['required'] == 1 ? '' : 'un';
			return "<img src='".$conf->icon."/{$un}checked.gif' />";
		}
	
	Map:
		remap database value to array value For example if databasevalue is 1 and array passed has 1=>Foo, Foo would display instead of 1 
	
	
	
	
	#==============================================================================#	
	# fields used when editing or adding a new item
	#==============================================================================#	
	
	$admin->form_field(name, label, field_type, array(option=>value));
	
	 Field Types: 
		 	group (used to just create a legend)
			text | password | email
			select
			file | image | document 
			textarea | editor 
			hidden 
			toggle 
			weight 
			date | time (stored as military time INT)
			html
	 Example Options 
			default_value => CA - Set a default value
			height => 100 - For editor height
			list => array(key=>value,key=>value) - Pass in the options for select in key/value pairs, can also pass parent =>parent_id and pass in a multidimensional array
			for the child as [parent_id][child_id] to do conditional selects
		
			conditions => array (value=>'fields|to_show') - if value is selected, fields will be displayed, other fields in other values will be hidden
			alias => 'source_name' - if this value is empty, generate an alias of source_name
			refresh => for alias, if set and the source is updated, alias will also refresh
			clone => 'source_name' - if this value is empty, clone the value of source_name
			final => true - Once a value is set, it cannot be changed
			required => false - Turn something required off
			maxlen => 15 - Max length of input
			ignore_self => true - Don't list self in a drop down
			copy => source_id - duplicates another field's value
			callback => validation_function - ran instead of Admin::process() for this field
			multiple => true - Allows a select to be a multiselect field
			repeat => true - Allows a row to be repeated (also requires 'group' => 'Group Name' AND 'table' => 'Child Table Name')
			field_repeat => true - Allows a field to be repeated, can be used in conjunction with repeat and group
			group => 'Group Name', can be used to have multiple fields in a single row
		 	tab => 'Tab Name', Used to put different fields in different tabs, undefined tabs fall under General
			help => string - Put help string below the field
			html => <html> - will replace entire form field with <html>
			function custom_field($type, $name, $label, $data, $option)
			{
				$get = Registry::get('get');
				$admin = Registry::get('admin');
				
				if ($admin->action == 'add' || !$data['menu'] && $data['parent_id']) {
					$value = $data[$name] ? $data[$name] : $get->parent_id;
					
					$html = "<select name='$name' class='select  $required'>";
					$html .= "<option value='' class='option'> -- Please Select -- </option>";
					if ($option['list']) {
						foreach ($option['list'] as $k=>$v)
						{
							$sel = $value == $k ? "selected='selected'" : '';
							$html .= "<option value='$k' $sel>$v</option>";
						}
					}
					$html .= "</select>";	
				}
			
				return $html;	
			}
*/ ?>