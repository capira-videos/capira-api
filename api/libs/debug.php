<?php

if(!defined('VALID_INCLUDE')) {
	exit;
}

function str_replace_first($search, $replace, $subject) {
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

class debug_mysqli {
	private $closed = false;
	public $errno;
	public $error;

	public function prepare($sql) {
		return new debug_stmt($sql);
	}
	
	public function __destruct() {
		if(!$this->closed) {
			echo 'Mysqli Object not closed!<br />';
			//debug_print_backtrace();
		}
	}
	
	public function close() {
		$this->closed = true;
	}	
	
}

class debug_stmt {
	public $sql;
	private $fetched;
	private $closed = false;
	
	public function __construct($sql) {
		$this->fetched = rand(0,1) == 1;
		$this->sql = $sql;
	}
	
	public function __destruct() {
		if(!$this->closed) {
			echo 'Stmt Object not closed!<br />';
			//debug_print_backtrace();
		}
	}
	
	public function bind_param($a, $b=false, $c=false, $d=false, $e=false, $f=false, $g=false) {
		$sql = str_replace_first('?', $b, $this->sql);
		$sql = str_replace_first('?', $c, $sql);
		$sql = str_replace_first('?', $d, $sql);
		$sql = str_replace_first('?', $e, $sql);
		$sql = str_replace_first('?', $f, $sql);
		$sql = str_replace_first('?', $g, $sql);
		echo $sql . '<br />';
		return true;
	}
	
	public function execute() {
		return true;
	}
	
	public function bind_result($a, $b=false, $c=false) {
		return true;
	}
	
	public function fetch() {
		if($this->fetched) return false;
		$this->fetched = true;
		return true;
	}
	
		
	public $insert_id = 42;
	public $errno;
	public $error;
	
	public function close() {
		$this->closed = true;
	}
}

$mysqli = new debug_mysqli();

?>