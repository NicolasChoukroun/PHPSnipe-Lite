<?php/*
 *  Copyright (c) 2013-2020. Nicolas Choukroun.
 *  Copyright (c) 2013-2020. The PHPSnipe Developers.
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the Attribution 4.0 International License as published by the
 *  Creative Commons Corporation; either version 2 of the License, or (at your option)
 *  any later version.  See COPYING for more details.
 *
 ******************************************************************************/ 
?>
<!-- - - - - - - - - - - - - - Alerts - - - - - - - - - - - - - - - - -->


	<li class="dropdown nav-item">
		<a data-toggle="dropdown" class="dropdown-toggle" href="#"> <?php echo _t("Languages");?>: <b class="caret"></b></a>

		<ul class="dropdown-menu " style="background:#fff">


			<?php
			//echo "l=".$settings->language;
			$scr="profile.php";
			switch ($settings->language) {
				case "th":
					echo "<li><a href='/".$scr."?language=us'><img class='language-icons'  style='right: 400px;' src='/img/us.png'>".ucwords(_t(" English"))."</a></li>";
					echo "<li><a href='/".$scr."?language=cn'><img class='language-icons'  style='right: 350px;' src='/img/cn.png'>".ucwords(_t(" Chinese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=ru'><img class='language-icons'  style='right: 300px;' src='/img/ru.png'>".ucwords(_t(" Russian"))."</a></li>";
					echo "<li><a href='/".$scr."?language=jp'><img class='language-icons'  style='right: 250px;' src='/img/jp.png'>".ucwords(_t(" Japanese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=fr'><img class='language-icons'  style='right: 200px;' src='/img/fr.png'>".ucwords(_t(" French"))."</a></li>";
					echo "<li><a href='/".$scr."?language=sp'><img class='language-icons'  style='right: 150px;' src='/img/sp.png'>".ucwords(_t(" Spanish"))."</a></li>";
					echo "<li><a href='/".$scr."?language=po'><img class='language-icons'  style='right: 100px;' src='/img/po.png'>".ucwords(_t(" Portuguese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=th'><img class='language-icons selected'  style='right: 50px;background-color: #dddd00;' src='/img/th.png'>".ucwords(_t(" Thai"))."</a></li>";
					break;
				case "po":
					echo "<li><a href='/".$scr."?language=us'><img class='language-icons'  style='right: 400px;' src='/img/us.png'>".ucwords(_t(" English"))."</a></li>";
					echo "<li><a href='/".$scr."?language=cn'><img class='language-icons'  style='right: 350px;' src='/img/cn.png'>".ucwords(_t(" Chinese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=ru'><img class='language-icons'  style='right: 300px;' src='/img/ru.png'>".ucwords(_t(" Russian"))."</a></li>";
					echo "<li><a href='/".$scr."?language=jp'><img class='language-icons'  style='right: 250px;' src='/img/jp.png'>".ucwords(_t(" Japanese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=fr'><img class='language-icons'  style='right: 200px;' src='/img/fr.png'>".ucwords(_t(" French"))."</a></li>";
					echo "<li><a href='/".$scr."?language=sp'><img class='language-icons'  style='right: 150px;' src='/img/sp.png'>".ucwords(_t(" Spanish"))."</a></li>";
					echo "<li><a href='/".$scr."?language=po'><img class='language-icons selected'  style='right: 100px;background-color: #dddd00;' src='/img/po.png'>".ucwords(_t(" Portuguese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=th'><img class='language-icons'  style='right: 50px;' src='/img/th.png'>".ucwords(_t(" Thai"))."</a></li>";
					break;
				case "sp":
					echo "<li><a href='/".$scr."?language=us'><img class='language-icons'  style='right: 400px;' src='/img/us.png'>".ucwords(_t(" English"))."</a></li>";
					echo "<li><a href='/".$scr."?language=cn'><img class='language-icons'  style='right: 350px;' src='/img/cn.png'>".ucwords(_t(" Chinese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=ru'><img class='language-icons'  style='right: 300px;' src='/img/ru.png'>".ucwords(_t(" Russian"))."</a></li>";
					echo "<li><a href='/".$scr."?language=jp'><img class='language-icons'  style='right: 250px;' src='/img/jp.png'>".ucwords(_t(" Japanese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=fr'><img class='language-icons'  style='right: 200px;' src='/img/fr.png'>".ucwords(_t(" French"))."</a></li>";
					echo "<li><a href='/".$scr."?language=sp'><img class='language-icons selected'  style='right: 150px;background-color: #dddd00;'  src='/img/sp.png'>".ucwords(_t(" Spanish"))."</a></li>";
					echo "<li><a href='/".$scr."?language=po'><img class='language-icons'  style='right: 100px;' src='/img/po.png'>".ucwords(_t(" Portuguese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=th'><img class='language-icons'  style='right: 50px;' src='/img/th.png'>".ucwords(_t(" Thai"))."</a></li>";
					break;
				case "fr":
					echo "<li><a href='/".$scr."?language=us'><img class='language-icons'  style='right: 400px;' src='/img/us.png'>".ucwords(_t(" English"))."</a></li>";
					echo "<li><a href='/".$scr."?language=cn'><img class='language-icons'  style='right: 350px;' src='/img/cn.png'>".ucwords(_t(" Chinese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=ru'><img class='language-icons'  style='right: 300px;' src='/img/ru.png'>".ucwords(_t(" Russian"))."</a></li>";
					echo "<li><a href='/".$scr."?language=jp'><img class='language-icons'  style='right: 250px;' src='/img/jp.png'>".ucwords(_t(" Japanese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=fr'><img class='language-icons selected'  style='right: 200px;background-color: #dddd00;' src='/img/fr.png'>".ucwords(_t(" French"))."</a></li>";
					echo "<li><a href='/".$scr."?language=sp'><img class='language-icons'  style='right: 150px;' src='/img/sp.png'>".ucwords(_t(" Spanish"))."</a></li>";
					echo "<li><a href='/".$scr."?language=po'><img class='language-icons'  style='right: 100px;' src='/img/po.png'>".ucwords(_t(" Portuguese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=th'><img class='language-icons'  style='right: 50px;' src='/img/th.png'>".ucwords(_t(" Thai"))."</a></li>";
					break;
				case "en":
					echo "<li><a href='/".$scr."?language=us'><img class='language-icons selected'  style='right: 400px;background-color: #dddd00;' src='/img/us.png'>".ucwords(_t(" English"))."</a></li>";
					echo "<li><a href='/".$scr."?language=cn'><img class='language-icons'  style='right: 350px;' src='/img/cn.png'>".ucwords(_t(" Chinese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=ru'><img class='language-icons'  style='right: 300px;' src='/img/ru.png'>".ucwords(_t(" Russian"))."</a></li>";
					echo "<li><a href='/".$scr."?language=jp'><img class='language-icons'  style='right: 250px;' src='/img/jp.png'>".ucwords(_t(" Japanese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=fr'><img class='language-icons'  style='right: 200px;' src='/img/fr.png'>".ucwords(_t(" French"))."</a></li>";
					echo "<li><a href='/".$scr."?language=sp'><img class='language-icons'  style='right: 150px;' src='/img/sp.png'>".ucwords(_t(" Spanish"))."</a></li>";
					echo "<li><a href='/".$scr."?language=po'><img class='language-icons'  style='right: 100px;' src='/img/po.png'>".ucwords(_t(" Portuguese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=th'><img class='language-icons'  style='right: 50px;' src='/img/th.png'>".ucwords(_t(" Thai"))."</a></li>";
					break;
				case "ru":
					echo "<li><a href='/".$scr."?language=us'><img class='language-icons'  style='right: 400px;' src='/img/us.png'>".ucwords(_t(" English"))."</a></li>";
					echo "<li><a href='/".$scr."?language=cn'><img class='language-icons'  style='right: 350px;' src='/img/cn.png'>".ucwords(_t(" Chinese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=ru'><img class='language-icons'  style='right: 300px; background-color: #dddd00;' src='/img/ru.png'>".ucwords(_t(" Russian"))."</a></li>";
					echo "<li><a href='/".$scr."?language=jp'><img class='language-icons'  style='right: 250px;' src='/img/jp.png'>".ucwords(_t(" Japanese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=fr'><img class='language-icons'  style='right: 200px;' src='/img/fr.png'>".ucwords(_t(" French"))."</a></li>";
					echo "<li><a href='/".$scr."?language=sp'><img class='language-icons'  style='right: 150px;' src='/img/sp.png'>".ucwords(_t(" Spanish"))."</a></li>";
					echo "<li><a href='/".$scr."?language=po'><img class='language-icons'  style='right: 100px;' src='/img/po.png'>".ucwords(_t(" Portuguese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=th'><img class='language-icons'  style='right: 50px;' src='/img/th.png'>".ucwords(_t(" Thai"))."</a></li>";
					break;
				case "jp":
					echo "<li><a href='/".$scr."?language=us'><img class='language-icons'  style='right: 400px;' src='/img/us.png'>".ucwords(_t(" English"))."</a></li>";
					echo "<li><a href='/".$scr."?language=cn'><img class='language-icons'  style='right: 350px;' src='/img/cn.png'>".ucwords(_t(" Chinese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=ru'><img class='language-icons'  style='right: 300px;' src='/img/ru.png'>".ucwords(_t(" Russian"))."</a></li>";
					echo "<li><a href='/".$scr."?language=jp'><img class='language-icons selected'  style='right: 250px;background-color: #dddd00;' src='/img/jp.png'>".ucwords(_t(" Japanese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=fr'><img class='language-icons'  style='right: 200px;' src='/img/fr.png'>".ucwords(_t(" French"))."</a></li>";
					echo "<li><a href='/".$scr."?language=sp'><img class='language-icons'  style='right: 150px;' src='/img/sp.png'>".ucwords(_t(" Spanish"))."</a></li>";
					echo "<li><a href='/".$scr."?language=po'><img class='language-icons'  style='right: 100px;' src='/img/po.png'>".ucwords(_t(" Portuguese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=th'><img class='language-icons'  style='right: 50px;' src='/img/th.png'>".ucwords(_t(" Thai"))."</a></li>";
					break;
				case "cn":
					echo "<li><a href='/".$scr."?language=us'><img class='language-icons'  style='right: 400px;' src='/img/us.png'>".ucwords(_t(" English"))."</a></li>";
					echo "<li><a href='/".$scr."?language=cn'><img class='language-icons selected'  style='right: 350px;background-color: #dddd00;' src='/img/cn.png'>".ucwords(_t(" Chinese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=ru'><img class='language-icons'  style='right: 300px;' src='/img/ru.png'>".ucwords(_t(" Russian"))."</a></li>";
					echo "<li><a href='/".$scr."?language=jp'><img class='language-icons'  style='right: 250px;' src='/img/jp.png'>".ucwords(_t(" Japanese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=fr'><img class='language-icons'  style='right: 200px;' src='/img/fr.png'>".ucwords(_t(" French"))."</a></li>";
					echo "<li><a href='/".$scr."?language=sp'><img class='language-icons'  style='right: 150px;' src='/img/sp.png'>".ucwords(_t(" Spanish"))."</a></li>";
					echo "<li><a href='/".$scr."?language=po'><img class='language-icons'  style='right: 100px;' src='/img/po.png'>".ucwords(_t(" Portuguese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=th'><img class='language-icons'  style='right: 50px;' src='/img/th.png'>".ucwords(_t(" Thai"))."</a></li>";

					break;
				default:
					echo "<li><a href='/".$scr."?language=us'><img class='language-icons selected'  style='right: 400px;background-color: #dddd00;' src='/img/us.png'>".ucwords(_t(" English"))."</a></li>";
					echo "<li><a href='/".$scr."?language=cn'><img class='language-icons'  style='right: 350px;' src='/img/cn.png'>".ucwords(_t(" Chinese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=ru'><img class='language-icons'  style='right: 300px;' src='/img/ru.png'>".ucwords(_t(" Russian"))."</a></li>";
					echo "<li><a href='/".$scr."?language=jp'><img class='language-icons'  style='right: 250px;' src='/img/jp.png'>".ucwords(_t(" Japanese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=fr'><img class='language-icons'  style='right: 200px;' src='/img/fr.png'>".ucwords(_t(" French"))."</a></li>";
					echo "<li><a href='/".$scr."?language=sp'><img class='language-icons'  style='right: 150px;' src='/img/sp.png'>".ucwords(_t(" Spanish"))."</a></li>";
					echo "<li><a href='/".$scr."?language=po'><img class='language-icons'  style='right: 100px;' src='/img/po.png'>".ucwords(_t(" Portuguese"))."</a></li>";
					echo "<li><a href='/".$scr."?language=th'><img class='language-icons'  style='right: 50px;' src='/img/th.png'>".ucwords(_t(" Thai"))."</a></li>";
					break;
			}

			?>
</li>
		</ul>
	</li>
