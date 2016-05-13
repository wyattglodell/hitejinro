<?php
	$results = array();
	
	if (!$_POST['search'] && $get->text) {
		$_POST['search'] = $get->text;
	}

	if ($_POST['search']) {
		if (strlen($_POST['search']) > 2) {
			$search = $sql->sanitize($_POST['search']);
	
			$temp = array();
	
			$temp = $func->search($conf->PAGE, $search, array('name','title','content'), 'AND status=1 AND is_content=1 ORDER BY weight');
			
			foreach ($temp as $v)
			{
				$row['alias'] = $conf->path.'/'.ltrim($v['full_alias'], '/');
				$row['body'] = substr(strip_tags($v['content']), 0, 180).'..';
				$row['title'] = $v['name'];
				
				$results[] = $row;	
			}
			
			
			$tpl->assign('results', $results);
		} else {
			$msg = 'Search string is not long enough';
		}
	}
	
	$tpl->assign('search', htmlentities($_POST['search'], ENT_QUOTES));
	$tpl->assign('search_msg', $msg);

	$tpl->assign('count', count($results));
	$tpl->set_template('base', 'filemanager-browse-content.tpl.php');
?>