<?php 
	if ($crud_msg) { 
		echo "<p class='center bold'>$crud_msg</p>"; 
	} else {
?>
        <form method='post' action='<?=$manager_url?>'>
            <ul class='form-ul'>
                <li><label>Manager Alias</label><input type='text' name='manager_name' value='' /> <em>(ie: events)</em></li>
                <li><label>Table Name</label><input type='text' name='table_name' value='' /> <em>(Don't include the prefix: <?=$prefix?>)</em></li>
                <li><label>Table Reference</label><input type='text' name='table_reference' value='' /> <em>(ie: EVENTS)</em></li>
                <li><label>Primary Key</label><input type='text' name='primary_key' value=''/> <em>(ie: item_id)</em></li>
                <li><label>Keyword</label><input type='text' name='keyword' value=''/> <em>(ie: Event)</em></li>
                <li><label>Can Add</label><input type='checkbox' name='can_add' value='1' checked='checked'/></li>
                <li><label>Can Edit</label><input type='checkbox' name='can_edit' value='1' checked='checked'/></li>
                <li><label>Can Delete</label><input type='checkbox' name='can_delete' value='1' checked='checked'/></li>
                <li><label>Description</label>
                    <textarea name='description' rows='5' cols='75'></textarea>
                </li>
                <li><label>&nbsp;</label>
                    <input type='hidden' value='Submit' name='action' />
                    <input type='submit' value='Submit' name='submit'  class='btn'/>
                </li>
            </ul>
        </form>
<?php
	}
?>