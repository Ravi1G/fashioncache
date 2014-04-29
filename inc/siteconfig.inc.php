<?php
/*******************************************************************\
 * CashbackEngine v2.1
 * http://www.CashbackEngine.net
 *
 * Copyright (c) 2010-2014 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	$setts_sql = "SELECT * FROM cashbackengine_settings";
	$setts_result = smart_mysql_query($setts_sql);

	unset($settings);
	$settings = array();

	while ($setts_row = mysql_fetch_array($setts_result))
	{
		$settings[$setts_row['setting_key']] = $setts_row['setting_value'];
	}

	define('SITE_TITLE', $settings['website_title']);
	define('SITE_MAIL', $settings['website_email']);
	define('NOREPLY_MAIL', $settings['noreply_email']);
	define('SITE_ALERTS_MAIL', $settings['alerts_email']);
	define('SITE_URL', $settings['website_url']);
	define('SITE_MODE', $settings['website_mode']);
	define('SITE_HOME_TITLE', $settings['website_home_title']);
	define('SITE_LANGUAGE', $settings['website_language']);
	define('MULTILINGUAL', $settings['multilingual']);
	define('SITE_TIMEZONE', $settings['website_timezone']);
	define('SITE_CURRENCY', $settings['website_currency']);
	define('SITE_CURRENCY_FORMAT', $settings['website_currency_format']);
	define('SIGNUP_CAPTCHA', $settings['signup_captcha']);
	define('ACCOUNT_ACTIVATION', $settings['account_activation']);
	define('LOGIN_ATTEMPTS_LIMIT', $settings['login_attempts_limit']);
	define('LOGIN_ATTEMPTS', 5);
	define('NEW_COUPON_ALERT', $settings['email_new_coupon']);
	define('NEW_REVIEW_ALERT', $settings['email_new_review']);
	define('NEW_TICKET_ALERT', $settings['email_new_ticket']);
	define('NEW_TICKET_REPLY_ALERT', $settings['email_new_ticket_reply']);
	define('NEW_REPORT_ALERT', $settings['email_new_report']);
	define('STORES_LIST_STYLE', $settings['stores_list_style']);
	define('HOMEPAGE_REVIEWS_LIMIT', $settings['homepage_reviews_limit']);
	define('TODAYS_COUPONS_LIMIT', $settings['todays_coupons_limit']);
	define('FEATURED_STORES_LIMIT', $settings['featured_stores_limit']);
	define('POPULAR_STORES_LIMIT', $settings['popular_stores_limit']);
	define('NEW_STORES_LIMIT', $settings['new_stores_limit']);
	define('RESULTS_PER_PAGE', $settings['results_per_page']);
	define('COUPONS_PER_PAGE', $settings['coupons_per_page']);
	define('SUBMIT_COUPONS', $settings['submit_coupons']);
	define('HIDE_COUPONS', $settings['hide_coupons']);
	define('MIN_PAYOUT_PER_TRANSACTION', $settings['min_transaction']);
	define('MIN_PAYOUT', $settings['min_payout']);
	define('SIGNUP_BONUS', $settings['signup_credit']);
	define('REFER_FRIEND_BONUS', $settings['refer_credit']);
	define('REFERRAL_COMMISSION', $settings['referral_commission']);
	define('IMAGE_WIDTH', $settings['image_width']);
	define('IMAGE_HEIGHT', $settings['image_height']);
	define('IMAGE_WIDTH2', $settings['image_width2']);
	define('IMAGE_HEIGHT2', $settings['image_height2']);
	define('SHOW_LANDING_PAGE', $settings['show_landing_page']);
	define('REVIEWS_APPROVE', $settings['reviews_approve']);
	define('MAX_REVIEW_LENGTH', $settings['max_review_length']);
	define('REVIEWS_PER_PAGE', $settings['reviews_per_page']);
	define('NEWS_PER_PAGE', $settings['news_per_page']);
	define('SHOW_CASHBACK_CALCULATOR', $settings['show_cashback_calculator']);
	define('SHOW_RETAILER_STATS', $settings['show_statistics']);
	define('SHOW_SITE_STATS', $settings['show_site_statistics']);
	define('FACEBOOK_CONNECT', $settings['facebook_connect']);
	define('FACEBOOK_APPID', $settings['facebook_appid']);
	define('FACEBOOK_SECRET', $settings['facebook_secret']);
	define('FACEBOOK_PAGE', $settings['facebook_page']);
	define('SHOW_FB_LIKEBOX', $settings['show_fb_likebox']);
	define('TWITTER_PAGE', $settings['twitter_page']);
	define('GOOGLE_ANALYTICS', $settings['google_analytics']);
	define('BANNER_SPEED', $settings['banner_speed']);

	// addthis.com Account ID
	define('ADDTHIS_ID', 'YOUR-ACCOUNT-ID');

	// letters for alphabetical order 
	$alphabet = array ("0-9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

	// results per page dropdown
	$results_on_page = array("5", "10", "25", "50", "100", "111111");

	// site languages
	$languages = array();
	$languages['us'] = "english";
	//$languages['de'] = "german";

?>