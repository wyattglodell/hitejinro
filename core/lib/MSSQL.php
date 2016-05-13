<?php # NOT 100% DONE BUT SHOULD WORK
class MSSQL
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
	}
	
	function connect()
	{
		$this->sqlconn = odbc_connect($this->host, $this->un, $this->pw);
		if (!$this->sqlconn) {
			die ("Cannot connect to the SQL Server");
		}
	}
	
	function show_query()
	{
		$this->show_query = true;
	}
	
	function query($qry, $error = true)
	{
		if ($this->show_query) die($qry);
		
		if ($error) $this->display_error = true;
		else $this->display_error = false;
		
		$this->qry = $qry;
		$this->rs = odbc_exec($this->sqlconn, $qry) or $this->errno = $this->error(); # store the resource in case you plan to use it sequentially
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
	
	function odbc_fetch_assoc($rs)
	{
		if (odbc_fetch_row($rs)){
			$line=array("odbc_affected_rows"=>odbc_num_rows($rs));
			for($f=1;$f<=odbc_num_fields($rs);$f++){
				$fn=odbc_field_name($rs,$f);
				$fct=odbc_result($rs,$fn);
				$newline=array($fn => $fct);
				$line=array_merge($line,$newline);
				//echo $f.": ".$fn."=".$fct."<br>";
			}
			return $line;
		} else {
			return false;
		}
	} 	
	function fetch($rs = '')
	{
		if (!$rs) $rs = $this->rs;
		if (!$rs) $this->error();
		if ($rs) {
			return $this->sanitize($this->odbc_fetch_assoc($rs), STRIP);
		}
	}	
	
	function num_rows($rs = '')
	{
		if (!$rs) $rs = $this->rs;
		if (!$rs) $this->error();
		else {
			return odbc_num_rows($rs);
		}
	}
	
	function result($row = 0, $rs = '',  $field = 0)
	{
		if (!$rs) $rs = $this->rs;
		if (!$rs) $this->error();
		else {
			return odbc_result($rs, $row+1);
		}
	}
	
	function & fetch_all($rs = '')
	{
		if (!$rs) $rs = $this->rs;
		if (!$rs) $this->error();
		else {
			$r = array();
			while ($row = $this->odbc_fetch_assoc($rs))
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
			echo odbc_cursor($this->rs); die; #mysql_insert_id($this->sqlconn);
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
				die("<h3>An error has occured:</h3><ul style='list-style: none;'><li style='margin-bottom: 10px;'><strong>Message</strong> - ".odbc_errormsg($this->sqlconn)."</li><li style='margin-bottom: 10px;'><strong>Query</strong> - ".$this->qry."</li></ul>");
			} else {
				$this->conf = Registry::get('conf');
				$this->func = Registry::get('func');
				$debug = debug_backtrace();
				
				$error_string = "File: ".$debug[1]['file']."\n\nLine: ".$debug[1]['line']."\n\nQuery: ".$this->qry."\n\nError: ".odbc_errormsg($this->sqlconn);
				
				error_log(date($this->conf->php_date)." -  File: ".$debug[1]['file']." => Line: ".$debug[1]['line']."\n", 3, $this->conf->log_file);
				$this->func->email($this->conf->dev_email, 'An Error occurred at '.$_SERVER['HTTP_HOST'], $error_string);
				die ("<p style='text-align: center; font-weight: bold;'>Sorry a fatal error has occurred, I have notified the site administrator with relevant information to get this resolved.</p>");
			}
		} else {
			return odbc_error($this->sqlconn);
		}
	}
	
	function sanitize($data, $mode = 0x0001 ) { // bitwise operator, 0 = strip, 1 = escape, 2 = entity, 4 = entity decode
		if (is_array($data)) { foreach ($data as $k => $v) $r[$k] = $this->sanitize($v, $mode); } 
		else { 
			$r = trim($data);
			if ($mode & 0x0000) $r = stripslashes($data);
			if ($mode & 0x0001) $r = str_replace("'","''", $data); 
			if ($mode & 0x0010) $r = htmlentities(trim($r), ENT_QUOTES);
			if ($mode & 0x0100) $r = html_entity_decode($r);
		}
		return $r;
	}
	
	function insert($tbl, $data, $fields = array())
	{
		if ($data) {
			$query = "INSERT INTO $tbl ";
			
			if (!$fields) {
				$fields = array_keys($data);
			} 
			
			foreach ($fields as $v)
			{
				$values[] = $data[$v];
			}
			
			$query .= "(".implode(',', $fields).") VALUES ('".implode("','", $values)."')";
			
			$this->query($query);
			
			return true;
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