<?php
	class Menu extends Base
	{
		public $menu = array();
		public $start = 1;
		public $end = 0;
		public $status = false; # ignore status
		public $is_menu = true; # require menu=1 flag
		
		function set_status($status)
		{
			$this->status = $status;	
		}
		
		function set_is_menu($status)
		{
			$this->is_menu = $status;	
		}
		
		function load_tree() 
		{  
			$menu = $counter = $nonmenu = array();

			if ($this->sql) {
				
				$this->sql->query("SELECT * FROM ".$this->conf->PAGE." ORDER BY left_id ASC");
				
				while ($row = $this->sql->fetch())
				{
					while (count($counter) && $counter[count($counter)-1] < $row['right_id'])
					{
						array_pop($counter);
					}
					
					if ($this->end < $row['right_id']) {
						$this->end = $row['right_id'];
						$this->end_id = $row['page_id'];
					}
					
					if ($row['left_id'] == 1) {
						$this->start_id = $row['page_id'];
					}
					
					$row['counter'] = count($counter);
					
					$menu[] = $row;		
	
					if ($all && $sub_menu[$row['page_id']]) {
						foreach ($sub_menu[$row['page_id']] as $v)
						{
							$v['counter'] = $row['counter'] + 1;
							$menu[] = $v;	
						}		
					}
					
					$counter[] = $row['right_id'];
				}
				
				if ($non_menu && $menu) {
					$menu = array_merge($menu, $non_menu);
				}
				
				$this->menu = $menu;
			}
		}  	
		
		function get_tree()
		{
			return $this->menu;
		}
		
		function get_admin_tree($attachable = false)
		{
			$menu = array();
			
			foreach ($this->menu as $id=>$row)
			{
				if (!$attachable) {
					$pad = $row['counter'] > 0 ? str_repeat(' &nbsp; &nbsp; &nbsp; ', $row['counter']).' - - ' : '';
				}
				
				
				if (!$attachable || $row['attachable']) {
					
					$menu[$row['page_id']] = ' &nbsp; '.$pad.$row['name'];
				}
			}
			
			return $menu;
		}
		
		function get_branch($left,$right)
		{
			$branch = array();
			
			foreach ($this->menu as $id=>$menu)
			{
				if ($menu['left_id'] > $left && $menu['right_id'] < $right) {
					$branch[$id] = $menu;
				}
			}		
			
			return $branch;
		}
		
		function get_siblings($parent_id)
		{
			$siblings = array();
			$parent_id = (int) $parent_id;
			
			foreach ($this->menu as $id=>$menu)
			{
				if (!$this->is_menu || $menu['menu']) {
					if ($menu['parent_id'] == $parent_id) {
						$siblings[$id] = $menu;
					}
				}
			}
			
			return $siblings;
		}
				
		function update_tree()
		{
			$this->rebuild_tree(0, 0);
		}
		
		function rebuild_tree($parent_id, $left) {     
			$right = $left + 1;     
			 
			$rs = $this->sql->query("SELECT * FROM ".$this->conf->PAGE." WHERE parent_id = $parent_id ORDER BY weight"); 
			while ($row = $this->sql->fetch($rs))
			{   
				$right = $this->rebuild_tree($row['page_id'], $right);     
			}     
			
			if ($this->menu[$parent_id]['left_id'] != $left || $this->menu[$parent_id]['right_id'] != $right) {
				$this->sql->query("UPDATE ".$this->conf->PAGE." SET left_id = '$left', right_id = '$right' WHERE page_id = '$parent_id'");
			}
			return $right + 1;     
		}     
		
		function get_menu($level = 0,$parent_id = false)
		{
			$get = Registry::get('get');
			
			$html = "<div id='menu'><ul>"; 
				
			if ($level) {
				
				$menu = $this->get_tier($level,$parent_id); 
				
				if ($menu) {
					foreach ($menu as $k=>$row)
					{
						if ($row['status'] || $this->status) {
							if (!$this->is_menu || $row['menu']) {
								$here = $get->a == $row['alias'] ? "class='active alias-$row[alias]'" : "class='alias-$row[alias]'";
								
								$html .= "<li><a $here href='".$this->conf->http."/".($row['full_alias'] ? $row['full_alias'] : $row['alias'])."'>".($row['menu_title'] ? $row['menu_title'] : $row['name'])."</a></li>";
							}
						}
					}
				}
			} else {	
				$this->menu_length = count($this->menu);
				
				$index = 0;
				while ($index < $this->menu_length)
				{
					$html .= $this->build_menu($index);
				}
				
			}
			
			$html .= '</ul></div>';

			return $html;
		}
		
		function has_children($index)
		{
			$parent = $this->menu[$index];

			$count = ($parent['right_id'] - $parent['left_id'] - 1);

			$children = array();
			
			if ($count) {
				$children = $this->get_tier($parent['counter'] + 1, $parent['page_id']);
			}
			
			return count($children);
		}
		
		function build_menu(& $index)
		{
			$get = Registry::get('get');
		
			do {
				$curr = $this->menu[$index];
				$next = $this->menu[++$index];
				if (!$this->is_menu || $curr['menu']) {
					if (($curr['status'] || $this->status) && $curr) {
						$url = $curr['url'] ? $curr['url'] : $this->conf->http.'/'.($curr['full_alias'] ? $curr['full_alias'] : $curr['alias']);
						$popup = $curr['url'] ? "target='_blank'" : '';
						$here = $get->a == $curr['alias'] ? "class='active alias-$curr[alias]'" : "class='alias-$curr[alias]'";
						$li_class = 'level-'.$curr['counter'];
						
						$html .= "<li class='$li_class'><a href='$url' $popup $here>".($curr['menu_title'] ? $curr['menu_title'] : $curr['name'])."</a>";
					
						if ($this->has_children($index-1)) {
							$ul_class = 'level-'.$next['counter'];
							$html .= "<ul class='$ul_class'>".$this->build_menu($index).'</ul>';
						}
					
						$html .= '</li>';
					} else  {
						$index += ($curr['right_id'] - $curr['left_id'] - 1) / 2 ;
					}
				}
			} while ($next && $curr['counter'] <= $next['counter']);
			
			return $html;
		}
		
		function get_tier($level, $parent_id = 0)
		{
			foreach($this->menu as $id=>$menu)
			{
				if  (($menu['counter'] + 1 == $level && !$parent_id) || $parent_id == $menu['parent_id']) {
					if (!$this->is_menu || $menu['menu']) {
						if ($menu['status'] || $this->status){
							$ret[$id] = $menu;
						}
					}
				}
			}
			
			return $ret;
		}
	}
?>