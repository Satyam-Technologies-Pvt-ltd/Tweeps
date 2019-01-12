<?php

date_default_timezone_set('utc') ;


function dbcon(){
	include_once('config.php');
$conn = mysqli_connect(DB_HOST, DB_USER, DB_USERPASS,DB_NAME);
return $conn;
}

function mysqli_result($res, $row, $field=0) {
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
}

function get_web_page( $url )
{ //echo $url;
//print_r($url);
if($url=='' || is_array($url)){return false; }
	if(function_exists('curl_version')){
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
      //  CURLOPT_USERAGENT      => "Satyam.Tech App", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch ); //echo $content;
	return $content;
	}
	else if(function_exists('file_get_contents') && ini_get('allow_url_fopen')==TRUE){
	$content = @file_get_contents($url);	
	return $content;	
	}

		return false;
	}
  
  function tweetnow($connection, $status_message, $imageurl){

if($imageurl!=''){
$status = $connection->upload('statuses/update_with_media', array('status' => $status_message, 'media[]' => get_web_page($imageurl)));
	
}
else{
	$status = $connection->post('statuses/update', array('status' => $status_message));	
}

	if(isset($status->id)){
	 return '<a href="https://twitter.com/'.$user[0]['username'].'/status/'.$status->id.'" >https://twitter.com/'.
	 $user[0]['username'].'/status/'.$status->id.'</a>';
}
else{
	print_r($status);
}
  	
  }
  
  

function array_iunique($array) {
//	print_r($array); exit;
    return array_intersect_key(
        $array,
        array_unique(array_map("StrToLower",$array))
    );
}

function recursiveFind(array $array, $needle) {
  $iterator = new RecursiveArrayIterator($array);
  $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
  $return = array();
  foreach ($recursive as $key => $value) {
    if ($key === $needle) { 
      $return[] = $value;
    }
  } 
  return $return;
}

    function arraycount($array, $value){
    $counter = 0;
    foreach($array as $thisvalue) /*go through every value in the array*/
     {
           if($thisvalue === $value){ /*if this one value of the array is equal to the value we are checking*/
           $counter++; /*increase the count by 1*/
           }
     }
     return $counter;
     }
function removeautopause24(){
	include_once('config.php');
	$conn=	dbcon();
	$qdel = "delete from history where  TIMESTAMPDIFF(MINUTE,  `time`, NOW()) >= 1440 ";
	$resdel = mysqli_query($conn,$qdel) or die(mysqli_error($conn));
	
		$qust = "update feeds set status = 'Active', autopaused = 'n'  where autopaused = 'y' 
		and status = 'Paused' and TIMESTAMPDIFF(MINUTE,  `current_tweet`, NOW()) >= 1440" ;
	$rust = mysqli_query($conn,$qust) or die(mysqli_error($conn));
}
class Users {
	public $tableName = 'users';
	
	function __construct(){
		include_once('config.php');
		//Database configuration
		$dbServer = DB_HOST; //Define database server host
		$dbUsername = DB_USER; //Define database username
		$dbPassword = DB_USERPASS; //Define database password
		$dbName = DB_NAME; //Define database name
		
		//Connect databse
		$con = mysqli_connect($dbServer,$dbUsername,$dbPassword,$dbName);
		if(mysqli_error($con)){
			die("Failed to connect with MySQL: ".mysqli_error($con));
		}else{
			$this->connect = $con;
		}
	}
	
	function checkUser($oauth_provider,$oauth_uid,$username,$fname,$lname,$locale,$oauth_token,$oauth_secret,$profile_image_url){
		$prevQuery = mysqli_query($this->connect,"SELECT * FROM $this->tableName WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$oauth_uid."'") or die(mysqli_error($this->connect));
		if(mysqli_num_rows($prevQuery) > 0){
			$update = mysqli_query($this->connect,"UPDATE $this->tableName SET oauth_token = '".$oauth_token."', oauth_secret = '".$oauth_secret."', modified = '".date("Y-m-d H:i:s")."' WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$oauth_uid."'") or die(mysqli_error($this->connect));
		}else{
			$insert = mysqli_query($this->connect,"INSERT INTO $this->tableName SET oauth_provider = '".$oauth_provider."', oauth_uid = '".$oauth_uid."', username = '".$username."', fname = '".$fname."', lname = '".$lname."', locale = '".$locale."', oauth_token = '".$oauth_token."', oauth_secret = '".$oauth_secret."', picture = '".$profile_image_url."', created = '".date("Y-m-d H:i:s")."', modified = '".date("Y-m-d H:i:s")."', account_status='Active'") or die(mysqli_error($this->connect));
		}
		
		$query = mysqli_query($this->connect,"SELECT * FROM $this->tableName WHERE oauth_provider = '".$oauth_provider."' AND oauth_uid = '".$oauth_uid."'") or die(mysqli__error($this->connect));
		$result = mysqli_fetch_array($query);
		return $result;
	}
}
?>