<?php
	class Config
	{		
		function set($key, $value, $override = false) 
		{		
			if (!$this->$key || $override) {
				$this->$key = $value;
			}		
		}
	}
?>