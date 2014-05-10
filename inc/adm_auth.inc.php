<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	if (!(isset($_SESSION['adm']['id']) && is_numeric($_SESSION['adm']['id'])))
	{
		header("Location: login.php");
		exit();
	}
	else
	{
		$admin_panel = 1;
	}

?>