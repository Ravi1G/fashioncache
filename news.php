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
	require_once("inc/pagination.inc.php");


	$results_per_page = NEWS_PER_PAGE;
	$cc = 0;

	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;
	
	$result = smart_mysql_query("SELECT *, DATE_FORMAT(added, '%M %e, %Y') AS news_date FROM cashbackengine_news WHERE status='active' ORDER BY added DESC LIMIT $from, $results_per_page");
	
	$total_result = smart_mysql_query("SELECT * FROM cashbackengine_news WHERE status='active' ORDER BY added DESC");
	$total = mysql_num_rows($total_result);
	$total_on_page = mysql_num_rows($result);


	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_NEWS_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_NEWS_TITLE; ?></h1>

	<?php if ($total > 0) { ?>

		<?php while ($row = mysql_fetch_array($result)) { ?>
		<div class="news_info">
			<div class="news_date"><?php echo $row['news_date']; ?></div>
			<div class="news_title"><a href="<?php echo SITE_URL; ?>news_details.php?id=<?php echo $row['news_id']; ?>"><?php echo $row['news_title']; ?></a></div>
			<div class="news_description">
				<?php
					if (strlen($row['news_description']) > 450)
						$news_description = substr($row['news_description'], 0, 450)."...<a class='more' href='/news_details.php?id=".$row['news_id']."'>read more</a>";
					else
						$news_description = $row['news_description'];
					
					echo $news_description;
				?>
			</div>
		</div>
		<?php } ?>

		<?php echo ShowPagination("news",$results_per_page,"news.php?","WHERE status='active'"); ?>

	<?php }else{ ?>
				<p><?php echo CBE1_NEWS_NO; ?></p>
	<?php } ?>

<?php require_once ("inc/footer.inc.php"); ?>