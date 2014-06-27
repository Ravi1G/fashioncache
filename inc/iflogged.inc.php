<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	if (isset($_SESSION['userid']) && is_numeric($_SESSION['userid']))
	{
		header("Location: index.php");
		exit();
	}

?>