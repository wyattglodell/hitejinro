$(function() {
	file_upload_init();
});

function file_upload_init()
{
	var html5_upload = !! ( window.FormData && ("upload" in ($.ajaxSettings.xhr()) ));	

	if (html5_upload) {	/*
		var filedrop = $('.file-input').parent();
		
		$(filedrop).on('dragenter', function(e) {
			e.preventDefault();
			
			$(this).addClass('active');
		});
		
		$(filedrop).on('dragover', function(e) {
			e.preventDefault();
			
			$(this).addClass('active');
		});
	
		$(filedrop).on('dragleave', function(e) {
			e.preventDefault();
			$(this).removeClass('active');
		});
		
		$(filedrop).on('drop', function(e) {
			$(this).removeClass('active');
			fileupload_drop(e, this, true);
		});*/
		
		browse_image();
	} else {
		set_msg("Your browser does not support HTML5, please use a modern browser");
	}
}

function fileupload_drop(e, me, dropped)
{
	e.preventDefault();
	
	var formData = new FormData();
	var total_size = 0;

	if (typeof max_upload_size == 'undefined') {
		max_upload_size = 2048000;
		max_upload_size_formatted = '2MB';
	}

	if(dropped){ 
		var self = me;
		if(e.originalEvent.dataTransfer.files.length) {
			for (i in e.originalEvent.dataTransfer.files)
			{
				if (!isNaN(e.originalEvent.dataTransfer.files[i].size)) {
					total_size += e.originalEvent.dataTransfer.files[i].size;
					formData.append('file[]', e.originalEvent.dataTransfer.files[i]);
				}
			}
		}
	} else {
		var self = $(me).parents('.fm-file-row');
		for (i in me.files)
		{
			if (!isNaN(me.files[i].size)) {
				total_size += me.files[i].size;
				formData.append('file[]', me.files[i]);
			}
		}
	}
	
	formData.append('inline_upload', 1);
	formData.append('current_folder', 'auto_upload');

	if (max_upload_size < total_size) {
		set_msg("This file is too large to be uploaded, a maximum of " + max_upload_size_formatted + " is allowed");	
	} else {
		$(self).removeClass('active');
		$(self).find('a.browse-file').hide();
		$(self).find('.progress-box').show();

		var xhr = new XMLHttpRequest();
		if ( xhr.upload ) {
			xhr.upload.onprogress = function(e) {
				var done = e.position || e.loaded, total = e.totalSize || e.total;
				$(self).find('.progress-bar').width((Math.floor(done/total*1000)/10) + '%');
			};
		}
		
		xhr.onreadystatechange = function(e) {
			if ( 4 == this.readyState ) {
				var response;
				
				try {
					response = $.parseJSON(this.responseText);
				} catch (e) {
					console.log(this.readyState);
					set_msg("An error has occured, check the console for the message");
					return false;
				}
				
				$(self).find('.progress-box').hide();

				$(self).find('span.file-input').html( $(self).find('span.file-input').html());

				$(self).find('input.upload-file').on('change', function(e) {
					e.preventDefault();	
					fileupload_drop(e, this, false);
				});
				
				if (response.status) {
					if (typeof fileupload_complete != 'undefined') { // overwrite default complete handling
						fileupload_complete(self, response.files);
					} else {
						insertURL(self, response.files);
					}
				} else {
					set_msg(response.message);
				}
			}
		};

		xhr.open('post', file_upload_url, true);
		xhr.send(formData);
	}
}
	
function insertURL(self, urls) 
{ 
	var first = true;
	var group = $(self).closest('.group-field');
	var files = urls || [];
	
	var fm_id;
	var use_this;
	var cols_class = $(self).closest('td.field-col').attr('col');
	var counter = 1;

	if (urls != 'null') {

		for (i in files)
		{
			var url = upload + files[i];
			preview_url = url;
	
			var fn = url.split('/');
			var file_name = fn[fn.length-1];
			
			if (path) {
				url = url.substr(path.length);
			}
			
			var ext = url.substr(-4, 4).toLowerCase();
			if (ext == '.jpg' || ext == '.png' || ext == '.gif' || ext == 'jpeg' || ext == '.ico') {
				var is_img = true;	
			} else {
				var is_img = false;	
			}
			
			if (first) {
				fm_id = self;
			} else {
				use_this = false;
	
				$(group).find('td.field-col[col="'+cols_class+'"] .fm-file').each(function() {
					if (!$(this).val()) {
						use_this = $(this).parent();
						return false;
					}
				});
	
				if (use_this) {
					fm_id = use_this;
				} else {
					clone_row(group);
					
					$(group).find('td.field-col[col="'+cols_class+'"] .fm-file').each(function() {
						if (!$(this).val()) {
							fm_id = $(this).parent();
							return false;
						}
					});
				}     
			}
				
			if (!is_img) {
				if (ext == '.pdf') {
					preview_url = icon + '/ext_pdf.gif';	
				} else if (ext == '.doc' || ext == '.docx' || ext == '.rtf') {
					preview_url = icon + '/ext_doc.gif';	
				} else if (ext == '.txt') {
					preview_url = icon + '/ext_text.gif';	
				} else if (ext == '.zip') {
					preview_url = icon + '/ext_zip.gif';	
				} else if (ext == '.ppt') {
					preview_url = icon + '/ext_ppt.gif';	
				} else {
					preview_url = icon + '/ext_unknown.gif';	
				}
			}
		
			html = "<div class='fm-img-preview'><div class='fm-img'><img src='" + preview_url + "'></div><div class='clear-img-btn'>"+file_name+"<button type='button' class='fm-img-delete btn'>Remove</button></div></div>";
			
			$(fm_id).find('a.browse-file').hide();
			$(fm_id).append(html);
			$(fm_id).find('.fm-file').val(url);
			
			fm_img_delete();
			
			first = false;
		}
	} else {
		$(self).find('a.browse-file').show();
	}
}

function browse_image()
{
	$('.upload-file').unbind('change');
	$('a.browse-file').unbind('click');
	
	$('a.browse-file').click(function(e) {
		e.preventDefault();
		if (typeof file_upload_url == 'undefined') {
			alert('file_upload_url is not set!');	
		} else {
			$(this).parent().find('input.upload-file').trigger('click');
		}
	});
	
	$('.upload-file').on('change', function(e) {
		e.preventDefault();	
		fileupload_drop(e, this, false);
	});
}