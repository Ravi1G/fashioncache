<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");

	$content = GetContent('howitworks');

	///////////////  Page config  ///////////////
	$PAGE_TITLE = $content['title'];

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo $content['title']; ?></h1>
	<p><?php echo $content['text']; ?></p>

<?php require_once("inc/footer.inc.php"); ?>