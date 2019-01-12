<?php
//start session
session_start();

// Include config file and twitter PHP Library by Abraham Williams (abraham@abrah.am)
include_once("config.php");
include_once("includes/twitteroauth.php");
include_once("includes/functions.php");
$conn =dbcon();
?><?php
	if(isset($_SESSION['status']) && $_SESSION['status'] == 'verified') 
	{
		//Retrive variables
		$screen_name 		= $_SESSION['request_vars']['screen_name'];
		$twitter_id			= $_SESSION['request_vars']['user_id'];
		$oauth_token 		= $_SESSION['request_vars']['oauth_token'];
		$oauth_token_secret = $_SESSION['request_vars']['oauth_token_secret'];
	
	
$quser = "select yourname, youremail, bitlyusername, bitlyapikey,account_status, id from users where oauth_uid = {$twitter_id} limit 1";	
$res1 = mysqli_query($conn,$quser) or die(mysqli_error($conn));

 $uid = mysqli_result($res1,0,'id');
 
 $oldyourname = 	mysqli_result($res1,0,'yourname');
 $oldyouremail = 	mysqli_result($res1,0,'youremail');
   $account_status = 	mysqli_result($res1,0,'account_status');
 $oldbitlyusername = 	mysqli_result($res1,0,'bitlyusername');
 $oldbitlyapikey = 	mysqli_result($res1,0,'bitlyapikey');
 		
if(isset($_POST['saveaccount'])){
	$yourname = mysqli_escape_string($conn,$_POST['yourname']);
	$youremail = mysqli_escape_string($conn,$_POST['youremail']);	
	$bitlyusername = mysqli_escape_string($conn,$_POST['bitlyusername']);
	$bitlyapikey = mysqli_escape_string($conn,$_POST['bitlyapikey']);
	
	$sql = "update users set yourname = '{$yourname}', youremail = '{$youremail}', bitlyusername = '{$bitlyusername}',
	bitlyapikey = '{$bitlyapikey}',bitlyapikey = '{$bitlyapikey}' ";
			
	$sql .=  " where id = {$uid} limit 1";
	
	mysqli_query($conn,$sql) or die(mysqli_error($conn));
}
if(isset($_POST['acctactive'])){	
	
	if($_POST['acctactive']=='Account Active'){
		$sqli = "update users set account_status = 'Inactive' where id = {$uid}";
	mysqli_query($conn,$sqli) or die(mysqli_error($conn));	
	$account_status = 'Inactive'	;
	}
	else {
		$sqla = "update users set account_status = 'Active' where id = {$uid}";
	mysqli_query($conn,$sqla) or die(mysqli_error($conn));	
		$account_status = 'Active'	;	
		
	}
	unset($_POST);
	}
		
		
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
					<div class="page_heading">Account </div>
			<!--
	<div class="notification"><span>Info :</span> Category added Successfully.</div>
				<div class="notification_green"><span>Error :</span> Category added Successfully.</div>
-->
<h2 style="font-size: 14px;">Profile</h2>
				<div class="adding_product">	
	<?php
$bgcolor = ($account_status=='Active')? 'green':'red';
?>
<form action="" method="POST" ><input type="submit" name="acctactive" style="float:right; margin:10px; width:120px; background:<?php echo $bgcolor; ?>; color: white; font-weight: bold;" class="delbtn" value="<?php
if($account_status=='Active')
{echo "Account Active";}else{
	echo "Account Inactive";
}	
?>" /> </form>

	<form method="post" action="account.php"><table  border="0" cellpadding="3" width="100%">
	

	<table>	
	
<tr><td>Name <img src="images/icon-help.png" width="15" height="15" id="feedname" /></td>
<td><input type="text" name="yourname" class="add_product_txtfld" id="tyourname"  size="50" value="<?php if(isset($oldyourname
)) {echo $oldyourname; } else{ echo $yourname; }?>"/>
<p class="message" id="pmyourname" ></p>

  </td>
</tr>	
	
<tr><td>Email <img src="images/icon-help.png" width="15" height="15" id="feedname" /> </td>
<td><input type="text" name="youremail" class="add_product_txtfld" id="tyouremail"  size="50" value="<?php if(isset($oldyouremail
)) {echo $oldyouremail; } else{ echo $youremail;} ?>"/>
<p class="message" id="pmyouremail" ></p>

  </td>
</tr>

	

				
<tr><td>Bit.ly Username <img src="images/icon-help.png" width="15" height="15" id="feedname" /></td>
<td><input type="text" name="bitlyusername" class="add_product_txtfld" id="tfeedname"  size="50" value="<?php if(isset(
$oldbitlyusername)) {echo $oldbitlyusername; }else{ echo $bitlyusername; } ?>"/>
<p class="message" id="pmbitlyusername" ></p>
<p class="helptext" id="pbitlyusername" >A Name for the feed for your reference only.</p>
  </td>
</tr>

<tr><td>Bit.ly API Key <img src="images/icon-help.png" width="15" height="15" id="rssurl" /> </td>
<td><input type="text" name="bitlyapikey" class="add_product_txtfld" size="50" value="<?php if(isset(
$oldbitlyapikey)) {echo $oldbitlyapikey; }else{ echo $bitlyapikey; } ?>" id=""/> Find your <a href="http://support.bitly.com/knowledgebase/articles/76785-how-do-i-find-my-api-key-" target="_new"> bit.ly API Key</a>
<p class="message" id="pmbitlyapikey" ></p>
<p class="helptext" id="pbitlyapikey" >To generate Bit.ly short urls this key is needed.</p>
</td>
</tr>

<tr>
<td colspan="2"><input type="submit" name="saveaccount" value="Save" class="add_product_btn" />
</td>
</tr>
</table>
</form>
</div>
<br />
<br />
</div>
</div>	<div class="clear">

	<div id="full_footer">
			<div id="inner_footer">
				<p>Created by Satyam Technologies &copy; 2016 </p>
			</div>
		</div>
	
		
	</body>
</html>
<?php			
	}else{
		//Display login button
	//	echo '<a href="process.php">Login</a>';
		header('location: ../index.php');
	}
?>