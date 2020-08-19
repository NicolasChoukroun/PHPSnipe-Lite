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



// errors
define('ERROR_LOGIN', -100);
define('ERROR_PASSWORD', -101);
define('ERROR_SESSION_EXPIRED', -102);
define('ERROR_SESSION_NOTEXIST', -102);

// types
define('TYPE_ADMIN', 1);
define('TYPE_VISITOR', 0);
define('TYPE_EDITOR', 2);
define('TYPE_INVESTOR', 3);

// levels
define('ADMIN', 6);
define('FREE', 5);
define('SUPPORTER', 2);
define('UNITY3D', 2);
define('NOBRAINER', 7);
define('GOLD', 20);
define('EDITOR', 9);
define('NOBRAINER2', 10);
define('NOBRAINER3', 12);
define('VST', 16);
define('MOVIES', 17);
define('POSER', 18);
define('EMU', 19);

define('NEWBIES', 4);
define('ADMINISTRATOR', 1);
define('GLOBALMODERATOR', 2);
define('MODERATOR', 3);
define('MEMBER', 5);
define('CONTRIBUTOR', 7);
define('SENIOR', 8);
define('SPECIALIST', 9);
define('GODOFTALKNG', 10);
define('DONATORLEVEL1', 6);
define('DONATORLEVEL2', 11);
define('DONATORLEVEL3', 12);
define('DONATORLEVEL4', 13);
define('BANNED', 14);

// global  action logs
define("ACTION_DOWNLOAD", 1);
define("ACTION_UPLOAD", 2);
define("ACTION_UPDATE_AUTOGRAB", 3);
define("ACTION_UPDATE_PICTURE", 4);
define("ACTION_RECATEGORIZE", 5);
define("ACTION_UPDATE_APPROVED", 6);
define("ACTION_NEW_APPROVED", 11);
define("ACTION_REJECTED", 7);
define("ACTION_UPDATE_GENERAL", 8);
define("ACTION_DELETED", 9);
define("ACTION_RESTORED", 10);
define("ACTION_INVITE", 12);
define("ACTION_WIN_REWARD", 14);

// global notifications
define("NOTIFY_RECEIVED_MONEY", 2);
define("NOTIFY_SENT_MONEY", 1);
define("NOTIFY_WISHLIST_UPDATE", 3);
define("NOTIFY_COMMENT_REVIEW", 4);
define("NOTIFY_REWARD_REDEEMED", 5);
define("NOTIFY_REWARD_COMPENSED", 6);
define("NOTIFY_UPLOAD_REJECTED",7);
define("NOTIFY_UPLOAD_APPROVED",8);
define("NOTIFY_REVIEW_REJECTED",9);
define("NOTIFY_REVIEW_APPROVED",10);
define("NOTIFY_UPLOAD_RESTORED",11);
define("NOTIFY_REVIEW_RESTORED",12);
define("NOTIFY_FORUM_REPLY",13);

// substatus
define("SUBCRIPTION_NONE",0);
define("SUBCRIPTION_RUNNING",1);
define("SUBCRIPTION_EXPIRED",2);

//
define('SECONDS_IN_DAY', 86400);
define('SECONDS_IN_MONTH', 86400 * 30);
define('SECONDS_IN_YEAR', 86400 * 30 * 5 + 86400 * 31 * 6 + 86400 * 28);

define("COOKIE_EXPIRATION",864000000); // cookies send for infinity
define("SESSION_TIMEOUT",86400); // cookies send for infinity


//clear_duplicate_cookies();

