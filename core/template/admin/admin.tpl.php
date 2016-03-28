<script type='text/javascript'>
	var icon = '<?=$icon?>';
	
	$(function() {		
		$("table.sortable").tablesorter({ 
			headers: { 
			<?php
				if ($fields['header']) {
					$num = 0;
					foreach ($fields['header'] as $k=>$v)
					{
						if (!$v['sort']) {
							echo "$num: { sorter: false },";
						}
						$num++;
					}
					echo "$num: { sorter: false }";
				}
			?>
			} 
		}); 
		
		$('#tbl th.header').append("<span class='icon icon-menu10'></span>");
	});
	
	<?php echo $filemanager_js?>

	<?php echo $open_edit?>
</script>
<div id='admin'>
    <div id='nav-box'>
        <div class='pad'>
            <?php echo $tpl->menu?>
        </div>
    </div>
    <div id='breadcrumb'>
        <div id='logged-in-as'>
            <?php if ($real_user['username']) { ?>
                 <a href='<?php echo $admin_url?>?switch_back=1'>switch back to <?php echo $real_user['username']?></a> 
            <?php } ?>
			
			<?php if ($user['username']) { ?>
                <a>hello <?php echo $user['username'] ?></a>
                
                <a href='<?php echo $profile_url?>'>update profile</a>
                
                <a href='<?php echo $http?>' target='_blank'>view website</a>
                
            	<a href='<?php echo $admin_url?>/logout'>logout</a>         
            <?php } ?>
        
        
        </div>
        <?php echo $admin_breadcrumb?>
    </div>
    
    <div id='content'>
        <div class='inner'>
            <div class='pad'>
            	<?php if ($management_header) { ?>
              		<h1><?=$management_header?></h1>
                <?php } ?>
                <div class='box'>
                	<?=$tpl->admin?>
                </div>
            </div>
        </div>
        <div class='clear'></div>
    </div>
    <div class='clear'></div>
</div>
