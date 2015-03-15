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

/**
 * Class PaperKeyProvider
 * Provides field and relationship keys needed during shelf mark generation
 */
class PaperKeyProvider implements IKeyProvider{
	/**
	 * Name of the main author relationship
	 * @return string Relationship name
	 */
	public function getMainAuthorRelationshipKey()
	{
		return "main_paper_author";
	}

	/**
	 * Name of the category attribute
	 * @return string Category attribute name
	 */
	public function getCategoryAttributeKey()
	{
		return "ca_objects.paper_category_list_attr";
	}

	/**
	 * Names of child types that may be borrowed from SLUB
	 * @return array Array of strings
	 */
	public function getTypesWithObjectSource()
	{
		return array();
	}
}