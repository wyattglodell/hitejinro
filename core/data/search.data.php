<?php
	$ignore = array('a','all','am','an','and','any','are','as','at','be','but','can','did','do','does','for','from','had','has','have','here','how','i','if','in','is','it','its','no','not','of','on','or','so','that','the','then','there','this','to','too','up','use','what','when','where','who','why','you');
	$results = array();
	$_POST['search'] = trim($_POST['search']);
	
	if ($_POST['search']) {
		$s = explode(' ', $_POST['search']);
		
		foreach ($s as $v)
		{
			if 	(!in_array($v, $ignore)) {
				$search[] = $v;
			}
		}		
		
		if ($search) {
			$search = $sql->sanitize(implode(' ', $search));
	
			$temp = array();
	
			$temp = $func->search($conf->PAGE, $search, array('name','title','content'), 'AND status=1 AND is_content=1 ORDER BY weight');
			
			foreach ($temp as $v)
			{
				$row['alias'] = $v['full_alias'];
				$row['body'] = substr(strip_tags($v['content']), 0, 330);
				$row['title'] = $v['name'];
				
				$results[] = $row;	
			}
			
			
			$tpl->assign('results', $results);
			$msg = 'Searching for '.$sql->sanitize($_POST['search'], ENTITY);
		} else {
			$msg = 'Search string is not long enough or invalid';
		}
	} else {
		$msg = 'No search string entered';	
	}
	
	$tpl->assign('search_msg', $msg);

	$tpl->assign('count', count($results));
	$tpl->set_template('content', 'search.tpl.php');
?>