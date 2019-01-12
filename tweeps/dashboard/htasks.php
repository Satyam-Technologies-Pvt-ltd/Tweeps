<?php
//start session
session_start();

// Include config file and twitter PHP Library by Abraham Williams (abraham@abrah.am)
include_once("config.php");
include_once("includes/twitteroauth.php");
include_once("includes/functions.php");
include_once("includes/pagination.php");
$conn = dbcon();
?><?php
	if(isset($_SESSION['status']) && $_SESSION['status'] == 'verified') 
	{
		//Retrive variables
		$screen_name 		= $_SESSION['request_vars']['screen_name'];
		$twitter_id			= $_SESSION['request_vars']['user_id'];
		$oauth_token 		= $_SESSION['request_vars']['oauth_token'];
		$oauth_token_secret = $_SESSION['request_vars']['oauth_token_secret'];
	
		//Show welcome message
	//	echo '<div class="welcome_txt">Welcome <strong>'.$screen_name.'</strong> <a href="logout.php?logout">Logout</a>!</div>';
	//	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);
	
$quser = "select id from users where oauth_uid = {$twitter_id} limit 1";	
$res1 = mysqli_query($conn,$quser) or die(mysqli_error($conn));

 $uid = mysqli_result($res1,0,id);	
		
$feed_id = isset($_GET['hid'])?(int)$_GET['hid']:0;

	if(isset($_POST['delhistory'])&& $feed_id!=0){
		$hid = (int)$_GET['did'];
		$q1 = "delete from history where feed_id =".$feed_id;
		$r1 = mysqli_query($conn,$q1) or die(mysqli_error($conn));
	
	}
	else if(isset($_POST['delhistory'])&& $feed_id==0){
		$q1 = "DELETE w FROM history w INNER JOIN feeds e   ON w.feed_id = e.id Where e.user_id = {$uid} ";	

		$r1 = mysqli_query($conn,$q1) or die(mysqli_error($conn));		
	}
	

 if(isset($feed_id) && $feed_id!=0){
 $sql = "SELECT * FROM history WHERE feed_id = ".$feed_id. " order by time desc";
}
else if($feed_id==0){
 $sql = "SELECT * FROM history INNER JOIN feeds on history.feed_id = feeds.id where feeds.user_id = {$uid} order by history.time desc ";	
}

$result = mysqli_query($conn,$sql) or die (mysqli_error($conn));

$psa = new Pagination($conn, $sql, 10,  5);

$result =  $psa->paginate();
		
?>	<!DOCTYPE html>
<html lang="en-us">
	<head>
		<title>Satyam Technologies - Drip Feed</title>
		<link rel="stylesheet" type="text/css" href="adminstyles.css" />
		<link type="shortcut icon" rel="icon" href="images/favicon_image.png"/>	
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="js/drip.js"></script>		
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<div id="logo"><a href="http://sixthlife.net/tweeps/"><img src="../bootstrap/img/tweeps-curly.png" /></a></div>

			<div id="login_detail">
					<div class="login_logout"><a href="logout.php?logout"><img src="images/logout.png" /></a></div>
					<div class="login_username">Welcome <?php echo $screen_name; ?></div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
			<div id="inner_content">
				<div class="top_menus">
					<ul class="admin_menu">
						<li class="right_bordered"><a href="index.php" class="selected">Add Feed</a></li>
						<li class="right_bordered"><a href="tasks.php">Feeds</a></li>
						<li class="right_bordered"><a href="htasks.php">Histroy</a></li>		
						<li class="right_bordered"><a href="account.php">Account</a></li>
						<div class="clear"></div>
					</ul>
				</div>
					<div class="page_heading">History </div>
			<!--
	<div class="notification"><span>Info :</span> Category added Successfully.</div>
				<div class="notification_green"><span>Error :</span> Category added Successfully.</div>
-->

<div>
<form action="" method="POST" ><input type="submit" name="delhistory" style="float:right; margin:10px; width:100px; " class="delbtn" value="Delete History" /></form></div>	<div class="clear"></div>

<div class="pagination">
<?php

echo $psa->renderFullNav();
?>
</div>
<div class="clear"></div>
			
					<div class="adding_border even_tr_top table_headings bolded_text">					
						<div class="product1_h">Sr </div>
						<div class="product2_h">Title</div>
						<div class="product3_h">Message</div>
						<div class="product4_h">Published</div>
						<div class="product5_h">Status</div>
						<div class="product6_h">Log</div>
						<div class="product7_h">Image</div>
						<div class="product8_h">Page</div>
						<div class="clear"></div>
						</div>



<?php
$i = (isset($_GET['page']) && $_GET['page']!=='' && is_numeric($_GET['page']))?( $_GET['page']* 10+1): 1;
while($row = mysqli_fetch_assoc($result)){
//	print_r($row);
	
	echo '<div class="table_headings" style="background:white;"><div class="product1">'.$i.'</div>';
	echo '<div class="product2">'.$row['title'].'</div>';	
	echo '<div class="product3">'.$row['status_message'].'</div>';
	echo '<div class="product4">'.date('h:m A, l - d M Y', strtotime($row['time'])).'</div>';	
	echo '<div class="product5">'.$row['log_status'].'</div>';	
	if($row['status']=='success'){
	echo '<div class="product6"><a href="'.$row['log'].'" target="_blank" >'.$row['log'].'</a></div>';
	}
	else{
	echo '<div class="product5">'.$row['log'].'</div>';		
	}	
	echo '<div class="product7"><a href="'.$row['image_url'].'" target="_blank" >'.$row['image_url'].'</a></div>';												
	echo '<div class="product8"><a href="'.$row['feedurl'].'" target="_blank" >'.$row['feedurl'].'</a></div><div class="clear"></div><div>';
	
	$i++;	
}

?>


<div class="pagination">
<?php

echo $psa->renderFullNav();
?>
</div>

</div>


</div>




</div>



	</div><div class="clear">

	<!--
	<div id="full_footer">
			<div id="inner_footer">
				<p>Created by Satyam Technologies &copy; 2016 </p>
			</div>
		</div>
-->
		
		
	</body>
</html>
<?php			
	}else{
		//Display login button
	//	echo '<a href="process.php">Login</a>';
		header('location: ../index.php');
	}
?>