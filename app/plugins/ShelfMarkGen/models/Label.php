<?php
 /* ----------------------------------------------------------------------
 * Label.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

class Label {
	public $id = null;
	public $shelfmark = null;

	public function getId(){
		return $this->id;
	}

	public function getShelfMark(){
		return $this->shelfmark;
	}
}