<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Search Content</title>
	<style type='text/css'>
		body {  padding: 0px 20px;  }
		body, a  {font-family: Arial, Helvetica, sans-serif; font-size: 12px;  }
		h3 { margin-bottom: 0; }
		p { margin-top: 5px; }
		
		a { text-decoration: underline; cursor: pointer; }
    </style>


</head>

<body>
    <h1>Search Content</h1>
    
    <form method='post' action='<?=$url?>'>
        Search: <input type='text' name='search' value='<?=$search?>' placeholder='Enter Text' /> <input type='submit' value='Submit' />
    </form>
    <div>&nbsp;</div>
	<?php
        if ($search_msg) {
            echo "<h1>$search_msg</h1>";
        } 
    ?>
    <?php
        if ($results) {
            echo "<ul id='search-result' style='list-style: none; margin: 0; padding: 0;'>";	
            foreach ($results as $result)
            {
                echo "<li><h3><a onclick='self.opener.SetUrl(\"$result[alias]\"); self.close()'>$result[title]</a></h3>";
                
                if ($result['body']) {
                    echo "<p>".$result['body']."</p>";
                }
                echo '</li>';
            }
            echo "</ul>";
        }
    ?>
</body>
</html>