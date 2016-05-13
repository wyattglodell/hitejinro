<?php
	$news = array();
	
	if ($get->b) {
		$sql->query("SELECT *, DATE_FORMAT(news_dt, '$conf->sql_date') as date_formatted FROM $conf->NEWS WHERE status = 1 AND alias = '".$sql->sanitize($get->b)."'");

		$news = $sql->fetch();
		
		if ($news) {
			$news['photo'] = $func->get_img($news['photo'], 'small');
			
			$tpl->assign('news', $news);
			$tpl->set_template('content', 'news-detail.tpl.php');
		} else {
			$func->error404();
		}
	} else {
		$sql->query("SELECT *, DATE_FORMAT(news_dt, '$conf->sql_date') as date_formatted FROM $conf->NEWS WHERE status = 1 ORDER BY news_dt DESC LIMIT 10");
		
		while ($row = $sql->fetch())
		{
			$cut = $func->truncate($row['content'], 300);
			
			
			if (!$row['photo']) {
				$row['photo'] = $conf->public.'/img/'.$row['site'].'/news-default.jpg';	
			} else {
				$row['photo'] = $func->get_img($row['photo'], 'tiny');
			}
			
			if ($row['url']) {
				$row['more'] = $row['url'];	
				$row['target'] = '_blank';	
			} else if ($cut != $row['content']) {
				$row['target'] = '_self';	
				$row['more'] = $conf->http.'/'.$get->a.'/'.$row['alias'];	
			} 
			
			$row['content'] = $cut;	


			if (!is_array($news[$row['site']])) $news[$row['site']] = array();
			$news[$row['site']][] = $row;
		}
	
		$tpl->assign('news', $news);
		$tpl->set_template('content', 'news.tpl.php');
	}
?>