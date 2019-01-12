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
		
	if(isset($_GET['did'])){
		$did = (int)$_GET['did'];
		$q1 = "delete from history where feed_id =".$did;
		$r1 = mysqli_query($conn,$q1) or die(mysqli_error($conn));
		$q2 = "delete from feeds where id =".$did;
		$r2 = mysqli_query($conn,$q2) or die(mysqli_error($conn));
		$_GET['did']='';
		unset($_GET['did'])	;	
	}
	

	if(isset($_GET['sid'])){

		$sid = (int)$_GET['sid'];
		
		$oldq = "select status from feeds where id =".$sid." limit 1";
		$resq = mysqli_query($conn,$oldq) or die(mysqli_error($conn))	;
		
		$oldstatus = mysqli_result($resq, 0, 'status');
		
		if($oldstatus=='Active'){$status = 'Paused';} else{ $status = 'Active';};
		//	echo $status;
		$q2 = "update feeds set status = '{$status}' where id =".$sid;
		$r2 = mysqli_query($conn,$q2) or die(mysqli_error($conn));	
				$_GET['sid']='';
		unset($_GET['sid'])	;	
	}
	
	$account_status='';
	
	$quser = "select id, account_status from users where  oauth_uid='{$twitter_id}' ";
	
	$ruser = mysqli_query($conn,$quser) or die(mysqli_error($conn));

		
	$users = array();
	$account_status='';
	while($row = mysqli_fetch_array($ruser)){
		$users[] = $row['id'];
		if(mysqli_num_rows($ruser)==1){
		$account_status = 	$row['account_status'];
		}
		if(isset($_POST['allactive'])){
			$status = 'Active';
		$q21 = "update feeds set status = '{$status}' where user_id =".$row['id']." and autopaused = 'y'";
		$r21 = mysqli_query($conn,$q21) or die(mysqli_error($conn));		
		
		$delhis = "delete from history where user_id =".$row['id'];	
		$rdelhis = mysqli_query($conn,$delhis) or die(mysqli_error($conn));
				}
		else if(isset($_POST['allpaused'])){
			$status = 'Paused';
		$q21 = "update feeds set status = '{$status}' , autopaused = 'y' where user_id =".$row['id']." and  autopaused = 'y'";
		$r21 = mysqli_query($conn,$q21) or die(mysqli_error($conn));			
		}		
	}
	
	$ids = join(',',$users);  
	$sql = "SELECT * FROM feeds WHERE user_id IN ($ids)"; //exit;
	
	$sqlc = "SELECT id FROM feeds WHERE user_id IN ($ids) and status = 'Active'";
	

$searchstr = '';
if(isset($_REQUEST['tsearchbox']) && $_REQUEST['tsearchbox']!='' ){
	$searchstr = mysqli_escape_string($conn, $_REQUEST['tsearchbox']);
	
	$sql = "SELECT * FROM feeds WHERE user_id IN ($ids) and (name like '%{$searchstr}%' or url like '%{$searchstr}%'  ) ";	
	$sqlc = "SELECT id FROM feeds WHERE user_id IN ($ids)  and status='Active' and (name like '%{$searchstr}%' or url like '%{$searchstr}%'  ) ";	
}

$result = mysqli_query($conn,$sql) or die (mysqli_error($conn));
$resc =  mysqli_query($conn,$sqlc) or die (mysqli_error($conn));

$totalfeeds = mysqli_num_rows($result);
$activefeeds = mysqli_num_rows($resc);


$ps = new Pagination($conn, $sql, 10,  5);
$result =  $ps->paginate();
		
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
						<li class="right_bordered"><a href="htasks.php">History</a></li>
						<li class="right_bordered"><a href="account.php">Account</a></li>
						<div class="clear"></div>
					</ul>
				</div>
					<div class="page_heading">All Feeds </div>
					
					 
					    
					    <?php
					    if($account_status=='Inactive'){
					    	echo '<div class="notification"><span>Error :</span> Your Account is not Active.  </div>';
					    }
					    ?>
              
		
	<!--	<div class="notification"><span>Info :</span> Category added Successfully.</div>
				<div class="notification_green"><span>Error :</span> Category added Successfully.</div>-->

<div>

<form action="" method="POST" ><input type="submit" name="allactive" style="float:right; margin:10px; width:100px;" class="delbtn" value="Activate All" /></form><form action="" method="POST" ><input type="submit" name="allpaused" style="float:right; margin:10px; width:100px; " class="delbtn" value="Pause All" /></form></div>		<div class="clear"></div>
<div id="searchthhis"><form action="" method="POST"><input type="text" class="cat_fld_txt" name="tsearchbox"/><input type="submit" name="submitsearch" value="Search" class="cat_fld_addBTN" /></form></div>
<div id="feedcts">
<strong>Total Feeds <?php echo $totalfeeds; ?></strong>&nbsp;&nbsp;<strong>Active Feeds <?php echo $activefeeds; ?></strong>&nbsp;&nbsp;<strong>Paused Feeds <?php echo $totalfeeds- $activefeeds; ?></strong>&nbsp;&nbsp;
</div>

<div class="pagination">

<?php

echo $ps->renderFullNav();
?>
</div>
	<div class="clear"></div>

				<div class="tabling">
					<div class="adding_border even_tr_top table_headings bolded_text">					
						<div class="product1_h">Sr </div>
						<div class="product2_h">Name</div>
						<div class="product3_h">Last Tweet</div>
						<div class="product4_h">Page</div>
						<div class="product5_h">Interval</div>
						<div class="product6_h">Status</div>
						<div class="product7_h">History</div>
						<div class="product8_h">Action</div>
						<div class="clear"></div>
						</div>



<?php
$i = (isset($_GET['page']) && $_GET['page']!=='' && is_numeric($_GET['page']))?( $_GET['page']* 10+1): 1;
while($row = mysqli_fetch_assoc($result)){
//	print_r($row);
	
	echo '<div class="adding_border odd_tr table_headings"><div class="product1">'.$i.'</div>';
	echo '<div class="product2">'.$row['name'].'</div>';	
	echo '<div class="product3">'.date('h:m A, l - d M Y', strtotime($row['current_tweet'])).'</div>';
	echo '<div class="product4">'.$row['current_page'].' of '.$row['page_from'].' to '.$row['page_to'].'</div>';	
	echo '<div class="product5">'.$row['post_interval'].' min</div>';	
	if($row['autopaused']=='y' && $row['status']=='Paused'){
	echo '<div class="product6"><a href="tasks.php?sid='.$row['id'].'"><button class="delbtn" style="background:yellow;color:white;">Auto'.$row['status'].'</button></a></div>';		
	}
	else{
		if($row['status']=='Paused'){
	echo '<div class="product6"><a href="tasks.php?sid='.$row['id'].'"><button class="delbtn" style="background:red;color:white;">'.$row['status'].'</button></a></div>';			
		}
		else{
	echo '<div class="product6"><a href="tasks.php?sid='.$row['id'].'"><button class="delbtn" ">'.$row['status'].'</button></a></div>';			
		}
		
	}
	
	echo '<div class="product7"><a href="htasks.php?hid='.$row['id'].'">History</a></div>												<div class="product8">
	<div class="">
							<a href="etasks.php?eid='.$row['id'].'"><button class="delbtn">Edit</button></a>
							<a href="tasks.php?did='.$row['id'].'&page='.$_GET['page'].'&tsearchbox='.$searchstr.'" ><button class="delbtn">delete</button></a>							</div>
						</div><div class="clear"></div><div>';
	
	$i++;	
}

?>

<div class="pagination">
<?php

echo $ps->renderFullNav();
?>
</div>

</div>


</div>


</div>

<p>&nbsp;</p><p>&nbsp;</p>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

	</div>
	
	
	
	<div class="clear"></div>
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