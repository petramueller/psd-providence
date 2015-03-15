<?php
 /* ----------------------------------------------------------------------
 * PaperKeyProvider.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once("IKeyProvider.php");

class PaperKeyProvider implements IKeyProvider{

	public function getMainAuthorRelationshipKey()
	{
		return "main_paper_author";
	}

	public function getCategoryAttributeKey()
	{
		return "ca_objects.paper_category_list_attr";
	}

	public function getTypesWithObjectSource()
	{
		return array();
	}
}