<?php
	class Get
	{
		function __construct($param)
		{
			$this->params = $param;
		}
		
		function arg($pos)
		{
			return $this->params[$pos];
		}
		
		function all()
		{
			return implode('/', $this->params);
		}
		
		function up($distance = 1)
		{
			return implode('/', array_splice($this->params, 0, -1*$distance));	
		}
	}
?>