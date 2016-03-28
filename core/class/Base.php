<?php
	class Base
	{
		function __construct()
		{	
			$this->sql = Registry::get('sql');
			$this->conf = Registry::get('conf');
			$this->get = Registry::get('get');
			$this->setting = Registry::get('setting');
		}
	}
?>