<?php
	class Session extends Base
	{
		public function __construct()
		{
			parent::__construct();
			
			session_set_save_handler(
			  array($this, "_open"),
			  array($this, "_close"),
			  array($this, "_read"),
			  array($this, "_write"),
			  array($this, "_destroy"),
			  array($this, "_gc")
			);	
			
			session_start();
		}
					
		public function _open()
		{
			return true;
		}		
		
		public function _close() 
		{
			return true;	
		}
		
		public function _read($id) 
		{
		  $this->sql->query("SELECT data FROM ".$this->conf->SESSION." WHERE session_id = '$id'");
		  $data = $this->sql->fetch();
		  
		  return (string) $data['data'];
		}
		
		public function _write($id, $data) 
		{
  			$this->sql->query("REPLACE INTO ".$this->conf->SESSION." VALUES ('$id', '".time()."', '".$this->sql->sanitize($data)."')");
			return true;
		}
		
		public function _destroy($id)
		{
			$this->sql->query("DELETE FROM ".$this->conf->SESSION." WHERE session_id = '$id'");
			return true;
		}
		
		public function _gc($max)
		{
			$old = time() - $max;
			$this->sql->query("DELETE FROM ".$this->conf->SESSION." WHERE access < '$old'");
			return true;	
		}
	}
?>