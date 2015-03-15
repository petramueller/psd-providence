<?php
 /* ----------------------------------------------------------------------
 * OtherKeyProvider.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once("BookKeyProvider.php");

class OtherKeyProvider extends BookKeyProvider{

	public function getMainAuthorRelationshipKey()
	{
		return "main_other_author";
	}

	public function getTypesWithObjectSource()
	{
		return array("other_copy", "other_attachment", "other_digitalcontent");
	}
}