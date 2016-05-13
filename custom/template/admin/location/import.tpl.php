<p><em>Acceptable file type: csv.</em></p>
<p>(Optional fields: address2, phone, web, hours1, hours2, hours3, latitude and longitude.)</p>
<p><a href="<?php echo $https; ?>/public/inc/store_locations_example.csv" target="_blank" class='action' download><span class="icon icon-download3"></span>Download Example CSV File</a></p>

<form method='post' action='<?php echo $url ?>' enctype="multipart/form-data">
	<div><input type='file' name='file'></div>
    <p><button type='submit' class='submit btn'>Upload</button></p>
</form>