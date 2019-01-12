<?php
/**
 * PHPSense Pagination Class
 *
 * PHP tutorials and scripts
 *
 * @package		PHPSense
 * @author		Jatinder Singh Thind
 * @copyright	Copyright (c) 2006, Jatinder Singh Thind
 * @link		http://www.phpsense.com
 */
 
// ------------------------------------------------------------------------

class Pagination {
	var $php_self;
	var $rows_per_page; //Number of records to display per page
	var $total_rows; //Total number of rows returned by the query
	var $links_per_page; //Number of links to display per page
	var $sql;
	var $debug = false;
	var $conn;
	var $page;
	var $max_pages;
	var $offset;
	
	/**
	 * Constructor
	 *
	 * @param resource $connection Mysql connection link
	 * @param string $sql SQL query to paginate. Example : SELECT * FROM users
	 * @param integer $rows_per_page Number of records to display per page. Defaults to 10
	 * @param integer $links_per_page Number of links to display per page. Defaults to 5
	 */
	function __construct($connection, $sql, $rows_per_page = 10, $links_per_page = 5) {
		$this->conn = $connection;
		$this->sql = $sql;
		$this->rows_per_page = $rows_per_page;
		$this->links_per_page = $links_per_page;
		$this->php_self = htmlspecialchars($_SERVER['PHP_SELF']); //print_r($this->php_self);
		if(isset($_GET['page'])) {
			$this->page = intval($_GET['page']);
		}
	}

	 function cupage(){
	 	return $this->page;
	 }
	 
	 


	
	/**
	 * Executes the SQL query and initializes internal variables
	 *
	 * @access public
	 * @return resource
	 */
	function paginate() {
		if(!$this->conn) {
			if($this->debug) echo "MySQL connection missing<br />";
			return false;
		}
		
		$all_rs = mysqli_query($this->conn,$this->sql) ;
		if(!$all_rs) {
			if($this->debug) echo "SQL query failed. Check your query.<br />";
			return false;
		}
		$this->total_rows = mysqli_num_rows($all_rs);
	//	@mysqli_close($this->conn);
		
		$this->max_pages = ceil($this->total_rows/$this->rows_per_page);
		//Check the page value just in case someone is trying to input an aribitrary value
		if($this->page > $this->max_pages || $this->page <= 0) {
			$this->page = 1;
		}
		
		//Calculate Offset
		$this->offset = $this->rows_per_page * ($this->page-1);
		
		//Fetch the required result set
		$rs = mysqli_query($this->conn,$this->sql." LIMIT {$this->offset}, {$this->rows_per_page}") ;//print_r($rs);
		if(!$rs) {
			if($this->debug) echo "Pagination query failed. Check your query.<br />";
			return false;
		} 
		return $rs;
	}
	
	/**
	 * Display the link to the first page
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to 'First'
	 * @return string
	 */
	function renderFirst($tag='First') {
		if($this->page == 1) {
			return '<span class="first" >'. $tag.'</span>';
		}
		else {
			if($this->getpart()){
			return '<a href="'.$this->php_self.'?page=1'.$this->getpart().'">'.$tag.'</a>';
			}
		}
	}
	
	/**
	 * Display the link to the last page
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to 'Last'
	 * @return string
	 */
	function renderLast($tag='Last') {
		if($this->page == $this->max_pages) {
			return '<span class="last" >'. $tag. '</span>';
		}
		else {
			return '<a href="'.$this->php_self.'?page='.$this->max_pages.$this->getpart().'">'.$tag.'</a>';
		}
	}
	
	/**
	 * Display the next link
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to '>>'
	 * @return string
	 */
	function renderNext($tag=' &gt;&gt;') {
		if($this->page < $this->max_pages) {
			return '<a href="'.$this->php_self.'?page='.($this->page+1).$this->getpart().'">'.$tag.'</a>';
		}
		else {

			return '<span class="next" >'. $tag. '</span>';
		}
	}
	
	/**
	 * Display the previous link
	 *
	 * @access public
	 * @param string $tag Text string to be displayed as the link. Defaults to '<<'
	 * @return string
	 */
	function renderPrev($tag='&lt;&lt;') {
		if($this->page > 1) {
			return '<a href="'.$this->php_self.'?page='.($this->page-1).$this->getpart().'">'.$tag.'</a>';
		}
		else {
			return '<span class="previous" >'. $tag. '</span>';
		}
	}
	
	/**
	 * Display the page links
	 *
	 * @access public
	 * @return string
	 */
	function renderNav() {
		for($i=1;$i<=$this->max_pages;$i+=$this->links_per_page) {
			if($this->page >= $i) {
				$start = $i;
			}
		}
		
		if($this->max_pages > $this->links_per_page) {
			$end = $start+$this->links_per_page;
			if($end > $this->max_pages) $end = $this->max_pages+1;
		}
		else {
			$end = $this->max_pages;
		}
			
		$links = '';
		
		for( $i=$start ; $i<$end ; $i++) {
			if($i == $this->page) {
				$links .= '<span class="current" >'." $i ".'</span>';
			}
			else {
				$links .= ' <a href="'.$this->php_self.'?page='.$i.$this->getpart().'">'.$i.'</a> ';
			}
		}
		
		return $links;
	}
	
	/**
	 * Display full pagination navigation
	 *
	 * @access public
	 * @return string
	 */
	function renderFullNav() {
		return $this->renderFirst().'&nbsp;'.$this->renderPrev('<img src="images/arrow-left.png" width="10" height="10" />').'&nbsp;'.
		$this->renderNav().'&nbsp;'.$this->renderNext('<img src="images/arrow-right.png" width="10" height="10" />').'&nbsp;'.$this->renderLast();	
	}
	
	/**
	 * Set debug mode
	 *
	 * @access public
	 * @param bool $debug Set to TRUE to enable debug messages
	 * @return void
	 */
	function setDebug($debug) {
		$this->debug = $debug;
	}
	
function getpart(){
	$getpar = '';
if(isset($_REQUEST)){
	foreach($_REQUEST as $key=>$value){
		if($key!='page'){
	$getpar .= '&'.$key.'='.$value;
	}
	}
	}
	
	return $getpar;
}
	
	
}
?>