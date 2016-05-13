<div id='breadcrumb'><a href='<?=$admin_url?>'>Home</a> &gt; <a>Module Management</a></div>
<h1>Module Management</h1>
<div class="msg"><?=$errmsg?></div>
<form enctype="multipart/form-data" method="POST">
	<div>
		Choose a file to upload:
		<input name="package_file" type="file" />
		<input type="submit" value="Upload File"/>
	</div>
</form>
<div id='log'>
	<ul id='install-log'>
		<?php
		foreach($install_log as $v){
			echo "<li>$v</li>";
		}
		?>
	</ul>
	<ul id='error-log'>
		<?php
		foreach($error as $v){
			echo "<li>$v</li>";
		}
		?>
	</ul>
</div>