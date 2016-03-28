	function insert_context(str)
	{
		document.getElementById('context-box').innerHTML = str;
	}
	
	function check_all_files(obj)
	{
		var i = 0;
		while (document.getElementById('file-'+i+'-checkbox')) {
			document.getElementById('file-'+i+'-checkbox').checked = obj.checked;
			i++;
		}
		
		fm.update_current_file();
	}
	
	function reset_file_input_box()
	{
		document.getElementById('file-upload-box').innerHTML = document.getElementById('file-upload-box').innerHTML;
	}
	
	function reset_context_box()
	{
		insert_context('');
		insert_msg('');
	}
	
	function reset_resize_option()
	{
		if (document.getElementById('image_resize')) document.getElementById('image_resize').checked = false;
		if (document.getElementById('resize-option')) document.getElementById('resize-option').selectedIndex = 0;
		if (document.getElementById('resize-option-box')) document.getElementById('resize-option-box').style.visibility = 'hidden';
		
		reset_context_box();
	}
	
	function image_resize(obj)
	{		
		var vis = obj.checked ? 'visible' : 'hidden'
		
		document.getElementById('resize-option-box').style.visibility = vis;
		
		resize_option(document.getElementById('resize-option'));
		
		if (!obj.checked) {
			reset_resize_option();
		}
	}
	
	function resize_option(obj)
	{
		var sel = obj.options[obj.selectedIndex].value;
		
		if (sel) {
			if (sel == 'fixed') {
				insert_msg('Select the fixed width and height for the image resize');
			} else if (sel == 'aspect') {
				insert_msg('Select the width OR height to use as basis for image reduction');
			}
			
			var str = "Width <input type='text' id='input-width' size='2' /> Height <input type='text' id='input-height' size='2' />";
			insert_context(str);
		} else {
			insert_context('');	
		}
	}
	
	function insert_msg(msg, error)
	{
		var box = document.getElementById('msg-box');
		
		error = error == true ? true : false;
		
		if (error) {
			box.className += ' error';
		} else {
			box.className = box.className.replace(' error', '');
			box.className = box.className.replace('error', '');
		}
		
		box.innerHTML = msg;	
	}
	
	function upload_files()
	{
		var msg = '';
		
		if (document.getElementById('image_resize').checked) {
			var sel = document.getElementById('resize-option').options[document.getElementById('resize-option').selectedIndex].value;
			
			if (sel == 'fixed') {
				var w = parseInt(document.getElementById('input-width').value);
				var h = parseInt(document.getElementById('input-height').value);
				if (!isNaN(h)) {
					document.getElementById('resize_height_data').value = h;	
				} else {
					msg = 'Enter a valid height for fixed sized image resize';
				}
				
				if (!isNaN(w)) {
					document.getElementById('resize_width_data').value = w;	
				} else {
					msg = 'Please enter a valid width for fixed sized image resize';
				}
			} else if (sel == 'aspect') {
				var w = parseInt(document.getElementById('input-width').value);
				var h = parseInt(document.getElementById('input-height').value);
				if (!isNaN(w) && !isNaN(h)) {
					msg = 'Enter only one side to use as basis for maintaining aspect ratio';	
				} else {
					document.getElementById('resize_height_data').value = h;	
					document.getElementById('resize_width_data').value = w;	
				}
			} else {
				msg = 'Select an option for image resizing';	
			}
		}
		
		document.getElementById('current_folder').value = fm.current_folder;
		
		if (msg) {
			insert_msg(msg, true); return false;	
		} else {
			reset_context_box()
			loading();
			return true;	
		}
	}
	
	function file_upload_status(msg, uploaded)
	{
		reset_context_box();
		reset_file_input_box();
		reset_resize_option();
		fm.update_file_list();

		
		var s = uploaded ? false : true;
		insert_msg(msg, s);
	}
	
	function inline_file_upload_status(msg, uploaded)
	{
		if (uploaded) {
			parent.fm_id = fm.return_id;
			parent.insertURL(uploaded_file_name);
		}
		
		if (msg) {
			parent.set_msg(msg);
		}
	}
	
	function show_preview(num)
	{
		var src = document.getElementById('file-'+num+'-thumb').innerHTML;
		loading();
		
		var thumb = new Image();
		thumb.onload = function()
		{
			display_preview(this);
		}
		
		thumb.src = src;
	}
	
	function reset_all()
	{
		reset_resize_option();
		reset_context_box();
		reset_file_input_box();		
	}
	
	function loading()
	{		
		insert_context("<p class='center'><img src='"+fm.filemanager_src+"/img/loading.gif' alt='Loading...' /></p>");	
	}
	
	function display_preview(obj)
	{
		reset_all();
		
		var w = obj.width;
		var h = obj.height;
	
		var ratio = (w > h) ? fm.preview_width / w : fm.preview_height / h;
		
		obj.width = (ratio * w);
		obj.height = (ratio * h);

		var box = document.getElementById('context-box');

		box.appendChild(obj);
	}
	
	function create_folder_context()
	{
		reset_context_box();
		insert_msg('Enter name of the new folder');
		var str = "<input type='text' id='folder-name' /> <input type='button' value='Create' onclick='submit_create_folder()'/>";
		insert_context(str);
	}
	
	function submit_create_folder()
	{
		var name = document.getElementById('folder-name').value;

		if (!name) {
			insert_msg('No folder name entered', true);	
		} else {
			fm.submit_create_folder(name);	
		}
	}
	
	function folder_delete_context()
	{
		reset_context_box();
		
		var f = fm.current_folder.split('/');
		var cur = f[ f.length-1];
		
		insert_msg('Are you sure you want to delete folder: '+ cur + '?');
		var str = "<input type='button' value='Delete' onclick='submit_delete_folder()' /> <input type='button' value='Cancel' onclick='reset_context_box()' />";
		insert_context(str);
	}
	
	function submit_delete_folder()
	{
		fm.submit_delete_folder();	
	}
	
	function folder_rename_context()
	{
		reset_context_box();
		
		var f = fm.current_folder.split('/');
		var cur = f[ f.length-1];
		
		insert_msg('What would you like '+ cur + ' renamed to?');
		var str = "<input type='text' id='rename-folder-input' value='"+cur+"'/> <input type='button' value='Rename' onclick='submit_rename_folder()' /> <input type='button' value='Cancel' onclick='reset_context_box()' />";
		insert_context(str);
	}
	
	function submit_rename_folder()
	{
		var f = fm.current_folder.split('/');
		var cur = f[ f.length-1];
		
		if (cur == document.getElementById('rename-folder-input').value) {
			insert_msg('What would you like '+ cur + ' renamed to?');
		} else {
			fm.submit_rename_folder();	
		}
	}
	
	function file_delete_context()
	{
		reset_context_box();
		
		if (fm.multiple_file_selected) {
			insert_msg('Are you sure you want to delete the selected files?');
			var btn = true;
		} else {
			var i = fm.single_file();
			if (i || i == 0) {
				var name = document.getElementById('file-'+i+'-name').innerHTML;
				insert_msg('Are you sure you want to delete: '+name);
				var btn = true;
			} else {
				var btn = false;
				insert_msg('No file selected');	
			}
		}
		if (btn) {
			var str = "<input type='button' value='Delete' onclick='submit_delete_file()' /> <input type='button' value='Cancel' onclick='reset_context_box()' />";
			insert_context(str);
		}
	}
	
	function submit_delete_file()
	{
		fm.submit_delete_file();	
	}
	
	function file_rename_context()
	{
		reset_context_box();
		
		if (fm.multiple_file_selected) {
			insert_msg('You must select a single file to rename it', true);
		} else {
			var i = fm.single_file();
			
			if (i || i == 0) {
				var name = document.getElementById('file-'+i+'-name').innerHTML;
				insert_msg('What would you like '+ name + ' renamed to?');
				var str = "<input type='text' id='rename-file-input' value='"+name+"'/> <input type='button' value='Rename' onclick='submit_rename_file()' /> <input type='button' value='Cancel' onclick='reset_context_box()' />";
				insert_context(str);
			} else {
				insert_msg('No file selected');	
			}			
		}
	}
	
	function file_move_context()
	{
		reset_context_box();
		
		var i = fm.single_file();
		
		if (fm.multiple_file_selected || i || i == 0) {
			var string = 'fm_request=folder_list';
			var resp = fm.ajax_request(string);
			if (resp.substr(0, 5) == 'ERROR') {
				var chunk = resp.split(resp);
				
				insert_msg(chunk[1], true);
			} else {
				//alert(resp);
				insert_msg('Please select the destination folder to move the selected files to');
				var folders = resp.split('|');
				
				var str = "<select id='move-file-select'><option value=''> -- Select -- </option>";
				for (i in folders)
				{
					str += "<option value='"+folders[i]+"'>"+folders[i]+"</option>";
				}
				
				str += "</select> <input type='button' value='Move' onclick='submit_move_file()' /> <input type='button' value='Cancel' onclick='reset_context_box()' />";
				insert_context(str);
			}
		} else {
			insert_msg('No file selected');	
		}	
		

	}
	
	function submit_move_file()
	{
		fm.submit_move_file();	
	}
	
	function submit_rename_file()
	{		
		var i = fm.single_file();
		var name = document.getElementById('file-'+i+'-name').innerHTML;
		
		if (name == document.getElementById('rename-file-input').value) {
			file_rename_context();
		} else {
			fm.submit_rename_file();	
		}
	}
	
	function download_file(num)
	{
		var path = document.getElementById('file-'+num+'-path').innerHTML.replace(fm.file_base_url, '');
		
		window.location = fm.current_url + '?file='+encodeURIComponent(path);
	}
	
	function select_file_context()
	{
		reset_context_box();
		
		if (fm.current_file || fm.current_file == 0) {
			var name = fm.current_file_name();
			insert_msg('Select ' + name + '?');
			
			var str = "<input type='button' value='Select' onclick='submit_select_file()' /> <input type='button' value='Cancel' onclick='reset_context_box()' />";
			insert_context(str);
		}
	}
	
	function submit_select_file()
	{		
		if (fm.current_file || fm.current_file == 0) {
			var path = fm.current_file_path();
			if (typeof fm.CKEditorFuncNum != null) {
				window.opener.CKEDITOR.tools.callFunction(fm.CKEditorFuncNum, path);
			} else {
				insert_msg('Could not find return function', true);	
				return false;
			}
				
			self.close();
		}
	}