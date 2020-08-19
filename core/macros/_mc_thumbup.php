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

?>


<div id="thumb-area" class="thumbs"><div class="icon"></div></div>
<aside class="note">Vote with your thumbs:
	swipe up for <span class="icon icon-thumbs-up"></span>;
	swipe down for <span class="icon icon-thumbs-down"></span>
</aside>

<script>
var thumb = $("#thumb-area")[0];

var thumbs = function(element, event, on, off) {
	var element = $(element);
	var iconClassOn = "icon-thumbs-"+on;
	var iconClassOff = "icon-thumbs-"+off;
	element.removeClass(off).addClass(on);
	element.find(".icon").removeClass(iconClassOff).addClass(iconClassOn);
	setTimeout(function() { element.removeClass(on).find(".icon").removeClass(iconClassOn) }, 1000);
};

var thumbsUp = function(event) {
	thumbs(this, event, "up", "down");
};

var thumbsDown = function(event) {
	thumbs(this, event, "down", "up");
};

var defaults = {
	drag_block_vertical: true
}
Hammer(thumb).on("swipeup", thumbsUp);
Hammer(thumb).on("swipedown", thumbsDown);
Hammer(thumb).on("drag", function(event) {
	if (event.gesture) {
		event.gesture.preventDefault();
	}
});
</script>