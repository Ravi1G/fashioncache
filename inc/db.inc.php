<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	if (!defined("CBengine_PAGE")) exit();

	$conn = @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ('Could not connect to MySQL server');
	@mysql_select_db(DB_NAME, $conn) or die ('Could not select database');

?>