<?php
// class MySQL
class MySQL{
	var $conId; // connection identifier
	var $host; // MySQL host
	var $user; // MySQL username
	var $password; // MySQL password
	var $database; // MySQL database
	var $result; // MySQL result set
	// constructor
	function MySQL($options=array()){
		// validate incoming parameters
		if(count($options)<1){
            trigger_error('No connection parameters were provided');
            exit();
        }
		foreach($options as $parameter=>$value){
            if(!$parameter||!$value){
                trigger_error('Invalid connection parameter');
                exit();
            }
            $this->{$parameter}=$value;
		}
		// connect to MySQL
		$this->connectDB();
	}
	// connect to MYSQL server and select database
	function connectDB(){
		if(!$this->conId=mysql_connect($this->host,$this->user,$this->password)){
            trigger_error('Error connecting to the server '.mysql_error());
            exit();
		}
		if(!mysql_select_db($this->database,$this->conId)){
			 trigger_error('Error selecting database '.mysql_error());
			 exit();
		}
	}
	// perform query
	function query($query){
		if(!$this->result=mysql_query($query,$this->conId)){
			trigger_error('Error performing query '.$query.' '.mysql_error());
			exit();
		}
	}
	// fetch row
	function fetchRow(){
		return mysql_fetch_array($this->result,MYSQL_ASSOC);
	}
	// count rows
	function countRows(){
		if(!$rows=mysql_num_rows($this->result)){
			trigger_error('Error counting rows');
			exit();
		}
		return $rows;
	}
	// count affected rows
	function countAffectedRows(){
		if(!$rows=mysql_affected_rows($this->conId)){
			trigger_error('Error counting affected rows');
			exit();
		}
		return $rows;
	}
	// get ID from last inserted row
	function getInsertID(){
		if(!$id=mysql_insert_id($this->conId)){
			trigger_error('Error getting ID');
			exit();
		}
		return $id;
	}
	// seek row
	function seekRow($row=0){
		if(!mysql_data_seek($this->result,$row)){
			trigger_error('Error seeking data');
			exit();
		}
	}
}
?>