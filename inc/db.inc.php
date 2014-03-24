<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	if (!defined("CBengine_PAGE")) exit();

	$conn = @mysql_connect(FC_DB_HOST, FC_DB_USER, FC_DB_PASSWORD) or die ('Could not connect to MySQL server');
	@mysql_select_db(FC_DB_NAME, $conn) or die ('Could not select database');

	
	function fc_reconnect_db() {
		$conn = @mysql_connect(FC_DB_HOST, FC_DB_USER, FC_DB_PASSWORD) or die ('Could not connect to MySQL server');
		@mysql_select_db(FC_DB_NAME, $conn) or die ('Could not select database');
	}
?>