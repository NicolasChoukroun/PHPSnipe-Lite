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

// IMPORTANT: Edit this file, and then copy it at the root of your project.

define('DB_USER', 'x'); // Replace your username with your database login name.
define('DB_PASSWORD', 'x'); // Replace yourpassword with yourusernames password.
define('DB_NAME', 'phpsnipe'); // Replace yourdatabase with the name of your mySQL database.
define('DB_HOST', '127.0.0.1'); // Replace yourhost with the server your database is hosted on.
define('SMTP_USER', 'support@phpsnipe'); //  username
define('SMTP_PASSWORD', 'x'); //  password
define('SMTP_HOST', 'localhost'); //  host
define('SMTP_PORT', '25'); //  host
define('SMTP_FROM', 'support@yourwebsite'); //  host
define('SMTP_FROM_NAME', 'Yourwebsite Support'); //  host

define('FSCUID', 'asl;dkjalksdjasd4a5s5d465465$@#$@#%sdfjnmsdf'); //  enter a random uid
define('ENCRYPTION_KEY','0101456sdfsdf'); // enter what you want
define('SESSION_KEY','sdfsdf7898721sdf324'); // enter what you want.


define('CACHE_PATH',$_SERVER['DOCUMENT_ROOT']."/cache/".$_SERVER['SERVER_NAME']."/");
define('CACHE_TIME',3600); // this is the cache time for the php pages
define('CACHE_TIME_W',600); // this is the cache time for widgets

define('_DEFAULT_SITENAME_', 'Demo Snipe Lite 1'); 
define('_DEFAULT_URL_', 'http://litedemo1.phpsnipe.net');  // edit with your website address
define('_DEFAULT_TITLE_', 'Demo Snipe Lite 1'); 
define('_DEFAULT_DESCRIPTION_', 'Demo Snipe Lite 1 description'); 
define('_DEFAULT_KEYWORDS_', 'demo,snipe,php,framework');
define('_DEFAULT_AUTHOR_', 'Nicolas Choukroun'); 

?>