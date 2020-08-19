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


if (!$user->is_logged ||  $user->id<=0)    	{echo '<script>window.top.location.href = "/login.php?error='. encrypt(_t("Security Credential Error Editor")). '";</script>';exit;}
if (!$user->is_admin &&  !$user->is_editor)    {echo '<script>window.top.location.href = "/403.html";</script>';exit;}

?>