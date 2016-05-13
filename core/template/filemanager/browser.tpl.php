<!doctype html>
<html>
<head>
	<?php echo $tpl->filemanager_header ?>
</head>

<body>
	<iframe id='upload_frame' name='upload_frame' src=''></iframe>
	<div id='main-box-wrapper'>
		<div id='main-box'>
			<div id='top-box'>
				<table cellspacing='0' cellpadding='0' width='100%'>
					<tr>
						<td width='320'>
							<div id='upload-file-box' class='box'>
								<h1>Upload Files</h1>
								<form action='<?php echo $filemanager_upload_handler ?>' target='upload_frame' method='post' enctype='multipart/form-data' onsubmit='return upload_files()'>
								<div class='pad'>
									<div id='file-upload-box'>
									<?php
										for ($i = 0; $i < 3; $i++)
										{
											echo "<input type='file' id='file-input-$i' name='file[]' size='29' class='file-input' /><br/>";
										}
									?>
									</div>
									<input type='hidden' name='thumbnail_option' value='<?php echo CREATE_THUMB?>' />
									<input type='hidden' name='thumbnail_width' id='thumbnail_width' value='<?php echo THUMBNAIL_WIDTH?>'/>
									<input type='hidden' name='thumbnail_height' id='thumbnail_height' value='<?php echo THUMBNAIL_HEIGHT?>'/>
									
									<div class='br'></div>
									Resize Image <input type='checkbox' name='image_resize' value='1' id='image_resize' />
									<span id='resize-option-box'>
										<select name='resize_option' id='resize-option'>
											<option value='aspect'>Keep Aspect Ratio</option>
											<option value='fixed'>Fixed Dimension</option>
										</select>
									</span>
									<input type='hidden' name='resize_width' id='resize_width_data' />
									<input type='hidden' name='resize_height' id='resize_height_data' />
									
									<input type='hidden' name='current_folder' id='current_folder' />
									<div class='br'></div>
									<input type='submit' value='Upload' />
								</div>
								</form>
							</div>
						</td>
						<td  id='context-box-td' >
							<div class='box' id='context-box-wrap'>
								<h1>Options &amp; Status</h1>	
								<div class='pad'>	
									<div id='msg-box'></div>
									<div id='context-box'></div>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div id='list-box'>
				<table cellspacing='0' cellpadding='0' width='100%'>
					<tr>
						<td width='200'>
							<div id='folder-list-box'>
								<h1>Folders</h1>
								<div id='folder-control'> 
									<a href='javascript:void(0)' id='add-folder-btn'>Create</a>
									<a href='javascript:void(0)' id='rename-folder-btn'>Rename</a>
									<a href='javascript:void(0)' id='delete-folder-btn'>Delete</a>
								</div>
								<div id='folder-list'></div>
							</div>
						</td>
						<td>
							<div id='file-list-box'>
								<h1>Files</h1> 
								<div id='file-control'>
									<a href='javascript:void(0)' id='select-file-btn'>Select</a>
									<a href='javascript:void(0)' id='rename-file-btn'>Rename</a>
									<a href='javascript:void(0)' id='move-file-btn'>Move</a>
									<a href='javascript:void(0)' id='delete-file-btn'>Delete</a>
								</div>
								<div id='file-list'></div>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<script type='text/javascript'>
		fm.setup();
	</script>	
</body>
</html>
