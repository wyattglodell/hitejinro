<?php
	if ($search_msg) {
		echo "<h1>$search_msg</h1>";
	} 
?>

<h2>
    Number of results found: <?=$count?>
</h2>

<?php
	if ($results) {
		echo "<ul id='search-result'>";	
		foreach ($results as $result)
		{
			echo "<li><h3><a href='$http/$result[alias]'>$result[title]</a></h3>";
			
			if ($result['body']) {
				echo "<p>".$result['body']."</p>";
			}
					
			echo '</li>';
		}
		echo "</ul>";
	}
?>