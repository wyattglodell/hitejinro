<?php if ($search_html) { ?>
    <form method='post' action='<?=$manager_url?>'>
        <input type='hidden' name='action' value='filter'>
        <div id='admin_filter'>
            <table cellspacing='0' cellpadding='0'>
            	<tr>
                	<td>
                    	<table cellspacing='1' cellpadding='0' class='admin-search-tbl'>
                			<tr><?php echo $search_html ?></tr>
                        </table>
                	</td>
                    <td><button type='submit' class='btn'>Search</button> <button type='submit' name='reset' value='reset' class='btn'>Clear</button></td>
                </tr>
            </table>
        </div>
    </form>
<?php } ?>
