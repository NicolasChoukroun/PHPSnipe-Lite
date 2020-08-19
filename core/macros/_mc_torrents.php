<?php /*
 *  Copyright (c) 2013-2020. Nicolas Choukroun.
 *  Copyright (c) 2013-2020. The PHPSnipe Developers.
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the Attribution 4.0 International License as published by the
 *  Creative Commons Corporation; either version 2 of the License, or (at your option)
 *  any later version.  See COPYING for more details.
 *
 ******************************************************************************/ 

?>

<style>
	.a, .s, .u, .d, .v {
	}

	.h {
		margin: 2px;
		padding: 10px;
		border: 2px solid black;
		background-color: #bcd9f1;
	}

	.torrent {
		text-align: center;
		margin: 2px;
		padding: 10px;
		border: 2px solid red;
		background-color: #ecf8ff;

	}
</style>
<div class="col-md-12 container">
	<hr>
	<?php
	//if ($user->is_admin) {
	if (!$user->is_logged && !$user->is_stopped && $user->rank >= $settings->rank_see_torrents) {
		?>

		<p><span class="label label-default">Find Private Torrents (powered by <a
					href="http://www.fileshareclub.com"><font
						color="white"><?php echo $settings->author; ?>
						's ass</font></a>)</span></p>

		<?php
		require_once $_SERVER['DOCUMENT_ROOT'] . '/3rdparty/Torrent.php';
		$tname = sanitizeFileNameLight(strip_tags("[FSC] - " . $name . " v" . $versionxxx));

		$torrent = new Torrent($zippath);
		$torrent->announce('udp://inferno.demonoid.ph:3389/announce'); // add a tracker
		$torrent->announce('udp://tracker.blackunicorn.xyz:6969/announce'); // add a tracker
		$torrent->announce('http://announce.torrentsmd.com:6969/announce'); // add a tracker
		$torrent->announce('http://bt.careland.com.cn:6969/announce'); // add a tracker
		$torrent->announce('http://explodie.org:6969/announce'); // add a tracker
		$torrent->announce('http://mgtracker.org:2710/announce'); // add a tracker
		$torrent->announce('http://tracker.tfile.me/announce'); // add a tracker
		$torrent->announce('http://tracker.torrenty.org:6969/announce'); // add a tracker
		$torrent->announce('http://tracker.trackerfix.com/announce'); // add a tracker
		$torrent->announce('http://www.mvgroup.org:2710/announce'); // add a tracker
		$torrent->announce('udp://9.rarbg.com:2710/announce'); // add a tracker
		$torrent->announce('udp://9.rarbg.me:2710/announce'); // add a tracker
		$torrent->announce('udp://9.rarbg.to:2710/announce'); // add a tracker
		$torrent->announce('udp://coppersurfer.tk:6969/announce'); // add a tracker
		$torrent->announce('udp://exodus.desync.com:6969/announce'); // add a tracker
		$torrent->announce('udp://glotorrents.pw:6969/announce'); // add a tracker
		$torrent->announce('udp://open.demonii.com:1337/announce'); // add a tracker');
		$torrent->announce('udp://tracker.coppersurfer.tk:6969/announce'); // add a tracker
		$torrent->announce('udp://tracker.glotorrents.com:6969/announce'); // add a tracker
		$torrent->announce('udp://tracker.leechers-paradise.org:6969/announce'); // add a tracker
		$torrent->announce('udp://tracker.openbittorrent.com:80/announce'); // add a tracker');
		$torrent->announce('udp://tracker.opentrackr.org:1337/announce'); // add a tracker
		$torrent->announce('udp://tracker.publicbt.com:80/announce'); // add a tracker
		$torrent->announce('udp://tracker4.piratux.com:6969/announce'); // add a tracker
		$info = strip_tags($description);

		$torrent->name($tname . ".zip");
		$dest = "d:/torrents_source/" . $tname . ".zip";
		if (!file_exists($dest)) {
			if (copy($zippath, $dest) === false) echo "<font color='red'>ERROR: the Torrent Client download directory must not be authorized to write files: $dest</font><br>";
			else  echo "Zip file saved to the seedbox storage: successful<br>";
		}
		$torrent->is_private(false);
		$hash = $torrent->hash_info();
		$download = "/torrents/" . $hash . '.torrent';
		$torrent->comment($info);
		$name1 = strip_tags(str_replace(" ", "+", $tname));
		$torrentlink = "<a href='" . $download . "'><img src='/img/torrent.png'></a>";
//$magnetlink = "magnet:?xt=urn:btih:" . $hash . "&dn=" . $name1 . "&tr=udp://inferno.demonoid.ph:3389/announce&tr=udp://tracker.blackunicorn.xyz:6969/announce&tr=http://announce.torrentsmd.com:6969/announce&tr=http://bt.careland.com.cn:6969/announce&tr=http://explodie.org:6969/announce&tr=http://mgtracker.org:2710/announce&tr=http://tracker.tfile.me/announce&tr=http://tracker.torrenty.org:6969/announce&tr=http://tracker.trackerfix.com/announce&tr=http://www.mvgroup.org:2710/announce&tr=udp://9.rarbg.com:2710/announce&tr=udp://9.rarbg.me:2710/announce&tr=udp://9.rarbg.to:2710/announce&tr=udp://coppersurfer.tk:6969/announce&tr=udp://exodus.desync.com:6969/announce&tr=udp://glotorrents.pw:6969/announce&tr=udp://open.demonii.com:1337/announce&tr=udp://tracker.coppersurfer.tk:6969/announce&tr=udp://tracker.glotorrents.com:6969/announce&tr=udp://tracker.leechers-paradise.org:6969/announce&tr=udp://tracker.openbittorrent.com:80/announce&tr=udp://tracker.opentrackr.org:1337/announce&tr=udp://tracker.publicbt.com:80/announce&tr=udp://tracker4.piratux.com:6969/announce";
		$magnetlink = "#";
		$magnet = "<a href='" . $magnetlink . "'><img src='/img/magnet.png'></a>";
		$size = getFileSize64($size2);
		$i = 0;
		/*

		   <table border="0" width="100%">
			   <tr>
				   <td class="h">Nbr</td>
				   <td class="h">Name</td>
				   <td class="h">Torrent</td>
				   <td class="h">Magnet</td>
				   <td class="h">Quality</td>
				   <td class="h">Age</td>
				   <td class="h">Size</td>
				   <td class="h">Seeders</td>
				   <td class="h">Leechers</td>
			   </tr>
			   <?php

			   $span = "<tr><td class='torrent'>" . intval($i) . "</td><td class='torrent'>$tname</td><td class='torrent'>$torrentlink</td><td class='torrent'>$magnet</td><td class='torrent'>" . "?" . " </td><td class='torrent'> Today  </td><td class='torrent'>" . $size . "  </td><td class='torrent'>1  </td><td class='torrent'>1</td>";
			   echo $span;
			   $i++;
			   $nbr++;
			   echo "</tr>";

			   // error method return the last error message

			   $tpath = $_SERVER['DOCUMENT_ROOT'] . '/torrents/' . $hash . '.torrent';
			   $watch1 = $settings->torrent_watch_path . "/" . $hash . "torrent.added";
			   if (file_exists($tpath) || file_exists($watch1)) {
				   //echo "error: torrent already exists";
			   } else {
				   $torrent->save($tpath);
				   $watch = "d:\\torrents_watch\\" . $hash . ".torrent";

				   //$torrent->send();
				   if (!$error = $torrent->error()) {

					   $db2 = new Database();

					   $sql = "INSERT INTO kat ( hash, name,category,info,download,size,catid,fcount,seeders,leechers,date,verified )
											   VALUES ( '" . $hash . "','" . $name . "','" . $categoryglobal . "','" . $info . "','" . $download . "','" . $size2 . "','26','1','1', '0', '" . time() . "', '1')";
					   $db2->query($sql, "select hash from kat where hash='" . $hash . "'");

					   if (copy($tpath, $watch) === false) echo "<font color='red'>ERROR: the Autowatch directory most not be authorized to write files: $watch</font><br>"; else echo ".Torrent created and copied to the Autowatch directory: seeding<br>";

				   } else {
					   echo '<br>Torrent creation error: ', $error;

				   }
				   // } // end if admin
			   }
			   } else {
				   echo "<div class='col-md-12 alert alert-warning'>You cannot see the private torrents because your rank is < to " . $settings->rank_see_torrents . ".</div>";
			   }

			   ?>
		   </table>
		   <br>
		   <?php
		   if ($nbr == 0) {
			   echo "<div class='col-md-12 alert alert-warning'>No Private Torrents available for this item. This is weird. That mean something is wrong.</div>";
		   }
		   */
	}
	if (!$user->is_stopped && $user->is_logged) {

		?>
		<p><span class="label label-default">Find Public Torrents (powered by <a
					href="http://www.torrentz.eu"><font color="white">Torrentz2.eu</font></a>)</span>
		</p>

		<table border="0" width="100%">
			<tr>
				<td class="h">Nbr</td>
				<td class="h">Name</td>
				<td class="h">Torrent</td>
				<td class="h">Magnet</td>
				<td class="h">Quality</td>
				<td class="h">Age</td>
				<td class="h">Size</td>
				<td class="h">Seeders</td>
				<td class="h">Leechers</td>
			</tr>

			<?php

			$dbx1 = new Database();
			$sql = "select * from kat where LOWER(name) LIKE '%" . strtolower($p->name) . "%'";
			$dbx1->query($sql);
			if ($dbx1->nbr() >= 0) {
				$name1 = str_replace(" ", "+", $p->name);

				//$grab = "http://www.torrentz.eu/search?q=" . $name1 . "+unity+asset";
				$grab = "https://torrentz2.eu/search?f=" . $name1 . "&s=dt&v=t&sd=d";
				//echo $grab."<br>";
				$curl = curl_init();
				// Setup headers - I used the same headers from Firefox version 2.0.0.6
				// below was split up because php.net said the line was too long. :/
				$header[0] = "Accept: application/json, text/javascript, text/html; q=0.01";
				$header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36";
				$header[] = "Cache-Control: max-age=0";
				$header[] = "Connection: keep-alive";
				$header[] = "Keep-Alive: 3000";
				$header[] = "X-Requested-With: Google-Bot";
				$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
				$header[] = "Accept-Language: en-us,en;q=0.5";
				$header[] = "Referer: http://www.torrentz.eu/";
				//$header[] = "Referer: https://www.assetstore.unity3d.com/en/";
				$header[] = "Pragma: "; // browsers keep this blank.
				//$header[] = "X-Kharma-Version: 5.1.0-r84295";
				$header[] = "Age: 0";

				//$header[] = "X-Unity-Session: DT0QtkD0XtndqYqKiZRtf9Eb5_0-VD4ynLiVhdPEWzp2Td-_gs07pJGNzxwDjpQv_3riBpd2Kr5CP_mg4lrRnQ::Sausage::";

				curl_setopt($curl, CURLOPT_URL, $grab);
				curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
				//curl_setopt($curl, CURLOPT_AUTOREFERER, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				curl_setopt($curl, CURLOPT_TIMEOUT, 30);
				curl_setopt($curl, CURLOPT_COOKIESESSION, true);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
				$cookie_jar = $_SERVER['DOCUMENT_ROOT'] . "/tmpdata/cookie.txt";
				curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);

				$html = curl_exec($curl); // execute the curl command
				$first_step = explode('<dt>', $html);
				$second_step = explode("</dt>", $first_step[1]);
				$i = 0;
				$nbr = 0;
				foreach ($first_step as $element) {
					$html = str_get_html($element);
					if ($i >= 4) {
						echo "<tr>";
						$url = $html->find('a', 0)->href;
						$url1 = str_replace("/", "", $url);
						$hash = $url1;
						$url1 = "https://torcache.net/torrent/" . $hash . ".torrent";
						if (url_exists($url1) == false) {
							$url1 = "";
						}
						$name = $html->find('a', 0)->innertext;
						$name1 = strip_tags(str_replace(" ", "+", $name));
						// echo $i." - <a href='".$url."'>'".$name."</a><br>";
						$dt = $html->find('dd', 0);
						$html2 = str_get_html($dt);
						if ($url1 <> "") {
							$torrent = "<a href='" . $url1 . "'><img src='/img/torrent.png'></a>";
							$magnetlink = "magnet:?xt=urn:btih:" . $hash . "&dn=" . $name1 . "&tr=udp://inferno.demonoid.ph:3389/announce&tr=udp://tracker.blackunicorn.xyz:6969/announce&tr=http://announce.torrentsmd.com:6969/announce&tr=http://bt.careland.com.cn:6969/announce&tr=http://explodie.org:6969/announce&tr=http://mgtracker.org:2710/announce&tr=http://tracker.tfile.me/announce&tr=http://tracker.torrenty.org:6969/announce&tr=http://tracker.trackerfix.com/announce&tr=http://www.mvgroup.org:2710/announce&tr=udp://9.rarbg.com:2710/announce&tr=udp://9.rarbg.me:2710/announce&tr=udp://9.rarbg.to:2710/announce&tr=udp://coppersurfer.tk:6969/announce&tr=udp://exodus.desync.com:6969/announce&tr=udp://glotorrents.pw:6969/announce&tr=udp://open.demonii.com:1337/announce&tr=udp://tracker.coppersurfer.tk:6969/announce&tr=udp://tracker.glotorrents.com:6969/announce&tr=udp://tracker.leechers-paradise.org:6969/announce&tr=udp://tracker.openbittorrent.com:80/announce&tr=udp://tracker.opentrackr.org:1337/announce&tr=udp://tracker.publicbt.com:80/announce&tr=udp://tracker4.piratux.com:6969/announce
                                                                ";
							$magnet = "<a href='" . $magnetlink . "'><img src='/img/magnet.png'></a>";
						} else {
							//$magnet=$torrent = "<a href='http://www.dmca.com/'><img src='/img/removed.png'></a>";
							$magnet = $torrent = "<a href='" . $url1 . "'><img src='/img/removed.png'></a>";
						}

						$span = "<td class='torrent'>" . intval($i - 3) . "</td><td class='torrent'>$name</td><td class='torrent'>$torrent</td><td  class='torrent'>$magnet</td><td class='torrent'>" . $html2->find('span', 0) . " </td><td class='torrent'>" . $html2->find('span', 1) . "  </td><td class='torrent'>" . $html2->find('span', 3) . "  </td><td class='torrent'>" . $html2->find('span', 4) . "  </td><td class='torrent'>" . $html2->find('span', 5);
						echo $span;
						$nbr++;
						echo "</tr>";
					}
					$i++;
				}
			} else {
				$i = 0;
				$nbr = 0;
				while ($dbx1->next()) {
					echo "<tr>";
					$name = $dbx1->rs['name'];
					$name1 = strip_tags(str_replace(" ", "+", $name));
					$hash = $dbx1->rs['hash'];
					$url1 = "https://torcache.net/torrent/" . $hash . ".torrent";

					$seeders = $dbx1->rs['seeders'];
					$leechers = $dbx1->rs['leechers'];
					$size = getFileSize64($dbx1->rs['size']);
					$date = getDateFromNow($dbx1->rs['date']);
					if (url_exists($url1)) {
						$torrent = "<a href='" . $url1 . "'><img src='/img/torrent.png'></a>";
						$magnetlink = "magnet:?xt=urn:btih:" . $hash . "&dn=" . $name1 . "&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A80&tr=udp%3A%2F%2Fopentor.org%3A2710&tr=udp%3A%2F%2Ftracker.ccc.de%3A80&tr=udp%3A%2F%2Ftracker.blackunicorn.xyz%3A6969&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969&tr=udp%3A%2F%2Ftracker.leechers-paradise.org%3A6969";
						$magnet = "<a href='" . $magnetlink . "'><img src='/img/magnet.png'></a>";
					} else {
						//$magnet=$torrent = "<a href='http://www.dmca.com/'><img src='/img/removed.png'></a>";
						$magnet = $torrent = "<a href='" . $url1 . "'><img src='/img/removed.png'></a>";
					}

					$span = "<td class='torrent'>" . intval($i - 3) . "</td><td class='torrent'>$name</td><td class='torrent'>$torrent</td><td class='torrent'>$magnet</td><td class='torrent'>" . "?" . " </td><td class='torrent'>" . $date . "  </td><td class='torrent'>" . $size . "  </td><td class='torrent'>" . $seeders . "  </td><td class='torrent'>" . $leechers;
					echo $span;
					$i++;
					$nbr++;
					echo "</tr>";
				}
			}
			?>

		</table>
		<?php
		if ($nbr == 0) {

			echo "<div class='col-md-12 alert alert-warning'>No Public Torrents available for this item. That means that nobody is sharing that file outside this community.</div>";
		}

	}

	?>

	<br><br>
</div>