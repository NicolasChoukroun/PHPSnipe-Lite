<?php
/*
 *  Copyright (c) 2013-2020. Nicolas Choukroun.
 *  Copyright (c) 2013-2020. The PHPSnipe Developers.
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the Attribution 4.0 International License as published by the
 *  Creative Commons Corporation; either version 2 of the License, or (at your option)
 *  any later version.  See COPYING for more details.
 *
 ******************************************************************************/ 

class Settings
{
	public $allfields;
	public $allcat;
	public $scraper;
	public $info;
	public $iframe;
	public $is_approved_setting;
	public $warning;
	public $web_title;
	public $import_path;
	public $daystart;
	public $dayend;
	public $author;
	public $stealthmode;
	public $defcon;
	public $smtp_address;
	public $smtp_login;
	public $smtp_pass;
	public $expiration;
	public $cache_time;
	public $waitanim;
	public $bitcoin;
	public $download_path;
	public $upload_path;
	public $torrent_watch_path;
	public $highest_score;
	public $score_interval;
	public $twitter;
	public $TwitterConsumerKey;
	public $TwitterConsumerSecret;
	public $TwitterAccessTocken;
	public $TwitterAccessTockenSecret;
	public $FacebookAppId;
	public $FacebookAppSecret;
	public $FacebookPageId;
	public $FacebookReturnUrl;
	public $failedlogins;
	public $failedloginswait;
	public $servername;
	public $ftp_server;
	public $ftp_user_name;
	public $ftp_user_pass;
	public $ftp_backup;
	public $login_ip_nbr;
	public $nbr_downloads_max;
	public $nbr_downloads_time;
	public $log_as;
	public $send_emails;
	public $email_signature;
	public $tmp_path;
	public $download_url;
	public $upload_exclu;
	public $upload_score;
	public $upload_files;
	public $upload_bytes;
	public $rank_hidden;
	public $failed_login_nbr;
	public $failed_login_time;
	public $success_login_nbr;
	public $success_login_time;
	public $review_exclu;
	public $review_score;
	public $review_files;
	public $review_bytes;
	public $rank_download;
	public $rank_user_downloads;
	public $rank_archive;
	public $rank_zip;
	public $rank_unity;
	public $rank_profile_another;
	public $rank_profile_another_downloads;
	public $rank_profile_downloads;
	public $rank_future;
	public $rank_see_rank;
	public $rank_see_torrents;
	public $rank_see_names;
	public $authorized_ext;
	public $max_size_upload;
	public $userid;
	public $visitor;
	public $editor;
	public $free;
	public $admin;
	public $rank;
	public $is_blocked;
	public $is_stopped;
	public $is_approved;
	public $is_locked;
	public $is_silenced;
	public $public_nickname;
	public $nickname;
	public $banned;
	public $score;
	public $yourscore;
	public $thanks;
	public $levelstatus;
	public $comment;
	public $bio;
	public $gravatar;
	public $levelid;
	public $usercreditbandwidth;
	public $usercreditfile;
	public $usercreditexclu;
	public $dmca;
	public $kma;
	public $url;
	public $dailybandwidth;
	public $dailyfile;
	public $dailyexclu;
	public $exscore;
	public $donation;
	public $bandwidthused;
	public $nbroffileused;
	public $nbrofexcluused;
	public $dailyfiles;
	public $theme;
	public $theme_url;
	public $theme_version;
	public $noregistration;
	public $money;
	public $userdb;
	public $firstname;
	public $lastname;
	public $login;
	public $root;
	public $display;
	public $logged;
	public $limit_credits;

	public $excluok;
	public $fileok;
	public $bandwidthok;
	public $success;
	public $error;
	public $sessionid;
	public $ip;
	public $optimize;
	public $timezone;
	public $patron;
	public $account_bandwidth;
	public $account_file;
	public $account_exclu;
	public $chat_discord_invite;
	public $chat_discord_widget;
	public $chat_mode;
	public $alert;
	public $desc;
	public $name;
	public $invite;
	public $renovation;
	public $adminip;
	public $language;
	public $hostname;
	public $isp;
	public $org;
	public $city;
	public $country;
	public $countrycode;
	public $googlebot;
	public $location;
	public $zip;
	public $closed;
	public $genname;
	public $rank_anon;
	public $discord_donator_invite;
	public $use_discord;
	public $no_registration;
	public $time_approval_low;
	public $time_approval_high;
	public $scraper_description;
	public $scraper_tag;
	public $nbr_total_download;
	public $image_w;
	public $image_h;
	public $discord_queue;
	public $thisurl;
	public $discord_new_hide;
	public $nbrgrid;
	public $referer;
	public $no_downloads;
	public $limit_tip_exclu;
	public $limit_tip_bandwidth;
	public $limit_tip_files;
	public $limit_reward_bandwidth;
	public $limit_reward_files;
	public $limit_reward_exclu;
	public $qsection;
	public $qcategory;
	public $qsubcategory;
	public $qsubcategory2;
	public $qsubcategory3;
	public $section;
	public $category;
	public $subcategory;
	public $subcategory2;
	public $subcategory3;
	public $discord_new_reward;
	public $rank_speed_download;
	public $display_news_section;
	public $display_pdf_section;
	public $display_reward_section;
	public $display_review_section;
	public $display_bargain_section;
	public $display_forum_section;
	public $httpmode;
	public $button_squared;
	public $theme_css_topbar;
	public $css;
	public $library_answers_patrons;
	public $library_answers_default;
	public $sql_add;
	public $tracking;
	public $search_compatible_3dmodel;
	public $search_compatible_app;
	public $search_compatible_texture;
	public $search_compatible_movie;
	public $search_compatible_pdf;
	public $search_compatible_unity;
	public $search_compatible_ue4;
	public $search_compatible_audio;
	public $turnoffjquery;
	public $otherfilter_exclu;
	public $otherfilter_forged;
	public $otherfilter_forged1;
	public $otherfilter_forged2;
	public $otherfilter_free;
	public $search_compatible_sound;
	public $filter_type_library;
	public $filter_type_archive;
	public $filter_type_categories;
	public $script;





	function __construct ()
	{
		$this->time_start = microtime(true);
		$db = new Database();
		$sql = "SELECT * FROM settings";
		$db->query($sql);
		$i = 0;
		while ($db->nextRecord()) {
			$field = $db->rs['name'];
			$this->$field = $db->rs['value'];
			$this->desc[$field] = $db->rs['description'];
			$this->name[$field] = $db->rs['name'];
			$i++;
		}

		$this->optimize = true;
		$this->iframe = false;
		$this->xcrud=false;
		$this->setupblockui=0;
		$db->close();
	}

	public static function loadSessionStatic ()
	{
		$sid = getSession("sessionid");
		if (isset($_SESSION[$sid])) {
			$s1 = unserialize(base64_decode(rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(SESSION_KEY), $_SESSION[$sid], MCRYPT_MODE_CBC, md5(md5(SESSION_KEY))), "\0")));

		} else {
			$s1 = ERROR_SESSION_NOTEXIST;
		}

		return $s1;
	}

	function loadSession ()
	{
		$sid = getSession("sessionid");
		if (isset($_SESSION[$sid])) {
			$s1 = unserialize(base64_decode(rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(SESSION_KEY), $_SESSION[$sid], MCRYPT_MODE_CBC, md5(md5(SESSION_KEY))), "\0")));

		} else {
			$s1 = ERROR_SESSION_NOTEXIST;
		}

		return $s1;
	}

	public function reload ()
	{
		$db = new Database();
		$sql = "SELECT * FROM settings ORDER BY id ASC";
		$db->query($sql);
		$i = 0;
		while ($db->nextRecord()) {
			$field = $db->rs['name'];
			$this->$field = $db->rs['value'];
			$this->desc[$field] = $db->rs['description'];
			$this->name[$field] = $db->rs['name'];
			$i++;
		}
		$re = '/(?:https?:\/\/)?(?:www\.)?(.*)\.(?=[\w.]{3,4})/';
		$str = $_SERVER['SERVER_NAME'];
		preg_match_all($re, $str, $matches);
		$this->servername = $matches[1][0];

		$this->optimize = true;
		$this->iframe = false;

		// fill in the category translation table.
		$sql = "SELECT * FROM categories ";
		$db->query($sql);
		while ($db->nextRecord()) $this->allcat[$db->rs['id']] = $db->rs['name'];

		$db->close();
		$this->saveSession();
	}

	public function saveSession() {
		global $user;
		session_start(['cookie_lifetime' => 2419200]); // 1 month
		session_set_cookie_params(2419200); // sessions last 1 hour*24*7=1 week
		$this->sid = $this->sid = md5((time() * $user->id * rand(1, 9999999)));
		$this->FSCUID = md5(FSCUID);
		$s1 = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(SESSION_KEY), base64_encode(serialize($this)), MCRYPT_MODE_CBC, md5(md5(SESSION_KEY)));
		setSession($user->sid, $s1); // settings

	}

	function __destruct ()
	{

	}

}

?>