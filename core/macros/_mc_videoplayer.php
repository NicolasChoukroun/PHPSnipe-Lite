<?php /*
 *  Copyright (c) 2013-2020. Nicolas Choukroun.
 *  Copyright (c) 2013-2020. The PHPSnipe Developers.
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the Attribution 4.0 International License as published by the
 *  Creative Commons Corporation; either version 2 of the License, or (at your option)
 *  any later version.  See COPYING for more details.
 *
 ******************************************************************************/ 


$dbv=new Database();

// YOUTUBE
$sql="select * from ximages where productid=".$p->id." and type=2";
$dbv->query($sql);
$nbr=$dbv->nbr();
if ($nbr==0 && $p->youtube <> "") {

?>

<div class="col-md-12 embbed-container boxed" style=" width: 100%    !important;height: auto   !important;">
	<?php if ($p->youtube <> "") { ?>
		<div>
			<?php echo $p->youtube;?>
            <iframe class="shadowed" src='<?php echo $p->youtube; ?>' frameborder='0' width="853" height="480" allowfullscreen></iframe>
		</div>
	<?php } ?>
</div>

<?php 
}
if ($nbr>0 ) {
	$i=$j=0;
	?>
	<div class="col-md-12 embbed-container boxed" style=" width: 100%   !important;height: auto   !important;">
	<ul class="nav nav-tabs "  id="myTabs">
		
		<?php
		for ($i=0;$i<$nbr;$i++) {		
			if ($i==0) echo '<li class="active"><a data-toggle="tab" href="#youtube'.$i.'">Youtube '.$i.' </a></li>';
			else echo '<li ><a data-toggle="tab" href="#youtube'.$i.'">Youtube '.$i.' </a></li>';	
		}
		echo "</ul>";
		echo ' <div class="tab-content">';
		while ($dbv->next()) {
			
			if ($j==0) echo ' <div id="youtube'.$j.'" class="active tab-pane  embed-responsive embed-responsive-16by9">';
			else echo ' <div id="youtube'.$j.'" class=" tab-pane  embed-responsive embed-responsive-16by9">';
			// echo $dbv->rs['path']
			//https://www.youtube.com/embed/LWoVxIT4T5w?feature=oembed
			if (strpos(strtolower($dbv->rs['path']),"embed")===false)  {
			    $youtubeid=youtube_id_from_url($dbv->rs['path']); e
	?>		
			
				<iframe class="shadowed embed-responsive-item" src='https://www.youtube.com/embed/<?php echo $youtubeid; ?>?feature=oembed' frameborder='0' width="853" height="480" allowfullscreen></iframe>
				<?php }else{ ?>
					<iframe class="shadowed embed-responsive-item" src='<?php echo $dbv->rs['path'];?>' frameborder='0' width="853" height="480" allowfullscreen></iframe>
			<?php } ?>
			</div>
		
			<?php
			$j++;
		}
		?>
		</div>	
	</div>	
	<?php
}

// VIMEO
$sql="select * from ximages where productid=".$p->id." and type=3";
$dbv->query($sql);
$nbr=$dbv->nbr();
if ($nbr==0 && $p->vimeo <> "") {

?>

<div class="col-md-12 embbed-container boxed" style=" width: 100%    !important;height: auto   !important;">
	<?php if ($p->vimeo <> "") { ?>
		<div>
		
				<iframe class="shadowed embed-responsive-item" src='<<?echo $p->vimeo;?>' frameborder='0' width="853" height="480" allowfullscreen></iframe>
			
		</div>
	<?php } ?>
</div>

<?php 
}
if ($nbr>0 ) {
	$i=$j=0;
	?>
	<div class="col-md-12 embbed-container boxed" style=" width: 100%   !important;height: auto   !important;">
	<ul class="nav nav-tabs "  id="myTabs">
		
		<?php

		for ($i=0;$i<$nbr;$i++) {		
			if ($i==0) { echo '<li class="active"><a data-toggle="tab" href="#vimeo'.$i.'">Vimeo '.$i.' </a></li>'; }
			else { echo '<li ><a data-toggle="tab" href="#vimeo'.$i.'">Vimeo '.$i.' </a></li>'; }
		}
		echo "</ul>";
		echo ' <div class="tab-content">';
		while ($dbv->next()) {
			$path=str_replace("https://vimeo.com","https://player.vimeo.com/video/", $dbv->rs['path'])."";
			if ($j==0) echo ' <div id="vimeo'.$j.'" class="active tab-pane embed-responsive embed-responsive-16by9">';
			else echo ' <div id="vimeo'.$j.'" class=" tab-pane  embed-responsive embed-responsive-16by9">';
		        //<iframe src="https://player.vimeo.com/video/212370716?title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
		        //<iframe type="text/html" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" data-src="https://player.vimeo.com/video/112885522?autoplay=1" width="640" height="360"></iframe>
	?>

            <div class="frame" data-type="embed"><iframe type="text/html" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" src="<?php echo $path; ?>" width="853" height="480" ></iframe></div>
			
			</div>
		
			<?php
			$j++;
		}
		?>
		</div>	
	</div>	

	<?php
}

// SKETCHFAB
$sql="select * from ximages where productid=".$p->id." and type=4";
$dbv->query($sql);
$nbr=$dbv->nbr();
if ($nbr==0 && $p->sketchfab <> "") {

?>

<div class="col-md-12 embbed-container boxed" style=" width: 100%    !important;height: auto   !important;">
	<?php if ($p->sketchfab <> "") { ?>
		<div>
			<iframe class="shadowed embed-responsive-item" src='<?php echo $p->sketchfab;?>' frameborder='0' width="853" height="480" allowfullscreen></iframe>
		</div>
	<?php } ?>
</div>

<?php 
}
if ($nbr>0 ) {
	$i=$j=0;
	?>
	<div class="col-md-12 embbed-container boxed" style=" width: 100%   !important;height: auto   !important;">
	<ul class="nav nav-tabs "  id="myTabs">
		
		<?php
		for ($i=0;$i<$nbr;$i++) {		
			if ($i==0) echo '<li class="active"><a data-toggle="tab" href="#sketchfab'.$i.'">Sketchfab '.$i.' </a></li>';
			else echo '<li ><a data-toggle="tab" href="#sketchfab'.$i.'">Sketchfab '.$i.' </a></li>';	
		}
		echo "</ul>";
		echo ' <div class="tab-content">';
		while ($dbv->next()) {
			
			if ($j==0) echo ' <div id="sketchfab'.$j.'" class="active tab-pane  embed-responsive embed-responsive-16by9">';
			else echo ' <div id="sketchfab'.$j.'" class=" tab-pane  embed-responsive embed-responsive-16by9">';
	?>		
			
				<iframe class="shadowed embed-responsive-item" src='<?php echo $dbv->rs['path']; ?>' frameborder='0' width="853" height="480" allowfullscreen></iframe>
			
			</div>
		
			<?php
			$j++;
		}
		?>
		</div>	
	</div>	

	<?php
}


$dbv->close();
?>
