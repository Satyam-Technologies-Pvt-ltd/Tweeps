<?php
if(array_key_exists('logout',$_GET))
{ include "config.php";
	session_start();
	unset($_SESSION['userdata']);
	session_destroy();
	header("Location:".INSTALL_BASE);
}
?>