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

<div id="divContenedor">
	<div id="divReproductor">
		<div id="divInfo">
			<!--BEGIN INFO MUSIC-->
			<div class="col-md-4" id="divLogo" style="text-align: left;">
				<img src="<?php echo $p->image; ?>" width="120" height="100" align="left">
				<?php //displayPicture($idx, $image, $name, $action, $exclu, $rating, $compatible, 120); ?>
			</div><!-- end div #divLogo -->

			<div class="col-md-8" id="divInfoCancion">
				<label
					id="lblCancion"><strong>Name: </strong><span>-</span></label>
				<label
					id="lblArtista"><strong>Artist: </strong><span>-</span></label>
				<label
					id="lblDuracion"><strong>Time: </strong><span>-</span></label>
				<label
					id="lblEstado"><strong>Lapsed: </strong><span>-</span></label>
			</div><!-- end div #divInfoCancion -->
			<!-- END INFO MUSIC -->

			<div style="clear: both"></div>
		</div><!-- end div #divInfo -->

		<!-- BEGIN PLAYER BUTTONS -->
		<div id="divControles">
			<input type="button" class="input-player" id="btnReproducir" title="Play">
			<input type="button" class="input-player" id="btnPausar" title="Pause">
			<input type="button" class="input-player" id="btnAnterior" title="Prev">
			<input type="button" class="input-player" id="btnSiguiente" title="Next">
			<input type="button" class="input-player" id="btnRepetir" title="Repeat">
			<input type="button" class="input-player" id="btnSilencio" title="Mute">
		</div><!-- end div #divControles -->
		<!-- END PLAYER BUTTONS -->

		<!-- BEGIN PROGRESS BAR-->
		<div id="divProgreso">
			<div id="divBarra"></div>
		</div><!-- end div #divProgreso -->
		<!-- END PROGRESS BAR -->

		<!-- BEGIN PLAYER LIST -->
		<div id="divLista">
			<ol id="olCanciones">

				<?php // display the music player
				//echo $zip;
				// Initialize getID3 engine
				$mp3 = new audioInfo();
				$zip1 = new ZipArchive();
				$dir_name = $_SERVER['DOCUMENT_ROOT'] . '/tmpdata';
				$zipadr= str_replace("xxxx", $settings->download_path, $p->zip);
				if ($zip1->open($zipadr)) {
					for ($i = 0; $i < $zip1->numFiles; $i++) {
						$entry = $zip1->statIndex($i);
						$solid_name="";
						// is it an image?
						if ($entry['size'] > 0 && preg_match('#\.(mp3)$#i', $entry['name'])) {
							$f_extract = $zip1->getNameIndex($i);
							$files[] = $f_extract; /* you man want to keep this array (use it to show result or something else) */

							if ($zip1->extractTo($dir_name, $f_extract) === true) {
								$solid_name = basename($f_extract);
								if (strpos($f_extract, "/")) // make sure zipped file is in a directory
								{
									if ($dir_name{strlen($dir_name) - 1} == "/") $dir_name = substr($dir_name, 0, strlen($dir_name) - 1); // to prevent error if $dir_name have slash in end of it
									if (!file_exists($dir_name . "/" . $solid_name)) {// you said you don't want to replace existed file
										copy($dir_name . "/" . $f_extract, $dir_name . "/" . $solid_name); // taking file back to where you need [$dir_name]
									}
									unlink($dir_name . "/" . $f_extract); // [removing old file]
									rmdir(str_replace($solid_name, "", $dir_name . "/" . $f_extract)); // [removing directory of it]
								}
							} else {
								echo("error on export<br />\n");
							}
						}
						if ($solid_name<>"") {
							$dest = $dir_name . "/" . $solid_name;
							$url = "/tmpdata/" . $solid_name;
							//echo "source=" . $source . "<br>";
							//echo "dest=" . $dest . "<br>";
							if (file_exists($dest) && is_file($dest)) {
								//$tag = id3_get_tag( $dest );
								$tag = $mp3->Info($dest);
								$title = $tag['tags']['id3v1']['title'][0];
								$title=str_replace("_"," ",$title);
								$title=str_replace("-"," ",$title);
								$title=str_replace("/"," ",$title);
								$artist = $tag['tags']['id3v1']['artist'][0];
								$time = $tag['playtime_seconds'];
								$year = $tag['tags']['id3v2']['year'][0];
								$genre = $tag['tags']['id3v2']['genre'][0];
								if ($title == "") $title = $entry['name'];
								if ($artist == "") $artist = "Unknown";
								if ($album == "") $album = basename($zipadr);
								if ($year == "") $year = "";
								if ($genre == "") $genre = $settings->category . "|" . $settings->subcategory . "|" . $settings->subcategory2;

								//echo "<br>".$title."|".$artist."|".$time."|".$year."|".$genre."<Br>";
								//dump($tag);exit;

								?>
								<li rel="<?php echo $url; ?>" style="text-align: left;">
									<strong><?php echo $title . " (" . $year . ")"; ?></strong>
									<em><?php echo $artist; ?>  </em>
								</li>
								<?php
								//echo "url= $url | source=".$source." | dest=".$dest."<br>";
							}
						}
					}
				}
				$zip1->close();
				?>


			</ol>
		</div><!-- end div #divLista -->
		<!-- END PLAYER LIST -->

	</div><!-- end div #divReproductor -->
</div><!-- end div #divContenedor -->