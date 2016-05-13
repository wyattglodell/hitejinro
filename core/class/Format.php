<?php
	class Format
	{
		function phone($value)
		{
			$value = preg_replace('~[^0-9]~', '', $value);	
		
			if(strlen($value) > 10) {
				$countryCode = substr($value, 0, strlen($value)-10);
				$areaCode = substr($value, -10, 3);
				$nextThree = substr($value, -7, 3);
				$lastFour = substr($value, -4, 4);
		
				$value = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
			}
			else if(strlen($value) == 10) {
				$areaCode = substr($value, 0, 3);
				$nextThree = substr($value, 3, 3);
				$lastFour = substr($value, 6, 4);
		
				$value = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
			}
			else if(strlen($value) == 7) {
				$nextThree = substr($value, 0, 3);
				$lastFour = substr($value, 3, 4);
		
				$value = $nextThree.'-'.$lastFour;
			}
		
			return $value;			
		}
	}
?>