<?php
	class Setting extends Base
	{
		function set_tbl($tbl)
		{
			$this->tbl = $tbl;
		}
		
		function load()
		{
			$this->sql->query("SELECT name, value FROM $this->tbl", true);
			while ($row = $this->sql->fetch())
			{
				$this->$row['name'] = $row['value'];
			}			
		}
		
		function update($name, $val)
		{
			$this->sql->query("UPDATE $this->tbl SET value = '".$this->sql->sanitize($val)."' WHERE name = '".$this->sql->sanitize($name)."'");	
		}
	}
?>