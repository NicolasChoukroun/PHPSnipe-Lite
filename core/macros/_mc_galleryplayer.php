<?php /**
 * Copyright (c) 2016. TheWolf
 */

/**
 * fileshareclub.com
 * File: _mc_musicplayer.php
 * Created by Administrator
 * Date: 7/16/2016
 * Time: 6:49 PM
 */
?>


<?php
$gallery = new Database();
$gallery1 = new Database();
$sql = "SELECT path FROM ximages WHERE type=1 AND (status IS NULL OR status=0) AND  productid=" . $p->id . " ORDER BY main desc LIMIT 25";

$gallery->query($sql);
$nbr = $gallery->nbr();

if ($nbr <= 1) {

	if ($p->image == "") {
		$gallery->single();
		$p->image = $gallery->rs['path'];
		$p->image = str_replace("//", "/", $p->image);
	}
	echo '<div class="row ">';
	echo "       <div class=\"col-md-12 woodbg \" style=\"background-color:#fff;\">";

	displayPicture($p->id, $p->image, $name, 2, $p->exclu, $p->rating, $p->compatible, 600);
	echo "</div></div>";

	?>
    <div class="row">
        <div class="col-md-12 woodbg" style="background-color:#fff;">
            <br><font color="white">No Gallery to display.</font><br>
        </div>
    </div>

	<?php

}else {
//displayPicture($p->id, $p->image, $name, 2, $p->exclu, $p->rating, $p->compatible, 600);
//$gallery1->query($sql);

?>

<style>
    .videoWrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        padding-top: 25px;
        height: 0;
    }

    .videoWrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .videogallery {
        width: 100%;
        height: auto;
    }
</style>
<script src="<?php echo $settings->cdn;?>/js/jssor.slider-21.1.6.min.js" type="text/javascript"></script>
<script type="text/javascript">
    jssor_1_slider_init = function () {

        var jssor_1_SlideshowTransitions = [
            {$Duration: 1200, x: -0.3, $During: {$Left: [0.3, 0.7]}, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2},
            {$Duration: 1200, x: 0.3, $SlideOut: true, $Easing: {$Left: $Jease$.$InCubic, $Opacity: $Jease$.$Linear}, $Opacity: 2}
        ];

        var jssor_1_options = {
            $AutoPlay: true,
            $SlideshowOptions: {
                $Class: $JssorSlideshowRunner$,
                $Transitions: jssor_1_SlideshowTransitions,
                $TransitionsOrder: 1
            },
            $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$
            },
            $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$
            },
            $ThumbnailNavigatorOptions: {
                $Class: $JssorThumbnailNavigator$,
                $Cols: 1,
                $Align: 0,
                $NoDrag: true
            }
        };

        var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

        //responsive code begin
        //you can remove responsive code if you don't want the slider scales while window resizing
        function ScaleSlider() {
            var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
            if (refSize) {
                refSize = Math.min(refSize, 600);
                jssor_1_slider.$ScaleWidth(refSize);
            }
            else {
                window.setTimeout(ScaleSlider, 30);
            }
        }

        ScaleSlider();
        $Jssor$.$AddEvent(window, "load", ScaleSlider);
        $Jssor$.$AddEvent(window, "resize", ScaleSlider);
        $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
        //responsive code end
    };

</script>
<style>
    /* jssor slider bullet navigator skin 01 css */
    /*
	.jssorb01 div           (normal)
	.jssorb01 div:hover     (normal mouseover)
	.jssorb01 .av           (active)
	.jssorb01 .av:hover     (active mouseover)
	.jssorb01 .dn           (mousedown)
	*/
    .jssorb01 {
        position: absolute;
    }

    .jssorb01 div, .jssorb01 div:hover, .jssorb01 .av {
        position: absolute;
        /* size of bullet elment */
        width: 12px;
        height: 12px;
        filter: alpha(opacity=70);
        opacity: .7;
        overflow: hidden;
        cursor: pointer;
        border: #000 1px solid;
    }

    .jssorb01 div {
        background-color: gray;
    }

    .jssorb01 div:hover, .jssorb01 .av:hover {
        background-color: #d3d3d3;
    }

    .jssorb01 .av {
        background-color: #fff;
    }

    .jssorb01 .dn, .jssorb01 .dn:hover {
        background-color: #555555;
    }

    /* jssor slider arrow navigator skin 05 css */
    /*
	.jssora05l                  (normal)
	.jssora05r                  (normal)
	.jssora05l:hover            (normal mouseover)
	.jssora05r:hover            (normal mouseover)
	.jssora05l.jssora05ldn      (mousedown)
	.jssora05r.jssora05rdn      (mousedown)
	*/
    .jssora05l, .jssora05r {
        display: block;
        position: absolute;
        /* size of arrow element */
        width: 40px;
        height: 40px;
        cursor: pointer;
        background: url('/img/a17.png') no-repeat;
        overflow: hidden;
    }

    .jssora05l {
        background-position: -10px -40px;
    }

    .jssora05r {
        background-position: -70px -40px;
    }

    .jssora05l:hover {
        background-position: -130px -40px;
    }

    .jssora05r:hover {
        background-position: -190px -40px;
    }

    .jssora05l.jssora05ldn {
        background-position: -250px -40px;
    }

    .jssora05r.jssora05rdn {
        background-position: -310px -40px;
    }

    /* jssor slider thumbnail navigator skin 09 css */

    .jssort09-600-45 .p {
        position: absolute;
        top: 0;
        left: 0;
        width: 600px;
        height: 45px;
    }

    .jssort09-600-45 .t {
        font-family: verdana;
        font-weight: normal;
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        color: #fff;
        line-height: 45px;
        font-size: 20px;
        padding-left: 10px;
    }

</style>
<div class="row">
    <div class="col-md-12 woodbg" style="background-color:#fff;">
		<?php
		/* -------------------------------- MAIN Image ----------------------------------*/
		if ($p->compatible == COMPATIBLE_TEXTURE ) {
			$w = 600;
			$h = 600;
		} else {
			if ($p->compatible == COMPATIBLE_MOVIE) {
				$w = 700   ;
				$h = 400;
			} else {
				$w = 600;
				$h = 400;
			}
		}
		?>
        <div id="jssor_1" style="position: relative; margin: 0 auto; top: 0px; left: 0px; width: <?php echo $w; ?>px; height: <?php echo $h; ?>px; overflow: hidden; visibility: hidden;">';
            <!-- Loading Screen -->
            <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
                <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
                <div style="position:absolute;display:block;background:url('/img/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
            </div>

            <div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width:  <?php echo $w; ?>px; height:  <?php echo $h; ?>px; overflow: hidden;">
                <?php
                if ($p->image <> "") {
                    $gallery->single();
                    $p->image = $gallery->rs['path'];
                    $p->image = str_replace("//", "/", $p->image);
                }
                ?>
                <div><img u="image" src="<?php echo $settings->cdn.$p->image; ?>" /></div>
                <?php
                if ($nbr>1) {
	                $db0 = new Database();
	                $sql = "SELECT * FROM ximages WHERE  productid=" . $p->id . " AND type=1 AND (status IS NULL OR status=0) order by main desc";

	                $db0->query($sql);
	                $i = 0;
	                while ($db0->next()) {
		                $pid = $db0->rs['productid'];
		                $imid = $db0->rs['id'];
		                $main = $db0->rs['main'];
		                $status = $db0->rs['status'];
		                $path = $db0->rs['path'];
		                $path = str_replace("//", "/", $path);
		                $thumb = $settings->cdn."/thumb.php?zc=0&w=".intval($w*2)."&h=".intval($h*2)."&src=" . $path;
		                echo '<div><img class="boxed" u="image" src="' . $thumb . '" /></div>';
		                $i++;
		                if ($i > 20) break;
	                }

	                $db0->close();
                }
                ?>
            </div>

            <!-- Bullet Navigator -->
            <div data-u="navigator" class="jssorb01" style="bottom:16px;right:16px;">
                <div data-u="prototype" style="width:12px;height:12px;"></div>
            </div>
            <!-- Arrow Navigator -->
            <span data-u="arrowleft" class="jssora05l" style="top:0px;left:8px;width:40px;height:40px;" data-autocenter="2"></span>
            <span data-u="arrowright" class="jssora05r" style="top:0px;right:8px;width:40px;height:40px;" data-autocenter="2"></span>
        </div>
        <script type="text/javascript">jssor_1_slider_init();</script>

    </div>


	<?php if ($nbr > 1 && $p->compatible <> COMPATIBLE_TEXTURE) { ?>
    <div class="row">
        <div class="col-md-12 thumbnail transbg1" style="padding: 5px;
    width: 96%;
    height: auto;
    margin: auto;
    margin-left: 2%;
    margin-top: 10px;
    margin-bottom: 10px;" id="ximages">
			<?php
			$db1 = new Database();
			$sql = "SELECT * FROM ximages WHERE  productid=" . $p->id . " AND type=1 AND (status IS NULL OR status=0)";

			$db1->query($sql);
			// start ximages
			while ($db1->next()) {
				$pid = $db1->rs['productid'];
				$imid = $db1->rs['id'];
				$main = $db1->rs['main'];
				$status = $db1->rs['status'];
				$path = $db1->rs['path'];
				$path = str_replace("//", "/", $path);

				if ($status <= 0) {
					if ($p->compatible == COMPATIBLE_TEXTURE || $p->compatible == COMPATIBLE_UE4) {
						$w = intval(600/3);
						$h = intval(600/3);
					} else {
						if ($p->compatible == COMPATIBLE_MOVIE) {
							$w = intval(700 / 3);
							$h = intval(400 / 3);
						}else{
							$w = intval(600 / 3);
							$h = intval(400 / 3);
                        }

					}
					$thumb = $settings->cdn."/thumb.php?zc=0&w=".$w."&h=".$h."&src=" . $path;
					?>
                    <div class='col-md-3 col-xs-3 ' style='margin:2px;padding:2px;width:24%;text-center'>
                        <div class='row'>
                            <div class='col-md-12'>
                                <a  href='<?php echo $path; ?>' data-fancybox="group" rel="gallery">
                                    <img class="boxed" style='margin:auto;width:auto;height:auto;<?php if ($status == 1) echo ";opacity: 0.5;
                                        filter: alpha(opacity=50);"; ?>' src='<?php echo $thumb; ?>'></a>
                            </div>
                        </div>
                    </div>
				<?php }
			}
			// end ximages
			$db1->close();
			?>
        </div>
    </div>
	<?php } ?>

</div>
<?php } ?>


