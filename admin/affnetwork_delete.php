<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("../inc/adm_auth.inc.php");
	require_once("../inc/config.inc.php");


	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$network_id = (int)$_GET['id'];
		
		smart_mysql_query("DELETE FROM cashbackengine_affnetworks WHERE network_id='$network_id'");
		
		header("Location: affnetworks.php?msg=deleted");
		exit();
	}

?>