<?php
 /* ----------------------------------------------------------------------
 * BookKeyProvider.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once("IKeyProvider.php");

class BookKeyProvider implements IKeyProvider{

	public function getMainAuthorRelationshipKey()
	{
		return "main_book_author";
	}

	public function getCategoryAttributeKey()
	{
		return "ca_objects.category_list_attr";
	}

	public function getTypesWithObjectSource()
	{
		return array("copy", "attachment", "digitalcontent");
	}
}