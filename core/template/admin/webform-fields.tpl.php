<?php
	if (!$form['weight']) $form['weight'] = $new_weight;
	$req = $form['required'] ? "checked='checked'" : '';
	$email = $form['options'] == 'email' ? "checked='checked'" : '';
	
	$common = "					
		<input type='hidden' name='form_submitted' value='1' />	
		<input type='hidden' name='action' value='submit_$action' />					
		<input type='hidden' name='action_id' value='$action_id' />					
		<input type='hidden' name='webform_id' value='$webform_id' />					
		<p class='center'>
			<input type='submit' name='submit' value='Submit' />
			<input type='button' class='cancel' value='Cancel' onclick=\"window.location='$admin_page'\"/>	
		</p>";
		
	$required = "<li><label>Required:</label><input type='hidden' name='required' value='0' /><input type='checkbox' name='required' value='1' $req/></li>";
	$weight = "<li><label>Weight:</label><input type='text' name='weight' value='$form[weight]'/></li>";
	$name_label = "
		<input type='hidden' name='type' class='type' value='$form[type]'/>
		<li><label>Name:</label><input type='text' name='name' value='$form[name]' /></li>
		<li><label>Label:</label><input type='text' name='label' value='$form[label]' /></li>	";
?>

<style type='text/css'>
	.field-box { display: none; }
	#options { display: none; }
</style>
<script type='text/javascript'>
	$(function() {
		$('.cancel').click(function() {
			$.fancybox.close();
		});
	
		$('#type').change(function() {
			var sel = $(this).val();
			
			if (sel) {
				$('.type').val(sel);
			
				$('#field-detail').children().each(function() {
					if ($(this).hasClass('field-'+sel)) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
		});	
		
		$('.field-select #subtype').change(function() {
			var sel = $(this).val();
			if (sel == 'custom') {
				$('#options').show();
			} else {
				$('#options textarea').val('');
				$('#options').hide();
			}
		});
		
		<?php if ($form['type']) { ?>
			$('.field-<?=$form['type']?>').show();
			$('#type').val('<?=$form['type']?>');
		<?php } ?>
		
		<?php if ($form['subtype'] == 'custom') { ?>
			$('#options').show();
		<?php } ?>
	});
</script>
<div>
	<ul class='form-ul'>
		<li><label style='width: 130px;'><strong>Select Field Type:</strong></label>
			<select name='type' id='type'>
				<option value=''> -- Select -- </option>
				<option value='text'>Text</option>
				<option value='select'>Select</option>
				<option value='checkbox'>Checkbox</option>
				<option value='textarea'>Textarea</option>
			</select>
		</li>
	</ul>
	
		<div id='field-detail'>
			<div class='field-text field-box'>
				<form method='post' action='<?=$manager_url?>'>
					<ul class='form-ul'>
						<?=$name_label?>
						<?=$required?>
						<li><label>Subtype:</label>
							<select name='subtype' id='subtype'>
								<option value=''> -- None -- </option>
								<option value='email' <?php if ($form['subtype'] == 'email') echo "selected='selected'" ?>>Email</option>
							</select>
						</li>
						<?=$weight?>
					</ul>	
					<?=$common?>
				</form>
			</div>
			<div class='field-select field-radio field-box'>
				<form method='post' action='<?=$manager_url?>'>
					<ul class='form-ul'>
						<?=$name_label?>
						<?=$required?>
						<li><label>Subtype:</label>
							<select name='subtype' id='subtype'>
								<option value='states' <?php if ($form['subtype'] == 'states') echo "selected='selected'" ?>>US States</option>
								<option value='months' <?php if ($form['subtype'] == 'months') echo "selected='selected'" ?>>Months</option>							
								<option value='custom' <?php if ($form['subtype'] == 'custom') echo "selected='selected'" ?>>Custom</option>							
							</select>
						</li>
						<li id='options'>
							<label>Options<br/>key|value</label>
							<textarea name='options' rows='4' cols='25'><?=$form['options']?></textarea>
						</li>
						<?=$weight?>
					</ul>	
					<?=$common?>
				</form>
			</div>
			<div class='field-checkbox field-box'>	
				<form method='post' action='<?=$manager_url?>'>
					<ul class='form-ul'>
						<?=$name_label?>
						<?=$required?>
						<li><label>Value:</label><input type='text' name='subtype' value='<?=$form['subtype']?>' /></li>
						<?=$weight?>
					</ul>	
					<?=$common?>
				</form>
			</div>
			<div class='field-textarea field-box'>	
				<form method='post' action='<?=$manager_url?>'>
					<ul class='form-ul'>
						<?=$name_label?>
						<?=$required?>
						<?=$weight?>
					</ul>	
					<input type='hidden' name='subtype' value='' />
					<?=$common?>
				</form>
			</div>
		</div>
	</form>
</div>