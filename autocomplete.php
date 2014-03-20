<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	require_once("inc/config.inc.php");

	$q = $_GET["q"];
	if (!$q) return;

	$q = strtolower(mysql_real_escape_string($q));
	$q = substr(trim($q), 0, 100);

	$ac_result = smart_mysql_query("SELECT DISTINCT retailer_id,title FROM cashbackengine_retailers WHERE title LIKE '%$q%' AND (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' LIMIT 20");

	if (mysql_num_rows($ac_result) > 0)
	{
		while ($ac_row = @mysql_fetch_array($ac_result))
		{
			echo $ac_row['title']."\n";
		}
	}

?>