<?php
	class Registry
	{
		private $bank;
		
		function load()
		{
			static $me;
			if (is_object($me)) return $me;
			$class = __CLASS__;
			$me = new $class;
			return $me;
		}
		
		function set($obj, $key='')
		{
			$key = $key ? $key : strtolower(get_class($obj));
			
			$self = self::load();
			$self->bank[$key] = $obj;	
		}
		
		function get($key)
		{
			$self = self::load();
			
			return $self->bank[$key];	
		}
		
		function retrieve_all()
		{
			$self = self::load();
			return $self->bank;	
		}
	}
?>