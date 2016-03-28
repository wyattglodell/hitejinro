function popup(url)
{
	$.fancybox({
		href: url,
		hideOnOverlayClick: false,
		onComplete: function() {
			control();
			
			$('.popup').click(function(e){
				e.preventDefault();			 	
				var url = $(this).attr('href');
				popup(url);
			});	
		}
	});
}

function handle_ajax_response(self, href)
{
	var temp_html = $(self).html();
	
	$(self).html("<span class='icon icon-spinner10 animate-loading'></span>");

	$.ajax({
		url: href,
		data: '',
		success: function(data) {
			try {
				var resp = $.parseJSON(data);
				if (resp.login) {
					window.location = window.location;
				}
				
				if (resp.status == true) {
					var classes = $(self).attr('class');
					
					var m = classes.match(/type\-[a-z0-9]+/);
					
					if (m[0] || resp.message) {
						switch (m[0])
						{
							case 'type-delete':
								if (resp.message) {
									set_msg(resp.message);	
								}
							
								$(self).closest('tr.rows').fadeOut('','', function() {
									var odd = '';
									$('#tbl .rows').each(function() {
										if (!$(this).is(':hidden')) {
											$(this).removeClass('odd');
											odd = odd ? '' : "odd";
											
											$(this).addClass(odd);
										}
									});
								});	
							break;	
							
							case 'type-toggle':
								if (resp.message) {
									set_msg(resp.message);	
								}
								
								var $span = $(self).html(temp_html).find('span.icon');
								
								if ($span.attr('status') == 'active') {
									$span.removeClass($span.attr('active')).addClass($span.attr('inactive'));
									$span.removeClass($span.attr('active_class')).addClass($span.attr('inactive_class'));
									
									$span.attr('status', 'inactive')
								} else { 
									$span.removeClass($span.attr('inactive')).addClass($span.attr('active'));
									$span.removeClass($span.attr('inactive_class')).addClass($span.attr('active_class'));
									
									$span.attr('status', 'active')
								}
							break;
							
							case 'type-hide':
								$(self).fadeOut();
								set_msg(resp.message);	
							break;
							
							case 'type-redirect':
								window.location = resp.url;
							break;
							
							case 'type-blank':
								window.open(resp.url);
							break;
							default:
								set_msg(resp.message);	
							
							break;
						}
					} else {
						$(self).html(temp_html);
						console.log(resp);	
					}
				} else if (resp.status == false) {
					$(self).html(temp_html);
					set_msg(resp.message);	
				} else {
					$(self).html(temp_html);
					set_msg(data);	
				}
			} catch(e) {
				$(self).html(temp_html);
				set_msg(data);
			}

		},
		error: function(data) {
			$(self).html(temp_html);
			set_msg(data);	
		}
	});		
}

function control()
{
	if (typeof $.datepicker != 'undefined') {
		$('.datepicker').datepicker();
	}	
	
	$('.control').click(function(e) {
		e.preventDefault();			 	

		var href = $(this).attr('href');
		var answer = true;
		var self = this;
		var span_html = $(this).html();
		
		var ajax = $(this).hasClass('ajax');
		
		if (ajax && href.indexOf('ajax=1') == -1) {
			href += "&ajax=1";
		}

		if ($(this).hasClass('confirm') && e.altKey == false) {
			set_msg('Are you sure you want to '+ $(this).attr('confirm_text') +'?', {
				buttons: { Yes: true, Cancel: false },
				submit:function(e,v,m,f) {
					if (v) {
						if (ajax) {
							handle_ajax_response(self, href);
						} else {
							window.location = href;	
						}
					}
				}
			});				
		} else {
			if (ajax) {
				handle_ajax_response(self, href);
			} else {
				window.location = href;	
			}
		}
	}); 	
}

$(function() {	
	var content_height = $('body').height() - $('#header-box').innerHeight() - $('#breadcrumb').innerHeight()-1;
	
	$('#content').css('min-height', content_height > 800 ? content_height: 800);
	
	$('.blank').attr('target','_blank');
	
	$('.popup').click(function(e){
		e.preventDefault();			 	
		var url = $(this).attr('href');
		popup(url);
	});
	
	control();
	
	if ($('#admin_menu a.main').length) {
		$('#admin_menu a.here.main').parent().find('ul.sub').show();
		
		$('#admin_menu a.main').click(function(e) {
			if ($(this).parent().find('ul.sub').length) {
				e.preventDefault();
				
				$(this).parent().find('ul.sub').slideToggle();
			}
		});
	}

	$('.menu-view-website').attr('target','_blank');

	$('.tab-name').click(function() {
		if (!$(this).hasClass('active')) {
			var tab_name = $(this).attr('tab_name');
			
			$('.tab-name.active').removeClass('active');
			$('.tab-content.active').removeClass('active');
			
			$(this).addClass('active');
			$('.tab-content[tab_name="'+tab_name+'"]').addClass('active');
			
			resize_field_group();
		}
	});
	
	if ($('.add-another').length) {
		timer = setTimeout('resize_field_group()', 500);
		
		field_event();
	}	
	
	fm_img_delete();
	
	$('select.has_parent').each(function() {

		var clone = $(this).clone().attr('name', '').attr('parent', '').attr('base_name', '');
		
		var self = this;
		var parent_id = $(this).attr('parent');
		var parent = $(this).closest('.form-group-tbl').find('select[base_name="'+parent_id+'"]');
		var label = $(parent).parent().find('label').html();
		
		$(this).hide();
		$(this).parent().append(clone);
		
		$(parent).change(function() {
			if ($(this).val()) {
				$(clone).html('');
				
				$(self).find('option[parent_id="'+$(this).val()+'"]').each(function() {
					$(clone).append($(this).clone());
				});
				
				if ($(clone).html()) {
					$(clone).removeClass('disabled').attr('disabled', false);	
				}
				
				$(clone).trigger('change');
			} else {
				$(clone).html("<option value=''>Select a "+label.replace('*','')+"</option>");
			}
		});
		
		$(clone).change(function() {
			$(self).val($(this).val());
		});
		
		$(parent).trigger('change');
	});
	
	$('select[conditions]').each(function() {
		$(this).on('change', function() {
			var options = $.parseJSON($(this).attr('conditions'));
			
			for (var opt in options)
			{
				var items = options[opt].split('|');
				for (var i in items)
				{
					$('tr.field-'+items[i]).hide();
				}
			}
			
			if ($(this).val() && typeof options[$(this).val()] != 'undefined') {
				var items = options[$(this).val()].split('|');
				for (var i in items)
				{
					$('tr.field-'+items[i]).show();
				}
			}
		});
		
		$(this).trigger('change');
	});
	
	if ($('.random-password').length) {
		$('.random-password').click(function() {
			var valid = ['48-57','65-90','97-122'];
			var num, rng;
			var string = '';
			
			for (var i=0; i<=10; i++)
			{
				var num = Math.floor(Math.random() * valid.length);
				rng = valid[num].split('-');
				
				string += String.fromCharCode(Math.floor(Math.random() * (+rng[1] - +rng[0] + 1)) + +rng[0]);
			}
			
			set_msg("The password generated is: " + string);
			$(this).parent().find('.password').val(string);
		});
	}
});

function fm_img_delete()
{
	$('.fm-img img').click(function() {
		$.fancybox({href: $(this).attr('src')});
	});
	
	$('.fm-img-delete').click(function() {
		var parent = $(this).closest('.fm-file-row');
		$(parent).find('a.browse-file').show();
		$(parent).find('.fm-img-preview').remove();
		$(parent).find('.fm-file').val('');
	});	
}

function resize_field_group()
{
	$('table.form-group-tbl.repeat').each(function() {
		$(this).height('auto');
		$(this).width($(this).parent().width()-(parseInt($(this).parent().css('padding'))*2)).height($(this).height());
	});
}

function field_event()
{
	
	if ($('.add-another').length) {
		$('.add-another').unbind('click');
		
		$('.add-another').click(function() {
			clone_row(this);
		});
	}	
	
	$('.group-field').each(function() {
		if ($(this).find('.row-move').length) {
			$(this).sortable({
				axis:'y',
				cursor:'move',
				handle:'.row-move',
				containment:'parent',
				tolerance: 'pointer',
				helper: 'clone',
				update: 	function() {
					index_field_name();
				}
			});
		} else if ($(this).find('.field-move').length) {
			$(this).sortable({
				axis:'y',
				cursor:'move',
				handle:'.field-move',
				containment:'parent',
				tolerance: 'pointer',
				helper: 'clone',
				update: 	function() {
					index_field_name();
				}
			});
		} 
	});	
	
	$('.group-field td.delete').click(function() {
		if ($(this).closest('.group-field').find('> table').length == 1) {
			var parent = $(this).closest('.group-field');
			$(parent).find('a.browse-file').show();
			$(parent).find('.progress-box').hide();
			$(parent).find('.fm-img-preview').remove();
			$('input[type!="button"],select,textarea', parent).val('');
			$(parent).find('.form-field-group-tbl:not(:first)').remove();
		} else {
			$(this).closest('table.form-group-tbl').remove();
		}
		
		index_field_name();
		resize_field_group();
	});
	
	
	index_field_name();
	resize_field_group();
}

function index_field_name()
{

	$('td.group-field-td').each(function() {
		if ($(this).find('> .more-btn .add-another-row').length) {
			$(this).find('> .group-field').each(function() {
				var index = 0;
				$(this).find('> table.form-group-tbl').each(function() {
					$(this).find('input[type!="button"],select,textarea').each(function() {
						if ($(this).attr('name') && $(this).attr('name').match(/\[\d+\]/)) {
							$(this).attr('name', $(this).attr('name').replace(/\[\d+\]/, '['+(index)+']'));
						}
						if ($(this).attr('base_name') && $(this).attr('base_name').match(/\[\d+\]/)) {
							$(this).attr('base_name', $(this).attr('base_name').replace(/\[\d+\]/, '['+(index)+']'));
						}
					});
					index++;
				});
			});
		}
	});
}

function clone_row(obj)
{
	var tbl = $(obj).closest('td').find('> .group-field > table.form-group-tbl').first().clone();

	if ($(tbl).find('.fm-file-row').length) {
		$(tbl).find('.fm-img-preview').remove();
		$(tbl).find('.progress-box').hide();
		$(tbl).find('a.browse-file').show();
	}
	
	$(tbl).find('.form-field-group-tbl:not(:first)').remove();
	
	$('input[type!="button"],select,textarea', tbl).val('');
	
	$(obj).closest('td').find('> .group-field').append(tbl);
	
	field_event();
	browse_image();
}