<?php
class MySQL
{
	protected $host;
	protected $un;
	protected $pw;
	protected $db;
	protected $sqlconn;
	protected $dbconn;
	protected $rs;
	protected $qry;
	protected $errno;
	protected $display_error = true;
	protected $env = 'dev';
	
	function __construct($host, $un, $pw, $db)
	{
		$this->host = $host;
		$this->un = $un;
		$this->pw = $pw;
		$this->db = $db;	
		
		$this->magic_quotes = get_magic_quotes_gpc();
		
	}
	
	function connect()
	{
		$this->sqlconn = mysql_connect($this->host, $this->un, $this->pw);
		if ($this->sqlconn) {
			$this->dbconn = mysql_select_db($this->db, $this->sqlconn);
			if (!$this->dbconn) {
				$this->error();
			}
		} else {
			die ("Cannot connect to the MySQL Database Server");
		}
	}
	
	function show_query()
	{
		$this->show_query = true;
	}
	
	function query($qry, $show_error = true)
	{
		if ($this->show_query) {
			echo '<br><br>'; die($qry);
		}
		
		$this->display_error = $show_error;
		
		$this->qry = $qry;
		$this->rs = mysql_query($qry, $this->sqlconn) or $this->errno = $this->error(); # store the resource in case you plan to use it sequentially
		return $this->rs;
	}
	
	function errno()
	{
		return $this->errno;
	}
	
	function data_seek($pos, $rs = '')
	{
		if (!$rs) $rs = $this->rs;
		if (!$rs) $this->error();
		else {
			mysql_data_seek($rs, $pos);
		}
	}
	
	function fetch($rs = '')
	{
		if (!$rs) $rs = $this->rs;
		if (!$rs) $this->error();
		if ($rs) {
			return mysql_fetch_assoc($rs);
		}
	}	
	
	function num_rows($rs = '')
	{
		if (!$rs) $rs = $this->rs;
		if (!$rs) $this->error();
		else {
			return mysql_num_rows($rs);
		}
	}
	
	function result($row = 0, $rs = '',  $field = 0)
	{
		if (!$rs) $rs = $this->rs;
		if (!$rs) $this->error();
		else {
			return mysql_result($rs, $field, $row);
		}
	}
	
	function & fetch_all($rs = '')
	{
		if (!$rs) $rs = $this->rs;
		if (!$rs) $this->error();
		else {
			$r = array();
			while ($row = mysql_fetch_assoc($rs))
			{
				$r[] = $this->sanitize($row, 0);
			}
			
			return $r;
		}
	}
	
	function insert_id()
	{
		if (!$this->rs) $this->error();
		else {
			return mysql_insert_id($this->sqlconn);
		}
	}

	function set_env($state)
	{
		$this->env = $state;
	}

	function error()
	{
		if ($this->display_error) {
			if ($this->env) {
				die("<h3>An error has occured:</h3><ul style='list-style: none;'><li style='margin-bottom: 10px;'><strong>Number</strong> - ".mysql_errno($this->sqlconn)."</li><li style='margin-bottom: 10px;'><strong>Message</strong> - ".mysql_error($this->sqlconn)."</li><li style='margin-bottom: 10px;'><strong>Query</strong> - ".$this->qry."</li></ul>");
			} else {
				$this->conf = Registry::get('conf');
				$this->func = Registry::get('func');
				$debug = debug_backtrace();
				
				$error_string = "File: ".$debug[1]['file']."\n\nLine: ".$debug[1]['line']."\n\nQuery: ".$this->qry."\n\nError Number: ".mysql_errno($this->sqlconn)."\n\nError: ".mysql_error($this->sqlconn);
				
				error_log(date($this->conf->php_date)." -  File: ".$debug[1]['file']." => Line: ".$debug[1]['line']."\n", 3, $this->conf->log_file);
 				$this->func->email($this->conf->dev_email, 'An Error occurred at '.$_SERVER['HTTP_HOST'], $error_string);
				die ("<p style='text-align: center; font-weight: bold;'>Sorry a fatal error has occurred, I have notified the site administrator with relevant information to get this resolved.</p>");
			}
		} else {
			return mysql_errno($this->sqlconn);
		}
	}
	
	function sanitize($data, $mode = 0x0001 ) { // bitwise operator, 0 = STRIP, 1 = ESCAPE, 2 = ENTITY, 4 = DECODE, 8 = STRIPSLASHES
		if (is_array($data)) { foreach ($data as $k => $v) $r[$k] = $this->sanitize($v, $mode); } 
		else { 
			$r = $this->magic_quotes ? stripslashes(trim($data)) : trim($data);
			
			if ($mode & STRIP) { $r = strip_tags($r); }
			
			if ($mode & ESCAPE) {
				if (get_magic_quotes_gpc()) {
					$r = mysql_real_escape_string(stripslashes($r), $this->sqlconn); 
				} else {
					$r = mysql_real_escape_string($r, $this->sqlconn); 
				}
			}
			
			if ($mode & ENTITY) { $r = htmlspecialchars($r, ENT_QUOTES); }
			if ($mode & DECODE) $r = html_entity_decode($r);
			
			if ($mode & STRIPSLASHES) { $r = stripslashes($r); }
		}
		return $r;
	}
	
	function insert($tbl, $data, $fields = array())
	{
		if ($data) {
			$query = "INSERT INTO $tbl ";
			
			if (count($data) == count($data, true)) {
				$temp = $data;
				$data = array();
				$data[0] = $temp;
			}
			
			if (!$fields) {
				$fields = array_keys($data[0]);
			} 
			
			$query .= ' (`'.implode('`,`', $fields).'`) VALUES ';
						
			foreach ($data as $k=>$row)
			{
				$values = array();
				
				foreach ($fields as $v)
				{
					$values[] = $row[$v];
				}

				$queries[] = "('".implode("','", $values)."')";
			}
			
			$this->query($query.implode(',', $queries));
			
			return $this->insert_id();
		}
	}	
	
	function update($tbl, $data, $fields = array(), $pk, $id)
	{
		if ($data) {
			
			if (!$fields) {
				$fields = array_keys($data);
			} 
					
			foreach ($fields as $v)
			{
				$field[] = "`$v` = '".$data[$v]."'";
			}
			
			$this->query("UPDATE $tbl SET ".implode(',',$field)." WHERE $pk = $id");
			
			return true;
		}
	}
	
	function check_duplicate($tbl, $field, $value)
	{
		$field = preg_replace('[^a-zA-Z0-9_]', '', $field);
		$value = $this->sanitize($value);
	
		$this->query("SELECT COUNT(*) FROM $tbl WHERE $field = '$value'");
		return $this->result(0);
	}
}
?>