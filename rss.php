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

	define('RSS_RESULTS_LIMIT', 1000);

	// show latest retailers //
	$query = "SELECT *, DATE_FORMAT(added, '%a, %d %b %Y %T') as pub_date FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY added DESC LIMIT ".RSS_RESULTS_LIMIT;
	$result = smart_mysql_query($query);
	$total = mysql_num_rows($result);

	if ($total > 0)
	{
		header("Content-Type: application/xml; charset=UTF-8");

		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<rss version="2.0">';
		echo '<channel>';
		echo '<title>'.SITE_TITLE.'</title>';
		echo '<link>'.SITE_URL.'</link>';
		echo '<description>'.SITE_HOME_TITLE.'</description>';
		echo '<image>';
			echo '<url>'.SITE_URL.'images/logo.gif</url>';
			echo '<title>'.SITE_TITLE.'</title>';
			echo '<link>'.SITE_URL.'</link>';
		echo '</image>';

		while($row = mysql_fetch_array($result)) 
		{
			$item_title			= well_formed($row['title']);
			$item_cashback		= DisplayCashback($row['cashback']);
			$item_url			= GetRetailerLink($row['retailer_id'], $row['title']);
			$item_pubdate		= $row['pub_date']." PDT";

			if ($row['cashback'] != "") $item_title	.= ' - '.$item_cashback.' '.CBE1_CASHBACK2;

			if (strlen($row['description']) > 300)
				$item_description = substr(well_formed(stripslashes($row['description'])),0,300).'...';
			else
				$item_description = well_formed(stripslashes($row['description']));

			echo '
				<item>
					<title><![CDATA['.$item_title.']]></title>
					<link>'.$item_url.'</link>
					<guid>'.$item_url.'</guid>
					<pubDate>'.$item_pubdate.'</pubDate>
					<description><![CDATA['.$item_description.']]></description>
				</item>
				';
		} 
		
		echo '</channel>';
		echo '</rss>';
	}

?>