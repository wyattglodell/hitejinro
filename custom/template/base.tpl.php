<!doctype html>
<html>
	<head>
		<?php echo $tpl->head?>
	</head>
	
	<body class='body-bg <?php echo $body_classes ?>'>
    	<?php if ($system_msg) { ?>
    		<div id='system-msg' style='display: none;'><?php echo $system_msg?></div>
        <?php } ?>
        
		<div id='body-inner'>
			<?php echo $tpl->body?>
        </div>
    </body>
</html>