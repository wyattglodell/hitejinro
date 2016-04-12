<?php
	$news = array();
	
	if ($get->b) {
		$sql->query("SELECT *, DATE_FORMAT('$conf->sql_date', news_dt) as date_formatted FROM $conf->NEWS WHERE status = 1 AND alias = '".$sql->sanitize($get->b)."'");
		$news = $sql->fetch();
		
		if ($news) {
			$news['photo'] = $func->get_img($news['photo'], 'small');
			
			$tpl->assign('news', $news);
			$tpl->set_template('content', 'news-detail.tpl.php');
		} else {
			$func->error404();
		}
	} else {
		$sql->query("SELECT *, DATE_FORMAT('$conf->sql_date', news_dt) as date_formatted FROM $conf->NEWS WHERE status = 1 ORDER BY news_dt DESC LIMIT 10");
		while ($row = $sql->fetch())
		{
			$row['photo'] = $func->get_img($row['photo'], 'tiny');
			$row['content'] = $func->truncate($row['content']);

			if (!is_array($news[$row['site']])) $news[$row['site']] = array();
			$news[$row['site']][] = $row;
		}
	
		$tpl->assign('news', $news);
		$tpl->set_template('content', 'news.tpl.php');
	}
?>