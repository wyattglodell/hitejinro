function filemanager()
{
	this.valid_ext = new Array();
	this.obj_list = new Array();
	
	this.msie = (navigator.appName == "Microsoft Internet Explorer" &&  parseInt(navigator.appVersion) >= 4);
	
	this.ajax = null;
	
	this.can_upload = 1;
	this.can_movefile = 1;
	this.can_deletefile = 1;
	this.can_renamefile = 1;
	this.can_newfolder = 1;
	this.can_deletefolder = 1;

	this.base_url = null;
	this.current_url = null;
	this.upload_folder_url = null;
	this.full_url = null;
	this.current_folder = null;
	
	/*
	this.inline_setup = function()
	{
		document.getElementById('current_folder').value = this.current_folder;
	}*/
	
	this.setup = function()
	{
		this.ajax_init();
		this.update_folder_list();
		this.update_file_list();
		reset_resize_option();
		reset_file_input_box();
	}
	
	this.attach_file_events = function()
	{
		if (document.getElementById('check-all-checkbox')) document.getElementById('check-all-checkbox').onclick = function() { check_all_files(this); }
		if (document.getElementById('image_resize')) document.getElementById('image_resize').onclick = function() { image_resize(this); }
		if (document.getElementById('resize-option')) document.getElementById('resize-option').onchange = function() { resize_option(this); }
		var i = 0;
		var self = this;
		while (document.getElementById('file-'+i+'-checkbox'))
		{
			var c = document.getElementById('file-'+i+'-checkbox');
			
			c.onclick = function() { self.update_current_file(); }
			
			i++;
		}
		
		sortables_init();
	}
	
	this.update_current_file = function()
	{
		reset_context_box();
		
		if (this.multiple_file()) {
			this.multiple_file_selected = true;
			this.current_file = null;	
		} else {
			this.multiple_file_selected = false;
			this.current_file = this.single_file();	
			select_file_context();
		}
		
		this.attach_file_control();
	}
	
	this.current_file_name = function()
	{
		if (this.current_file || this.current_file == 0) {
			if (document.getElementById('file-'+this.current_file+'-name')) {
				return document.getElementById('file-'+this.current_file+'-name').innerHTML;
			}
		}
	}
	
	this.current_file_path = function()
	{
		if (this.current_file || this.current_file == 0) {
			if (document.getElementById('file-'+this.current_file+'-path')) {
				var url = document.getElementById('file-'+this.current_file+'-path').innerHTML.replace(fm.file_base_url, '');
				
				return fm.base_path + url;
			}
		}
	}
	
	this.single_file = function()
	{
		var i = 0;
		var sel = null;
		while (document.getElementById('file-'+i+'-checkbox'))
		{
			if (document.getElementById('file-'+i+'-checkbox').checked) { 
				return i;
			}
			i++;
		}			
		
		return null;			
	}
	
	this.multiple_file = function()
	{
		var i = 0;
		var num = 0;
		while (document.getElementById('file-'+i+'-checkbox'))
		{
			if (document.getElementById('file-'+i+'-checkbox').checked) { num++; }
			
			if (num > 1) return true;
			i++;
		}			
		
		return false;
	}
	
	
	this.update_folder_list = function()
	{
		var list = document.getElementById('folder-list');
		var string = 'fm_request=update_folder_list&current_folder='+this.current_folder;
		var resp = this.ajax_request(string);
		if (resp.substr(0, 5) == 'ERROR') {
			var chunk = this.split(resp);
			
			//insert_msg(chunk[1], true);
			list.innerHTML = "<p class='no-file'>"+chunk[2]+"</p>";
		} else {
			list.innerHTML = resp;
		}
		
		this.attach_folder_event();
	}
	
	this.attach_folder_event = function()
	{
		var list = document.getElementById('folder-list').getElementsByTagName('div');
		var self = this;
		for (var i=0; i < list.length; i++)
		{

			if (list[i].className == 'folder-item') {

				list[i].onmouseover = function() 
				{
					this.className += ' '+self.folder_hover_classname;
				}
				list[i].onmouseout = function() 
				{
					this.className = this.className.replace(' '+self.folder_hover_classname, '');
				}
				list[i].onclick = function() 
				{
					loading();
					this.className += ' '+self.folder_hover_classname;
					self.update_current_folder(this.id.substr(7));
					self.update_file_list();
					self.open_current_folder(this);
					self.attach_folder_control();
					
					reset_context_box();
				}
				
				if (list[i].id.substr(7) == self.current_folder) {
					list[i].className += ' '+self.folder_hover_classname;	
				}
			}
		}
		
		this.attach_folder_control();
	}
	
	this.reset_selected_file = function()
	{
		this.multiple_file_selected = false;
		this.current_file = null;
	}
	
	this.attach_folder_control = function()
	{
		var add = document.getElementById('add-folder-btn');
		var del = document.getElementById('delete-folder-btn');
		var rename = document.getElementById('rename-folder-btn');
		
		if (this.current_folder != this.file_folder) { // don't allow modification of base folder
			this.control_state(del, true);
			this.control_state(rename, true);
			
			del.onclick = folder_delete_context;
			rename.onclick = folder_rename_context;
		} else {
			this.control_state(del, false);
			this.control_state(rename, false);
			
			del.onclick = null;
			rename.onclick = null;
		}
		
		this.control_state(add, true);
		add.onclick = create_folder_context;
	}
	
	this.submit_create_folder = function(name)
	{
		var string = 'fm_request=create_folder&current_folder='+this.current_folder+'&new_folder='+name;
		var resp = this.ajax_request(string);
		if (resp.substr(0, 5) == 'ERROR') {
			var chunk = this.split(resp);
			
			insert_msg(chunk[1], true);
		} else {
			reset_context_box();
			
			insert_msg('Folder '+name+' created successfully!', false);
			this.update_folder_list();
		}
	}

	this.submit_delete_folder = function()
	{
		var string = 'fm_request=delete_folder&current_folder='+this.current_folder;
		var resp = this.ajax_request(string);
		if (resp.substr(0, 5) == 'ERROR') {
			var chunk = this.split(resp);
			
			insert_msg(chunk[1], true);
		} else {
			var f = fm.current_folder.split('/');
			var cur = f[ f.length-1];
			
			reset_context_box();
			insert_msg('Folder '+cur+' deleted successfully', false);
			
			this.current_folder = this.upload_folder;
			
			this.update_folder_list();
			this.update_file_list();
		}
	}
	
	
	this.submit_rename_folder = function()
	{
		var rename = document.getElementById('rename-folder-input').value;
		if (rename) {
			var string = 'fm_request=rename_folder&current_folder='+this.current_folder+'&rename_folder='+rename;
			var resp = this.ajax_request(string);
			if (resp.substr(0, 5) == 'ERROR') {
				var chunk = this.split(resp);
				
				insert_msg(chunk[1], true);
			} else if (resp == 'SUCCESS') {
				var f = fm.current_folder.split('/');
				var cur = f[ f.length-1];
				var old = fm.current_folder.substr(0, fm.current_folder.lastIndexOf('/'));

				this.current_folder = old + '/' + rename;	
				
				reset_context_box();
				insert_msg('Folder '+cur+' renamed to '+rename+' successfully', false);
				this.update_folder_list();
				this.update_file_list();
			} else {
				insert_msg('Oops an error occured! ' + resp, true);	
			}
		} else {
			insert_msg('No name was entered', true);	
		}
	}

	
	this.control_state = function (obj, state)
	{
		if (state) {
			obj.className += ' active';	
			obj.className = obj.className.replace('inactive', '');
			obj.className = obj.className.replace(' inactive', '');
		} else {
			obj.className = obj.className.replace('active', '');
			obj.className = obj.className.replace(' active', '');
			obj.className += ' inactive';	
		}
	}
	
	this.open_current_folder = function(obj)
	{
		var f = obj.getElementsByTagName('img');
		for (var i=0; i < f.length; i++)
		{
			if (f[i].className = 'folder-img') {
				this.close_current_folder();
				
				f[i].src = this.open_folder_img;
				
			}
		}
		
		obj.className += ' '+this.folder_hover_classname;
	}
	
	this.close_current_folder = function()
	{
		var box = document.getElementById('folder-list').getElementsByTagName('img');
		for (var i=0; i < box.length; i++)
		{
			if (box[i].className = 'folder-img') {
				box[i].src = this.closed_folder_img;
			}
		}	
		
		var f = document.getElementById('folder-list').getElementsByTagName('div');
		for (var i=0; i < f.length; i++)
		{
			f[i].className = f[i].className.replace(' '+this.folder_hover_classname, '');
		}
	}
	
	this.update_current_folder = function(folder)
	{
		this.current_folder = folder;	
	}
	
	this.update_file_list = function()
	{
		var list = document.getElementById('file-list');
		var string = 'fm_request=update_file_list&current_folder='+this.current_folder;
		var resp = this.ajax_request(string);
		if (resp.substr(0, 5) == 'ERROR') {
			var chunk = this.split(resp);
			//insert_msg(chunk[1], true);
			list.innerHTML = "<p class='no-file'>"+chunk[2]+"</p>";
		} else {
			list.innerHTML = resp;
		}
		
		this.reset_selected_file();
		this.attach_file_events();
		this.attach_file_control();
	}	
	
	this.attach_file_control = function()
	{		
		var move = document.getElementById('move-file-btn');
		var del = document.getElementById('delete-file-btn');
		var rename = document.getElementById('rename-file-btn');
		var sel = document.getElementById('select-file-btn');
		var file = document.getElementsByTagName('span');
		var self = this;

		if (this.current_file || this.current_file == 0) {
			this.control_state(del, true);
			this.control_state(rename, true);
			this.control_state(sel, true);
			this.control_state(move, true);
			
			sel.onclick = select_file_context; 
			del.onclick = file_delete_context;
			rename.onclick = file_rename_context;
			move.onclick = file_move_context;
			
		} else {
			this.control_state(rename, false);
			this.control_state(sel, false);
			this.control_state(move, false);
			
			del.onclick = null;
			rename.onclick = null;			
			sel.onclick = null;			
			move.onclick = null;			
			
			if (this.multiple_file_selected) {
				this.control_state(move, true);
				this.control_state(del, true);
				del.onclick = file_delete_context;
				move.onclick = file_move_context;
			} else {
				this.control_state(del, false);
				this.control_state(move, false);
			}
			
			if (file) {
				for (i in file)
				{
					if (file[i] && file[i].className == 'checkbox') {
						file[i].onclick = function() {
							var here = this.id.replace('name','checkbox');
							document.getElementById(here).checked = !document.getElementById(here).checked;
							self.update_current_file();
						}
					}
				}
			}			
		}
	}
	
	this.submit_delete_file = function()
	{
		var i = 0;
		var sel = '';
		while (document.getElementById('file-'+i+'-checkbox'))
		{
			if (document.getElementById('file-'+i+'-checkbox').checked) { 
				sel +=  document.getElementById('file-'+i+'-name').innerHTML + '|';
			}
			i++;
		}	

		if (sel) {
			var string = 'fm_request=delete_file&file='+sel+'&current_folder='+this.current_folder;
			var resp = this.ajax_request(string);
			if (resp.substr(0, 5) == 'ERROR') {
				reset_context_box();
				var chunk = this.split(resp);
				
				insert_msg(chunk[1], true);
			} else {
				reset_context_box();
				insert_msg(resp);
				this.current_file = null;
				this.update_file_list();
			}
		}
	}
	
	this.submit_move_file = function() 
	{
		var i = 0;
		var sel = '';
		var dest = document.getElementById('move-file-select').options[ document.getElementById('move-file-select').selectedIndex].value;
		
		while (document.getElementById('file-'+i+'-checkbox'))
		{
			if (document.getElementById('file-'+i+'-checkbox').checked) { 
				sel +=  document.getElementById('file-'+i+'-name').innerHTML + '|';
			}
			i++;
		}	

		if (sel) {
			var string = 'fm_request=move_file&file='+sel+'&current_folder='+this.current_folder+'&destination_folder='+ dest;
			var resp = this.ajax_request(string);
			if (resp.substr(0, 5) == 'ERROR') {
				reset_context_box();
				var chunk = this.split(resp);
				
				insert_msg(chunk[1], true);
			} else {
				reset_context_box();
				insert_msg(resp);
				this.current_file = null;
				this.update_file_list();
			}
		}
		
	}
	
	this.submit_rename_file = function()
	{
		if (this.multiple_file_selected) {
			insert_msg('More than one file selected', true);	
		} else {
			var i = this.single_file();
			
			if (i || i == 0) {
				var old_name = document.getElementById('file-'+i+'-name').innerHTML;
				var new_name = document.getElementById('rename-file-input').value;
				
				if (old_name && old_name != new_name) {
					var string = 'fm_request=rename_file&old_name='+old_name+'&new_name='+new_name+'&current_folder='+this.current_folder;
					var resp = this.ajax_request(string);
					if (resp.substr(0, 5) == 'ERROR') {
						reset_context_box();
						var chunk = this.split(resp);
						
						insert_msg(chunk[1], true);
					} else {
						reset_context_box();
						insert_msg(resp);
						this.current_file = null;
						this.update_file_list();
					}
				}
			}
		}
	}
	
	this.split = function(str)
	{	
		return str.split('|');
	}
	
	this.ajax_init = function()
	{
        try {
            this.ajax = new XMLHttpRequest();
        } catch (e){
            try {
                 this.ajax = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    this.ajax = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {
                    alert("Your browser broke!");
                }
            }
        }
	}
	
	this.ajax_request = function(data) {
		this.ajax.open("GET", this.ajax_url + '?' + data + '&random='+Math.random(), false);
        this.ajax.send(null); 
		
		var resp = this.ajax.responseText;
		return resp ? resp : 'ERROR|No response from server';
	}
}