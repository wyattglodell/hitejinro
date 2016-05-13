<?php
	class Func extends BaseFunc
	{
		function admin_menu_ignore()
		{
			$ignore = array(); # ex: $ignore[] = 'page';
						
			return $ignore;
		}
		
		function admin_menu_icons()
		{
			$icons = array (
				'administrator' => 'icon-user7',
				'crud' => 'icon-tools',
				'settings' => 'icon-cog3',
				'menu' => 'icon-menu',
				'log' => 'icon-file',
				'menu' => 'icon-tree5',
				'users' => 'icon-user-plus2',
				'icons' => 'icon-lab',
				'user_roles' => 'icon-users2',
				'webforms' => 'icon-insert-template',
				'delete_archive' => 'icon-remove2',
				'pages' => 'icon-stack-empty'
			);
			
			return $icons;
		}
	}
?>