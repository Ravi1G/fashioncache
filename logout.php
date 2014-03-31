<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	
	unset($_SESSION['userid'], $_SESSION['FirstName'], $_SESSION['goRetailerID'], $_SESSION['goCouponID']);
	
	session_destroy();

	setcookie("usname", "", time()-3600);

	header("Location: signup_or_login.php");
	exit();
	
?>