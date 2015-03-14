<?php
 /* ----------------------------------------------------------------------
 * not_allowed_html.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */
http_response_code(403);
?>
<style type="text/css">
	.tud-clearfix:after {
		content:"";
		display:table;
		clear:both;
	}
	.tud-message{
		display: block;
		text-align:center;
		width:424px;
	}
</style>

<div class="tud-clearfix">
	<em class="tud-message">Access denied.</em>
</div>