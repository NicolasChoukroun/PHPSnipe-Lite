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

Class User
{

	public $id;
	public $sid;
	public $allfields;
	public $password;
	public $email;
	public $aptreon_email;
	public $discord_nickname;
	public $subscription;
	public $started_date;
	public $firstname;
	public $lastname;
	public $address1;
	public $address2;
	public $city;
	public $state;
	public $zip;
	public $country;
	public $company;
	public $phone;
	public $added;
	public $date;
	public $remote_addr;
	public $from;
	public $statusid;
	public $newsletter;
	public $lang;
	public $i_agree;
	public $is_stopped;
	public $is_locked;
	public $is_silenced;
	public $is_approved;
	public $reseller_id;
	public $comment;
	public $tax_id;
	public $aff_id;
	public $aff_payout_type;
	public $credit_file;
	public $credit_size;
	public $credit_exclu;
	public $public_nickname;
	public $invite_code;
	public $ip;
	public $ip_org;
	public $ip_area;
	public $ip_city;
	public $ip_country;
	public $levelstatus;
	public $last_login;
	public $last_ip;
	public $last_session;
	public $aff_added;
	public $kma_password;
	public $avatar;
	public $score;
	public $thanks;
	public $nbruploads;
	public $nbrcomments;
	public $nbrreviews;
	public $nbractions;
	public $rank;
	public $warning_message;
	public $help;
	public $signature;
	public $bio;
	public $sex;
	public $donation;
	public $type;
	public $levelid;
	public $genname;
	public $sparkle;
	public $anonymous;
	public $theme;
	public $random;
	public $css;
	public $patron;
	public $substatus;
	public $buttons_squared;
	public $is_admin;
	public $is_editor;
	public $dailyexclu;
	public $nbrofexcluused;
	public $dailyfile;
	public $nbroffileused;
	public $dailybandwidth;
	public $nbrofbandwidthused;
	public $bandwidthused;
	public $fileok;
	public $sizeok;
	public $excluok;
	public $bandwidthok;
	public $error;
	public $usercreditfile;
	public $usercreditexclu;
	public $usercreditbandwidth;

	private $method;
	private $ivSize;
	private $iv;
	private $encrypt_password;
	private $key;

	private $db;
	private $reload_time;
	private $css_custom;

	/**
	 * User constructor.
	 *
	 * @param string $login
	 * @param string $password
	 */
	function __construct ($login = "", $password = "")
	{
		// session encryption with openssl
		$this->method = 'AES-128-CFB8'; // AES/CFB8/NoPadding
		$this->ivSize = openssl_cipher_iv_length($this->method);
		$this->iv = openssl_random_pseudo_bytes($this->ivSize);
		$this->encrypt_password = md5($login.$password."lzzxs9875678987da45gmnbvnbv$$##adsd987");
		$this->key = password_hash($this->encrypt_password, PASSWORD_BCRYPT, ['cost' => 12]);

		$this->db = new Database();
		if ($this->isLogged() == true) {
			return $this;
		}
		$this->is_logged = false;
		$this->error = $this->login($login, $password);
		if ($this->error <> true) return $this->error;
		$this->reload_time = time() + 600;
		return $this;

	}

	/**
	 * isLogged
	 *
	 * @param bool $topbar
	 *
	 * @return bool
	 */
	function isLogged ($topbar = false)
	{
		$temp = $this->loadSession();
		//dump($temp);
		//exit;
		if ($temp->id <= 0 || !is_object($temp)) {
			return false;
		}
		if (is_object($temp)) {
			foreach ($temp as $key => $v) {
				$this->$key = $v;

			}
			if ($this->patron > 0 && $this->substatus==SUBCRIPTION_RUNNING) {
				if ($this->patron>$this->rank) {$this->realrank = $this->rank;$this->rank = $this->patron;}
				else {$this->realrank = $this->rank;}
			}
			//$this->calculateCredits();
			switch ($this->type) {
				case TYPE_ADMIN:
					$this->is_admin = true;
					$this->is_editor = true;
					$this->is_visitor = false;
					break;
				case TYPE_EDITOR:
					$this->is_editor = true;
					$this->is_admin = false;
					$this->is_visitor = false;
					break;
				default:
					$this->is_admin = false;
					$this->is_editor = false;
					$this->is_visitor = true;
					break;
			}
			return true;
		}
		return false;
	}


	/**
	 * calculateCredits
	 *
	 * @return int
	 */
	function calculateCredits ()
	{
		if ($this->id == 0 || $this->id == "") {
			$this->logout();
			return ERROR_LOGIN;
		}

		$db = new Database();

		$this->usercreditbandwidth = abs($this->credit_size);
		$this->usercreditexclu = abs($this->credit_exclu);
		$this->usercreditfile = abs($this->credit_file);

		// check the bandwidth and number of files used today
		//$t=strtotime("today");
		$sql="SELECT SUM(size) AS bandwidth,count(*) AS total,sum(exclu) AS totalexclu FROM action_logs WHERE  userid=" . $this->id . " AND actionid=" . ACTION_DOWNLOAD . " AND DATE(FROM_UNIXTIME(action_logs.date)) > CURDATE() - INTERVAL 1 DAY";
		//$sql = "SELECT SUM(size) AS bandwidth,count(*) AS total,sum(exclu) AS totalexclu FROM action_logs WHERE userid='" . $this->id . "' AND actionid=" . ACTION_DOWNLOAD . " AND date>=".$t;
		$db->query($sql);
		//echo $sql;exit;
		$db->singleRecord();
		$this->bandwidthused = $db->Record['bandwidth'];
		$this->nbroffileused = $db->Record['total'];
		$this->nbrofexcluused = $db->Record['totalexclu'];
		$db->close();

		if ($this->patron > 0 && $this->rank < $this->patron) {$this->realrank = $this->rank;$this->rank = $this->patron;}

		$this->dailyexclu = round($this->rank, 0);
		if ($this->patron > 0) $this->dailyexclu = round($this->rank*2, 0) ;
		else $this->dailyexclu = round($this->rank, 0);

		$this->dailybandwidth = floatval(($this->rank) * 1073741824 + 504857600); // 100 Mb + 1Gb * rank
		if ($this->patron > 0) $this->dailybandwidth = floatval(($this->rank) * 1073741824*2 + 504857600); // 100 Mb + 1Gb * rank
		else $this->dailybandwidth = floatval(($this->rank) * 1073741824 + 504857600); // 100 Mb + 1Gb * rank


		if ($this->patron > 0) $this->dailyfile = 5 + ($this->rank * 10) + 1; // 1 + 2 * rank
		else $this->dailyfile = 5 + ($this->rank * 5) + 1; // 1 + 2 * rank


		$this->fileok = ($this->dailyfile - $this->nbroffileused) + $this->usercreditfile;
		if ($this->fileok < 0) $this->fileok = 0;
		$this->bandwidthok = (float)($this->dailybandwidth - $this->bandwidthused) + $this->usercreditbandwidth;
		if ($this->bandwidthok < 0) $this->bandwidthok = 0;
		$this->excluok = ($this->dailyexclu - $this->nbrofexcluused) + $this->usercreditexclu;
		if ($this->excluok < 0) $this->excluok = 0;

		return true;
	}

	/**
	 * logout
	 */
	function logout ()
	{
		global $theme;
		session_start();
		session_unset();
		unset($_SESSION);
		setSession($this->sid, "");
		setSession($this->sid . "s2", "");
		setSession($this->sid . $theme->name, "");
		session_destroy();
		session_write_close();
		setcookie(session_name(), '', 0, '/');
		session_regenerate_id(true);

	}



	/**
	 * login
	 *
	 * @param     $email
	 * @param     $pass
	 * @param int $log_as
	 *
	 * @return bool|int
	 */
	function login ($email, $pass, $log_as = 0)
	{
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$sql = "SELECT * FROM users WHERE (email = '" . $email . "' OR login = '" . $email . "')";
        //echo $sql."<br>";
		$this->db->query($sql);
        $nbr = $this->db->nbr();
        $this->db->single();


		if ($nbr == 1) {
			$hash = $this->db->rs['password'];
			$t_hasher = new PasswordHash(12, true);

			$check = $t_hasher->CheckPassword($pass, $hash);
			if ($check == 1) {

				$this->is_logged = true;
				$this->is_admin = false;
				$this->is_editor = false;
				$this->is_visitor = false;
				$this->ip = $_SERVER['REMOTE_ADDR'];


				foreach ($this->db->rs as $key => $v) {
					$this->$key = $v;
					$this->allfields[] = $key;
				}

				if ($this->is_admin && $log_as == 0) $this->rank = 14;
				if ($this->patron > 0 && $this->substatus==SUBCRIPTION_RUNNING) {
					if ($this->patron>$this->rank) {$this->realrank = $this->rank;$this->rank = $this->patron;}
					else {$this->realrank = $this->rank;}
				}
				if ($this->db->rs['type'] == TYPE_ADMIN) {
					$this->is_admin = true;
					if ($log_as <> 0) {
						$this->id = $log_as;
						$this->is_admin = $admin = true;
					}
				}
				if ($this->db->rs['type'] == TYPE_EDITOR) {
					$this->is_admin = false;
					$this->is_editor = true;
				}
				if (!$this->is_approved) {

					$this->logout();
					header("Location: /401waiting.html");
					exit;
				}
				// locked
				if ($this->is_locked) {
					$this->logout();
					header("Location: /401locked.html");
					exit;
				}
				// locked
				if ($this->is_stopped) {
					$this->logout();
					header("Location: /401onhold.html");
					exit;
				}
				$this->fullname = ucwords($this->firstname . " " . $this->lastname);


				$this->saveSession();

				return true;
			} else return (ERROR_PASSWORD);
		} else return (ERROR_LOGIN);
	}


	function regenerateSession($reload = false)
	{
		// This token is used by forms to prevent cross site forgery attempts
		if(!isset($_SESSION['nonce']) || $reload)
			$_SESSION['nonce'] = bin2hex(openssl_random_pseudo_bytes(32));

		if(!isset($_SESSION['IPaddress']) || $reload)
			$_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];

		if(!isset($_SESSION['userAgent']) || $reload)
			$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];

		//$_SESSION['user_id'] = $this->user->getId();

		// Set current session to expire in 1 minute
		$_SESSION['OBSOLETE'] = true;
		$_SESSION['EXPIRES'] = time() + 60;

		// Create new session without destroying the old one
		session_regenerate_id(false);

		// Grab current session ID and close both sessions to allow other scripts to use them
		$newSession = session_id();
		session_write_close();

		// Set session ID to the new one, and start it back up again
		session_id($newSession);
		session_start();

		// Don't want this one to expire
		unset($_SESSION['OBSOLETE']);
		unset($_SESSION['EXPIRES']);
	}

	function checkSession()
	{
		try{
			if($_SESSION['OBSOLETE'] && ($_SESSION['EXPIRES'] < time()))
				throw new Exception('Attempt to use expired session.');

			if(!is_numeric($_SESSION['user_id']))
				throw new Exception('No session started.');

			if($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR'])
				throw new Exception('IP Address mixmatch (possible session hijacking attempt).');

			if($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT'])
				throw new Exception('Useragent mixmatch (possible session hijacking attempt).');


			if(!$_SESSION['OBSOLETE'] && mt_rand(1, 100) == 1)
			{
				$this->regenerateSession();
			}

			return true;

		}catch(Exception $e){
			return false;
		}
	}


	/**
	 * loadSession
	 *
	 * @return int|mixed
	 */
	function loadSession ()
	{
		$sid = getSession("sessionid");
		if (isset($_SESSION[$sid . "s2"])) {
			$s2 = unserialize(base64_decode(rtrim(decrypt( $_SESSION[$sid . "s2"]), "\0")));
		} else {
			unset($_SESSION[$sid . "s2"]);
			$_SESSION[$sid . "s2"]="";
			$s2 = ERROR_SESSION_NOTEXIST;
		}
		return $s2;
	}

	/**
	 *  saveSession
	 *
	 */
	function saveSession ()
	{
		Global $settings;
		
		$this->calculateCredits();
		session_start(['cookie_lifetime' => COOKIE_EXPIRATION]); // 1 month=2419200 | 48h= 86400
		session_set_cookie_params(SESSION_EXPIRATION); //
		$settings->sid = $this->sid = md5((time() * $this->id * rand(1, 9999999)));
		$settings->FSCUID = md5(FSCUID);
		$s2 = encrypt( base64_encode(serialize($this)));
		//$s2= $this->encrypt(base64_encode(serialize($this)),)
		setSession($this->sid . "s2", $s2); // user
		setSession("email", encrypt($this->email)); // this mean that the customer is logged.
		setSession("sessionid", $this->sid);
		setSession("password", encrypt($this->password));
	}

	private function encrypt($data)
	{
		return base64_encode(openssl_encrypt($data, $this->method, $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv));
	}

	private function decrypt($data)
	{
		return openssl_decrypt(base64_decode($data), $this->method, $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
	}

	/**
	 * static_logout
	 *
	 */
	public static function static_logout ()
	{
		global $theme, $settings;
		session_start();
		session_unset();
		unset($_SESSION);
		setSession($settings->sid, "");
		setSession($settings->sid . "s2", "");
		setSession($settings->sid . $theme->name, "");
		session_destroy();
		session_write_close();
		setcookie(session_name(), '', 0, '/');
		session_regenerate_id(true);
	}

	/**
	 * addCreditUser()
	 *
	 * @return
	 */
	public static function addCreditUserFromId ($userid, $creditfile, $creditsize, $creditexclu = 1)
	{
		global $warning, $settings;

		$db = new Database;
		$sql = "SELECT * from users where id=$userid";

		$db->query($sql);
		$db->singleRecord();

		$c_size = abs($db->Record['credit_size']);
		$c_file = abs($db->Record['credit_file']);
		$c_exclu = abs($db->Record['credit_exclu']);

		$f = $c_file + $creditfile;
		$s = $c_size + $creditsize;
		$e = $c_exclu + $creditexclu;
		if ($e > $settings->limit_credit_exclu) $e = $settings->limit_credit_exclu;
		if ($e >= $settings->limit_credit_exclu) {
			$warning = "You have reached your maximum Exclu Credit (" . $settings->limit_credit_exclu . ")";
		}

		if ($f > $settings->limit_credit_file) {
			$warning = "Credit limit of " . $settings->limit_credit_file . " reached";
		}
		if ($s > intval($settings->limit_credit_bandwidth / 1024)) {
			$warning = "Credit limit of " . $settings->limit_credit_bandwidth . " reached";
		}

		if ($f > $settings->limit_credit_file) $f = $settings->limit_credit_file;
		if ($s > $settings->limit_credit_bandwidth) $s = $settings->limit_credit_bandwidth;
		if ($f < 0) $f = '0';
		if ($s < 0) $s = '0';
		if ($e < 0) $e = '0';

		$sql = "UPDATE users SET credit_file = " . $f . " WHERE id = $userid";

		$db->query($sql) or die("error: user credit file update:" . $sql);
		$sql = "UPDATE users SET credit_size = " . $s . " WHERE id = $userid";

		$db->query($sql) or die("error: user credit size update:" . $sql);
		$sql = "UPDATE users SET credit_exclu = " . $e . " WHERE id = $userid";

		$db->query($sql);
		$db->close();

	}

	/**
	 * subCreditUserFromId
	 *
	 * @param $userid
	 * @param $creditfile
	 * @param $creditsize
	 * @param $creditexclu
	 */
	public static function subCreditUserFromId ($userid, $creditfile, $creditsize, $creditexclu)
	{
		global $settings, $user;

		$db = new Database;
		$sql = "SELECT  SQL_NO_CACHE * from users WHERE id=$userid";
		$db->query($sql);
		$db->singleRecord();
		$c_size = abs($db->Record['credit_size']);
		$c_file = abs($db->Record['credit_file']);
		$c_exclu = abs($db->Record['credit_exclu']);
		if ($creditfile > 0) {
			$f_total = ($c_file + $user->dailyfile) - $user->nbroffileused - $creditfile;
			if ($f_total < 0) {
				$settings->warning = "You have no more credit files (files<0)";
			} else {
				$f_daily = ($user->dailyfile) - $user->nbroffileused - $creditfile;
				if ($f_daily < 0) {
					$f = $c_file - $creditfile;
					if ($f < 0) $f = 0;
					$user->subcreditfile = $creditfile;
					$user->subcreditfileleft = $f;
					if ($f == 0) $f = '0';
					$sql = "UPDATE users SET credit_file = '" . $f . "' WHERE id = $userid";
					$db->query($sql);
				}
			}
		}
		if ($creditsize > 0) {
			// check if all credits gone
			$s_total = ($c_size + $user->dailybandwidth) - $user->bandwidthused - $creditsize;
			if ($s_total < 0) {
				$settings->warning = "You have no more credits bandwidth (bandwidth<0)";
			} else {
				$s_daily = ($user->dailybandwidth) - $user->bandwidthused - $creditsize;
				if ($s_daily < 0) {
					$s = $c_size - $creditsize;
					if ($s < 0) $s = 0;
					$user->subcreditsize = $creditsize;
					$user->subcreditsizeleft = $s;
					if ($s == 0) $s = '0';
					$sql = "UPDATE users SET credit_size = '" . $s . "' WHERE id = $userid";
					$db->query($sql);
				}
			}
		}
		if ($creditexclu > 0) {
			$e_total = ($c_exclu + $user->dailyexclu) - $user->nbrofexcluused - $creditexclu;
			if ($e_total < 0) {
				$settings->warning = "You have no more credits exclusive ( exclu<0)";
			} else {
				$e_daily = ($user->dailyexclu) - $user->nbrofexcluused - $creditexclu;
				if ($e_daily < 0) {
					$e = $c_exclu - $creditexclu;
					if ($e < 0) $e = 0;

					$user->subcreditexclu = $creditexclu;
					$user->subcreditexcluleft = $e;
					if ($e == 0) $e = '0';
					$sql = "UPDATE users SET credit_exclu = '" . $e . "' WHERE id = $userid";
					$db->query($sql);
				}
			}
		}
		$db->close();
		$user->reload();
	}

	/**
	 * subCreditUserPermanentFromId
	 *
	 * @param $userid
	 * @param $creditfile
	 * @param $creditsize
	 * @param $creditexclu
	 */
	public static function subCreditUserPermanentFromId ($userid, $creditfile, $creditsize, $creditexclu)
	{
		global $settings;

		refresh_session(); // we need to refresh the session with the new values.

		$db = new Database;
		$sql = "SELECT  SQL_NO_CACHE * from users WHERE id=$userid";
		$db->query($sql);
		$db->singleRecord();
		$c_size = $db->Record['credit_size'];
		$c_file = $db->Record['credit_file'];
		$c_exclu = $db->Record['credit_exclu'];

		if ($creditfile > 0) {
			$f = $c_file - $creditfile;
			if ($f <= 0) {
				$settings->warning = "You have no more credit files (files<0)";
				$f = 0;
			}
			$sql = "UPDATE users SET credit_file = '" . $f . "' WHERE id = $userid";
			$db->query($sql);
		}
		if ($creditsize > 0) {
			$s = $c_size - $creditsize;
			if ($s <= 0) {
				$settings->warning = "You have no more credits bandwidth (bandwidth<0)";
				$s = 0;
			}
			$sql = "UPDATE users SET credit_size = '" . $s . "' WHERE id = $userid";
			$db->query($sql);
		}
		if ($creditexclu > 0) {
			$e = $c_exclu - $creditexclu;
			if ($e < 0) {
				$e = 0;
				$settings->warning = "You have no more credits exclusive ( exclu<0)";
			}
			$sql = "UPDATE users SET credit_exclu = '" . $e . "' WHERE id = $userid";
			$db->query($sql);
		}
		$db->close();
	}

	/*
 *  getUserFromId
 */
	public static function getUserFromId ($userid)
	{
		Global $settings,$user;
		if ($userid <= 0) die("user id cannot be 0");

		$db = new Database();
		$sql = "SELECT * FROM users WHERE id=" . intval($userid);
		$db->query($sql);
		
		if ($db->nbr()<=0)	 return false;
	
		$db->single();
	
		$db->is_logged = true;
		$db->is_admin = false;
		$db->is_editor = false;
		$db->is_visitor = false;
		$db->ip = $_SERVER['REMOTE_ADDR'];

		foreach ($db->rs as $key => $v) {
			$db->$key = $v;
			$db->allfields[] = $key;
		}

		if ($db->is_admin && $settings->log_as == 0) $db->rank = 14;
		if ($db->patron > 0 && $db->substatus==SUBCRIPTION_RUNNING) {
			if ($db->patron>$db->rank) {$db->realrank = $db->rank;$db->rank = $db->patron;}
			else {$db->realrank = $db->rank;}
		}
		if ($db->db->rs['type'] == TYPE_ADMIN) {
			$db->is_admin = true;
			if ($settings->log_as <> 0) {
				$db->id = $settings->log_as;
				$db->is_admin = $admin = true;
			}
		}
		if ($db->type == TYPE_EDITOR) {
			$db->is_admin = false;
			$db->is_editor = true;
		}
		if ($db->random)  getRandomName (true);
		else 	$db->fullname = ucwords($db->firstname . " " . $db->lastname);
		if ($db->random && !$user->is_admin && !$user->is_editor)  $name1=getRandomName (true);
		else $db->fullname = $db->rs['public_nickname'];
		if ($db->anonymous && !$user->is_admin && !$user->is_editor)  $name1="Anon";
		else $db->fullname = $db->rs['public_nickname'];


		return $db;
	}

	/**
	 * getNickNameFromIdClean()
	 *
	 * @return
	 */
	public static function getNickNameFromIdClean ($id, $hint = true)
	{
		global $settings, $user;

		if (($user->rank < $settings->rank_see_names) || !$user->is_logged) {

			if (rand(1, 2) == 0) $name1 = "Unity_" . rand(0, $id+1000);
			else if (rand(1, 2) == 1) $name1 = "Unity3d_" . rand(0, $id+1000);
			else $name1 = "UnityAsset_" . rand(0, $id+1000);
			return $name1;

		}
		// memoize
		static $cache_getNickNameFromIdClean;
		$key = md5(serialize(func_get_args()));
		if (!$cache_getNickNameFromIdClean[$key]) {
			$db = new Database;
			$sql = "SELECT * from users WHERE id='$id'";
			$db->query($sql);
			$db->single();
			if ($db->random && !$user->is_admin && !$user->is_editor)  $name1=getRandomName (true);
			else if (!$db->random && $db->anonymous && !$user->is_admin && !$user->is_editor)  $name1="Anon";
			else $name1 = $db->rs['public_nickname'];
			$type = $db->rs['type'];
			$is_locked = $db->rs['is_locked'];
			$is_silenced = $db->rs['is_silenced'];
			$userid = $db->rs['user_id'];
			$is_stopped = $db->rs['is_stopped'];
			$rank = $db->Record['rank'];
			$patron = $db->Record['patron'];
			if ($patron > 0 && $rank < $patron) $rank = $patron;

			if ($is_locked == 1) {
				$name1 .= "(<img src='/img/banned.png'>)";
				$result = $name1;
				$cache_getNickNameFromIdClean[$key] = $result;
				$db->close();
				return $result;
			}
			if ($is_stopped === 1) {
				$name1 .= "(<img src='/img/stopped.png'>)";
				$result = $name1;
				$cache_getNickNameFromIdClean[$key] = $result;
				$db->close();
				return $result;
			}
			if ($is_silenced === 1) {
				$name1 .= "(<img src='/img/shut.png'>)";
				$result = $name1;
				$cache_getNickNameFromIdClean[$key] = $result;
				$db->close();
				return $result;
			}
			$name1 = ucwords($name1);

			if ($name1 == "" || ($user->rank < $settings->rank_see_names && $user->id <> $id)) {
				if (rand(1, 2) == 0) $result = "Unity_" . $userid;
				else if (rand(1, 2) == 1) $result = "Unity3d_" . $userid;
				else $result = "UnityAsset_" . $userid;

				$result = $name1;
			} else {

				$result = $name1;
			}
			$db->close();
			$cache_getNickNameFromIdClean[$key] = $result;
		}

		return $cache_getNickNameFromIdClean[$key];

	}

	/**
	 * getNickNameFromIdExt()
	 *
	 * @return
	 */
	public static function getNickNameFromIdExt ($id, $nolink = false, $hint = true, $ob = true)
	{
		global $settings, $user;

		if (($user->rank < $settings->rank_see_names) || !$user->is_logged) {

			if ($ob == true) {
				$name1 = "";
				if (rand(1, 2) == 0) $name1 = "Unity_" . rand(0, $id+1000);
				else if (rand(1, 2) == 1) $name1 = "Unity3d_" . rand(0, $id+1000);
				else $name1 = "UnityAsset_" . rand(0, $id+1000);
				return $name1;
			}

		}

		// memoize
		static $cache_getNickNameFromIdExt;
		$key = md5(serialize("getNickNameFromIdExt" . $id . $nolink . $hint . $ob));
		if (!$cache_getNickNameFromIdExt[$key]) {

			$db = new Database;

			$sql = "SELECT * from users WHERE id='$id'";
			$db->query($sql);
			$db->singleRecord();

			if ($db->rs['random'] && !$user->is_admin && !$user->is_editor)  $name1="<tag>".getRandomName (true)."</tag>";
			else if (!$db->rs['random'] && $db->rs['anonymous'] && !$user->is_admin && !$user->is_editor)  $name1="Anon";
			else $name1 = $db->rs['public_nickname'];

			$is_locked = $db->rs['is_locked'];
			$userid = $db->rs['user_id'];
			$is_stopped = $db->rs['is_stopped'];
			$is_silenced = $db->rs['is_silenced'];
			$nbrthanks = $db->rs['thanks'];
			$rank = $db->rs['rank'];
			$patron = $db->rs['patron'];
			if ($patron > 0 && $rank < $patron) $rank = $patron;
			$type = $db->rs['type'];
			$score = $db->rs['score'];
			$donation = $db->rs['donation'];
			//$levelstatus = $db->Record['levelstatus'];

			if ($is_locked == 1) {
				$name1 .= "(<img src='/img/banned.png'>)";
				$result = $name1;
				if (strpos("</tag>",$result)!==false) {
					$result=replace_between($result,"<tag>","</tag>",getRandomName (true));
					$result=str_replace("<tag>","",$result);
					$result=str_replace("</tag>","",$result);
				}
				$cache_getNickNameFromIdExt[$key] = $result;
				return $result;
			}
			if ($is_stopped == 1) {
				$name1 .= "(<img src='/img/stopped.png'>)";
				$result = $name1;
				if (strpos("</tag>",$result)!==false) {
					$result=replace_between($result,"<tag>","</tag>",getRandomName (true));
					$result=str_replace("<tag>","",$result);
					$result=str_replace("</tag>","",$result);
				}
				$cache_getNickNameFromIdExt[$key] = $result;
				return $result;
			}
			if ($is_silenced == 1) {
				$name1 .= "(<img src='/img/shut.png'>)";
				$result = $name1;
				if (strpos("</tag>",$result)!==false) {
					$result=replace_between($result,"<tag>","</tag>",getRandomName (true));
					$result=str_replace("<tag>","",$result);
					$result=str_replace("</tag>","",$result);
				}
				$cache_getNickNameFromIdExt[$key] = $result;
				return $result;
			}

			if ($type == 1) $rank = 14;
			//if ($type == 2) $rank = 13;

			if ($name1 == "" || ($user->rank < $settings->rank_see_names)) {

				if (rand(1, 2) == 0) $name1 = "Unity_" . $userid;
				else if (rand(1, 2) == 1) $name1 = "Unity3d_" . $userid;
				else $name1 = "UnityAsset_" . $userid;
			}
			if ($nolink == false)
				$name1 = "<a target='_parent'  href='/users.php?id=" . $id . "'>" . ucwords($name1) . "</a>";
			else        $name1 = ucwords($name1);

			$away = (($rank + 1) * $user->score_interval) - $score;

			if ($nbrthanks == "") $nbrthanks = "0";
			if ($user->id == $userid) {

				$thanksx = ' <span class="label-new hint--right" data-hint="Your Score is ' . ($score ?: "0") . '. Your rank is ' . ($rank ?: "0") . '. and you are ' . ($away ?: "0") .
					' away from the next Rank" >';
			} else {
				$thanksx = ' <span class="label-new hint--right" data-hint="Score ' . ($score ?: "0") . ', Rank: ' . ($rank ?: "0") . '" >';
			}
			if ($id <> 1) $thanksx .= "(" . ($score ?: "0") . ")";
			$thanksx .= "</span>";
			//}

			$r = "";
			if ($user->rank >= $settings->rank_see_names) {
				$r = returnDisplayRank($rank, 16, false, "", $hint); //" align=left "
			}
			$levelstatus = 0;
			if ($type == 0 && $donation == 0 && $rank == 0) $levelstatus = FREE;
			if ($type == 0 && $donation == 0 && $rank > 0) $levelstatus = SUPPORTER;
			if ($type == 0 && $donation > 0 && $rank > 0) $levelstatus = NOBRAINER;
			if ($type == 1) $levelstatus = ADMIN;
			if ($type == 2) $levelstatus = EDITOR;

			if ($levelstatus == FREE || !$user->is_logged) $result = "<i class='splashy-smiley_amused' ></i>" . $name1;
			$result = $name1 . $thanksx;
			if ($levelstatus == SUPPORTER) $result = "<i class='splashy-contact_grey hint--right' data-hint='Supporter Donator Status'></i>" . $name1 . $thanksx;
			if ($levelstatus == NOBRAINER) $result = "<i class='splashy-star_boxed_full hint--right' data-hint='No-Brainder Donator'></i>" . $name1 . $thanksx;
			if ($levelstatus == EDITOR) $result = "<i class='splashy-check hint--right' data-hint='Moderator Status'></i>" . $name1 . $thanksx;
			if ($levelstatus == ADMIN) $result = "<i class='splashy-shield_chevrons hint--right' data-hint='oh! an Admin!'></i>" . $name1 . $thanksx;
			if ($id <> $user->id) $result .= "<img  src='/img/tip.png' type='button' data-toggle='modal' data-user-id='" . $id . "' data-user-nickname='" . $db->Record['public_nickname'] .
				"' data-target='#modal_tip' class='hint--right' data-hint='Give a tip to this member'  style='margin-bottom:8px;margin-left:2px;'>";
			$result = $r . $result;
			$cache_getNickNameFromIdExt[$key] = $result;
		}
		$r=$cache_getNickNameFromIdExt[$key];
		if (strpos("</tag>",$r)!==false) {
			$r=replace_between($r,"<tag>","</tag>",getRandomName (true));
			$r=str_replace("<tag>","",$r);
			$r=str_replace("</tag>","",$r);
		}
		return $r;

	}

	/*
    * Generate a random name
    */

	/**
	 * getSession()
	 *
	 * @return
	 */
	function getSession ($v)
	{
		if (isset($_SESSION[$v])) {
			return $_SESSION[$v];
		} else {
			if (isset($_COOKIE[$v])) {
				return $_COOKIE[$v];
			} else {
				if (isset($_REQUEST[$v])) {
					return $_REQUEST[$v];
				}

			}
		}
		return false;
	}

	/**
	 * @return bool|int
	 */
	function refresh ()
	{

		if ($this->id <= 0) return ERROR_LOGIN;
		$db= new Database();
		// affect all the variables
		$sql = "SELECT * FROM users WHERE id=" . $this->id;
		//if ($this->id==1) echo $sql;
		$db->query($sql);
		//if ($this->id==1) echo "<br>nbr=".$db->nbr()."<br>";
		if ($db->nbr() >= 1) {
			$db->single();
			foreach ($db->rs as $key => $v) {
				$this->$key = $v;
				$this->allfields[] = $key;
				//if ($this->id==1) {
				//	echo $v."-".$key."<br>";
				//}
			}
		}
		if ($this->patron > 0 && $this->rank < $this->patron) $this->rank = $this->patron;
		switch ($this->type) {
			case TYPE_ADMIN:
				$this->is_admin = true;
				$this->is_editor = true;

				break;
			case TYPE_EDITOR:
				$this->is_editor = true;
				$this->is_admin = false;

				break;
			default:
				$this->is_admin = false;
				$this->is_editor = false;
				break;
		}
		$this->saveSession();

		$db->close();
		return true;
	}

	/**
	 * @param $r
	 * @param $s
	 *
	 * @return int
	 */
	function updateRow ($r, $s)
	{
		if ($this->id <= 0) return ERROR_LOGIN;
		$sql = "UPDATE users  SET " . $r . "='" . $s . "' WHERE id=" . $this->id;;
		$this->db->query($sql);
	}

	/**
	 *
	 */
	function __destruct ()
	{
		$this->db->close();
	}

	/**
	 * getUserName()
	 *
	 * @return
	 */
	function getUserName ()
	{
		Global $settings;
		if ($this->id <= 0) return ERROR_LOGIN;

		$name = $this->db->rs['name_f'] . " " . $this->db->rs['name_l'];
		// $name=$db->Record['login'];
		if ($this->id == 1 || (!$this->is_visitor) || $this->id == $this->id || $this->rank > 0)
			if ($this->genname && $this->rank >= $settings->rank_anon) $result = ucfirst($this->genName(10)); else $result = ucwords($name);
		else
			$result = "Anon";

		return $result;
	}

	/**
	 * @param int  $length
	 * @param bool $lower_case
	 * @param bool $ucfirst
	 * @param bool $upper_case
	 *
	 * @return string
	 */
	function genName ($length = 5, $lower_case = true, $ucfirst = true, $upper_case = false)
	{

		$done = false;
		$const_or_vowel = 1;
		$word = "";

		$vowels = ['a', 'e', 'i', 'o', 'u', 'y'];

		$consonants = [
			'b', 'c', 'd', 'f', 'g', 'h',
			'j', 'k', 'l', 'm', 'n', 'p',
			'r', 's', 't', 'v', 'w', 'z',
			'ch', 'qu', 'th', 'xy'
		];
		$i = 1;
		while (!$done) {
			switch ($const_or_vowel) {
				case 1:
					$word .= $consonants[($this->id + 21 * 39 * $i * rand(1, 1000)) % sizeof($consonants)];
					$const_or_vowel = 2;
					break;
				case 2:
					$word .= $vowels[($this->id + 21 * 39 * $i * rand(1, 1000)) % sizeof($vowels)];
					$const_or_vowel = 1;
					break;
			}

			if (strlen($word) >= $length) {
				$done = true;
			}
			$i++;
		}

		$word = substr($word, 0, $length);

		if ($lower_case) {
			$word = strtolower($word);
		} else if ($ucfirst) {
			$word = ucfirst(strtolower($word));
		} else if ($upper_case) {
			$word = strtoupper($word);
		}
		return $word;
	}


	/**
	 * @param bool $force
	 *
	 * @return int
	 */
	function reload ($force = false)
	{
		if ($this->id == 0 || $this->id == "") {
			$this->logout();
			return ERROR_LOGIN;
		}

		if ($this->reload_time < time() || $force) {
			$this->reload_time = time() + 600;

			$db = new Database();
			$sql = "SELECT * FROM users WHERE id=" . $this->id;
			$db->query($sql);
			$db->single();

			foreach ($db->rs as $key => $v) {
				$this->$key = $v;

			}
			if ($this->patron > 0 && $this->rank < $this->patron) $this->rank = $this->patron;
			switch ($this->type) {
				case TYPE_ADMIN:
					$this->is_admin = true;
					$this->is_editor = true;
					$this->is_visitor = false;
					break;
				case TYPE_EDITOR:
					$this->is_editor = true;
					$this->is_admin = false;
					$this->is_visitor = false;
					break;
				default:
					$this->is_admin = false;
					$this->is_editor = false;
					$this->is_visitor = true;
					break;
			}
			$this->calculateCredits();
			$this->saveSession();
			$db->close();
		}
	}

	/**
	 *
	 */
	function update ()
	{
		$table = "users";
		$col = "";
		$obj = $this->allfields;
		foreach ($obj as $key => $value) {
			if (!is_object($value)) {
				$col .= " {$key} = '{$value}',";
			}
		}
		$col[strlen($col) - 1] = " ";
		$sql = "UPDATE {$table} SET {$col} WHERE id = '" . $this->id . "'";
		$this->db->query($sql);

	}



	/**
	 * addCreditUser()
	 *
	 * @return
	 */
	function addCredit ($creditfile, $creditsize, $creditexclu = 1)
	{
		global $warning, $settings;

		$db=new Database();
		$c_size = abs($this->credit_size);
		$c_file = abs($this->credit_file);
		$c_exclu = abs($this->credit_exclu);

		$f = $c_file + $creditfile;
		$s = $c_size + $creditsize;
		$e = $c_exclu + $creditexclu;
		if ($e > $settings->limit_credit_exclu) $e = $settings->limit_credit_exclu;
		if ($e >= $settings->limit_credit_exclu) {
			$warning = "You have reached your maximum Exclu Credit (" . $settings->limit_credit_exclu . ")";
		}

		if ($f > $settings->limit_credit_file) {
			$warning = "Credit limit of " . $settings->limit_credit_file . " reached";
		}
		if ($s > intval($settings->limit_credit_bandwidth / 1024)) {
			$warning = "Credit limit of " . $settings->limit_credit_bandwidth . " reached";
		}

		if ($f > $settings->limit_credit_file) $f = $settings->limit_credit_file;
		if ($s > $settings->limit_credit_bandwidth) $s = $settings->limit_credit_bandwidth;
		if ($f < 0) $f = 0;
		if ($s < 0) $s = 0;
		if ($e < 0) $e = 0;

		$this->credit_file = $f;
		$this->credit_size = $s;
		$this->credit_exclu = $e;

		$sql = "UPDATE users SET credit_file = '" . $f . "' WHERE id = ".$this->id;
		$db->query($sql);
		$sql = "UPDATE users SET credit_size = '" . $s . "' WHERE id = ".$this->id;
		$db->query($sql);
		$sql = "UPDATE users SET credit_exclu = '" . $e .  "' WHERE id = ".$this->id;
		$db->query($sql);

		$this->calculateCredits();
		$this->saveSession();
		$this->reload(true);
		$db->close();

	}


	/**
	 * @param $creditfile
	 * @param $creditsize
	 * @param $creditexclu
	 */
	function subCredit ($creditfile, $creditsize, $creditexclu)
	{
		global $settings;

		$db=new Database();

		$c_size = abs($this->credit_size);
		$c_file = abs($this->credit_file);
		$c_exclu = abs($this->credit_exclu);
		if ($creditfile > 0) {
			$f_total = ($c_file + $this->dailyfile) - $this->nbroffileused - $creditfile;
			if ($f_total < 0) {
				$settings->warning = "You have no more credit files (files<0)";
			} else {
				$f_daily = ($this->dailyfile) - $this->nbroffileused - $creditfile;
				if ($f_daily < 0) {
					$f = $c_file - $creditfile;

					if ($f < 0) $f = 0;

					$this->subcreditfile = $creditfile;
					$this->subcreditfileleft = $f;
					$this->credit_file = $f;

					$sql = "UPDATE users SET credit_file = '" . $f . "' WHERE id = ".$this->id;
					$db->query($sql);
				}
			}

		}
		if ($creditsize > 0) {
			// check if all credits gone
			$s_total = ($c_size + $this->dailybandwidth) - $this->bandwidthused - $creditsize;
			if ($s_total < 0) {
				$settings->warning = "You have no more credits bandwidth (bandwidth<0)";
			} else {
				// check if daily enough
				$s_daily = ($this->dailybandwidth) - $this->bandwidthused - $creditsize;
				if ($s_daily < 0) {
					// we need to take from the collected credits
					$s = $c_size - $creditsize;
					if ($s < 0) $s = 0;

					$this->subcreditsize = $creditsize;
					$this->subcreditsizeleft = $s;
					$this->credit_size = $s;
					$sql = "UPDATE users SET credit_size = '" . $s . "' WHERE id = ".$this->id;
					$db->query($sql);

				}
			}

		}
		if ($creditexclu > 0) {
			$e_total = ($c_exclu + $this->dailyexclu) - $this->nbrofexcluused - $creditexclu;
			//echo "e_total=".$e_total."<br>";
			if ($e_total < 0) {
				$settings->warning = "You have no more credits exclusive ( exclu<0)";
			} else {
				$e_daily = ($this->dailyexclu) - $this->nbrofexcluused - $creditexclu;
				//echo "e_daily=".$e_total."<br>";
				if ($e_daily < 0) {
					$e = $c_exclu - $creditexclu;
					if ($e < 0) $e = 0;

					$this->subcreditexclu = $creditexclu;
					$this->subcreditexcluleft = $e;
					$this->credit_exclu = $e;
					$sql = "UPDATE users SET credit_exclu = '" . $e .  "' WHERE id = ".$this->id;
					$db->query($sql);
					//echo "<br>"."subcreditexclu=".$this->subcreditexclu;
					//echo "<br>"."subcreditexcluleft=".$this->subcreditexcluleft;
					//echo "<br>"."credit_exclu=".$this->credit_exclu;


				}
			}
		}

		$this->calculateCredits();
		$this->saveSession();
		$this->reload(true);
		$db->close();
	}

	/**
	 * @param $creditfile
	 * @param $creditsize
	 * @param $creditexclu
	 */
	function subCreditPermanent ($creditfile, $creditsize, $creditexclu)
	{
		global $settings;

		$db=new Database();

		$c_size = $this->credit_size;
		$c_file = $this->credit_file;
		$c_exclu = $this->credit_exclu;

		if ($creditfile > 0) {
			$f = $c_file - $creditfile;
			if ($f <= 0) {
				$settings->warning = "You have no more credit files (files<0)";
				$f = 0;
				$this->credit_file = $f;
				$sql = "UPDATE users SET credit_file = '" . $f . "' WHERE id = ".$this->id;
				$db->query($sql);
			}
		}
		if ($creditsize > 0) {
			$s = $c_size - $creditsize;
			if ($s <= 0) {
				$settings->warning = "You have no more credits bandwidth (bandwidth<0)";
				$s = 0;
			}
			$this->credit_size = $s;
			$sql = "UPDATE users SET credit_size = '" . $s . "' WHERE id = ".$this->id;
			$db->query($sql);
		}
		if ($creditexclu > 0) {
			$e = $c_exclu - $creditexclu;
			if ($e < 0) {
				$e = 0;
				$settings->warning = "You have no more credits exclusive ( exclu<0)";
			}
			$this->credit_exclu = $e;
			$sql = "UPDATE users SET credit_exclu = '" . $e .  "' WHERE id = ".$this->id;
			$db->query($sql);

		}
		$this->calculateCredits();
		$this->saveSession();
		$this->reload(true);
		$db->close();
	}


	/**
	 * @param $creditfile
	 * @param $creditsize
	 * @param $creditexclu
	 */
	function addCreditPermanent ($creditfile, $creditsize, $creditexclu)
	{
		global $settings;

		$db=new Database();

		$c_size = $this->credit_size;
		$c_file = $this->credit_file;
		$c_exclu = $this->credit_exclu;

		if ($creditfile > 0) {
			$f = $c_file + $creditfile;
			if ($f <= 0) {
				$settings->warning = "You have no more credit files (files<0)";
				$f = 0;
				$this->credit_file = $f;
				$sql = "UPDATE users SET credit_file = '" . $f . "' WHERE id = ".$this->id;
				$db->query($sql);
			}
		}
		if ($creditsize > 0) {
			$s = $c_size + $creditsize;
			if ($s <= 0) {
				$settings->warning = "You have no more credits bandwidth (bandwidth<0)";
				$s = 0;
			}
			$this->credit_size = $s;
			$sql = "UPDATE users SET credit_size = '" . $s . "' WHERE id = ".$this->id;
			$db->query($sql);
		}
		if ($creditexclu > 0) {
			$e = $c_exclu + $creditexclu;
			if ($e < 0) {
				$e = 0;
				$settings->warning = "You have no more credits exclusive ( exclu<0)";
			}
			$this->credit_exclu = $e;
			$sql = "UPDATE users SET credit_exclu = '" . $e .  "' WHERE id = ".$this->id;
			$db->query($sql);

		}
		$this->calculateCredits();
		$this->saveSession();
		$this->reload(true);
		$db->close();
	}


	/**
	 * getNickNameFromIdClean()
	 *
	 * @return
	 */
	function getNickNameClean ($hint = true)
	{
		global $settings;

		if (($this->rank < $settings->rank_see_names) || !$this->is_logged) {
			if (rand(1, 2) == 0) $name1 = "Unity_" . rand(0, $this->id);
			else if (rand(1, 2) == 1) $name1 = "Unity3d_" . rand(0, $this->id);
			else $name1 = "UnityAsset_" . rand(0, $this->id);
			return $name1;
		}
		// memoize
		static $cache_getNickNameClean;
		$key = md5(serialize(func_get_args()));
		if (!$cache_getNickNameClean[$key]) {
			$name1 = $this->public_nickname;
			if ($this->is_locked == 1) {
				$name1 .= "(<img src='/img/banned.png'>)";
				$result = $name1;
				$cache_getNickNameClean[$key] = $result;
				return $result;
			}
			if ($this->is_stopped === 1) {
				$name1 .= "(<img src='/img/stopped.png'>)";
				$result = $name1;
				$cache_getNickNameClean[$key] = $result;
				return $result;
			}
			if ($this->is_silenced === 1) {
				$name1 .= "(<img src='/img/shut.png'>)";
				$result = $name1;
				$cache_getNickNameClean[$key] = $result;
				return $result;
			}
			$name1 = ucwords($name1);
			if ($name1 == "" || ($this->rank < $settings->rank_see_names)) {
				if (rand(1, 2) == 0) $result = "Unity_" . $this->id;
				else if (rand(1, 2) == 1) $result = "Unity3d_" . $this->id;
				else $result = "UnityAsset_" . $this->id;

				$result = $name1;
			} else {
				$result = $name1;
			}
			$cache_getNickNameClean[$key] = $result;
		}
		return $cache_getNickNameClean[$key];

	}

	/**
	 * getNickNameFromIdExt()
	 *
	 * @return
	 */
	function getNickNameExt ($nolink = false, $hint = true, $ob = true)
	{
		global $settings;
		$name1 = "";
		if (($this->rank < $settings->rank_see_names) || !$this->is_logged) {

			if ($ob == true) {
				$name1 = "";
				if (rand(1, 2) == 0) $name1 = "Unity_" . rand(0, $this->id);
				else if (rand(1, 2) == 1) $name1 = "Unity3d_" . rand(0, $this->id);
				else $name1 = "UnityAsset_" . rand(0, $this->id);
				return $name1;
			}

		}
		// memoize
		static $cache_getNickNameExt;
		$key = md5(serialize("getNickNameFromIdExt" . $this->id . $nolink . $hint));
		if (!$cache_getNickNameExt[$key] && !$settings->genname) {
			if ($settings->genname) $name1 = genName($this->id, 10); // generate random name
			if ($this->is_locked == 1) {
				$name1 .= "(<img src='/img/banned.png'>)";
				$result = $name1;
				$cache_getNickNameExt[$key] = $result;
				return $result;
			}
			if ($this->is_stopped == 1) {
				$name1 .= "(<img src='/img/stopped.png'>)";
				$result = $name1;
				$cache_getNickNameExt[$key] = $result;
				return $result;
			}
			if ($this->is_silenced == 1) {
				$name1 .= "(<img src='/img/shut.png'>)";
				$result = $name1;
				$cache_getNickNameExt[$key] = $result;
				return $result;
			}
			if ($this->type == TYPE_ADMIN) $rank = 14;
			if ($name1 == "" || ($this->rank < $settings->rank_see_names)) {
				if (rand(1, 2) == 0) $name1 = "Unity_" . $this->id;
				else if (rand(1, 2) == 1) $name1 = "Unity3d_" . $this->id;
				else $name1 = "UnityAsset_" . $this->id;
			}
			if ($nolink == false)
				$name1 = "<a target='_parent'  href='/users.php?id=" . $this->id . "'>" . ucwords($name1) . "</a>";
			else        $name1 = ucwords($name1);

			$away = (($this->rank + 1) * $settings->score_interval) - $this->score;

			$thanksx = ' <span class="label-new hint--right" data-hint="Your Score is ' . ($this->score ?: "0") . '. Your rank is ' . ($this->rank ?: "0") . '. and you are ' . ($away ?: "0") .
				' away from the next Rank" >';

			if (!$this->is_admin) $thanksx .= "(" . ($this->score ?: "0") . ")";
			$thanksx .= "</span>";
			$r = "";
			if ($this->rank >= $settings->rank_see_names) {
				$r = returnDisplayRank($this->rank, 16, false, "", $hint); //" align=left "
			};
			if ($this->type == TYPE_VISITOR && $this->donation == 0 && $this->rank == 0 && $this->patron == 0) $this->levelstatus = FREE;
			if ($this->type == TYPE_VISITOR && $this->donation == 0 && $this->rank > 0 && $this->patron == 0) $this->levelstatus = SUPPORTER;
			if ($this->type == TYPE_VISITOR && ($this->donation > 0 || $this->patron > 00) && $this->rank > 0) $this->levelstatus = NOBRAINER;
			if ($this->type == TYPE_ADMIN) $this->levelstatus = ADMIN;
			if ($this->type == TYPE_EDITOR) $this->levelstatus = EDITOR;

			if ($this->levelstatus == FREE) $result = "<i class='splashy-smiley_amused' ></i>" . $name1;
			$result = $name1 . $thanksx;
			if ($this->levelstatus == SUPPORTER) $result = "<i class='splashy-contact_grey hint--right' data-hint='Supporter Donator Status'></i>" . $name1 . $thanksx;
			if ($this->levelstatus == NOBRAINER) $result = "<i class='splashy-star_boxed_full hint--right' data-hint='No-Brainder Donator'></i>" . $name1 . $thanksx;
			//if ($levelstatus == GOLD) $result = "<i class='splashy-shield hint--right' data-hint='Lifetime GOLD Donator!'></i>" . $name1 . $thanksx;
			if ($this->levelstatus == EDITOR) $result = "<i class='splashy-check hint--right' data-hint='Moderator Status'></i>" . $name1 . $thanksx;
			if ($this->levelstatus == ADMIN) $result = "<i class='splashy-shield_chevrons hint--right' data-hint='Hohoho an Admin!'></i>" . $name1 . $thanksx;
			if ($this->id <> $this->id) $result .= "<img  src='/img/tip.png' type='button' data-toggle='modal' data-user-id='" . $this->id . "' data-user-nickname='" . $this->public_nickname .
				"' data-target='#modal_tip' class='hint--right' data-hint='Give a tip to this member'  style='margin-bottom:8px;margin-left:2px;'>";
			$result = $r . $result;
			$cache_getNickNameExt[$key] = $result;
		}
		return $cache_getNickNameExt[$key];
	}
}

?>