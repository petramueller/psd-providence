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

/**
 * Class BookKeyProvider
 * Provides field and relationship keys needed during shelf mark generation
 */
class BookKeyProvider implements IKeyProvider{

	/**
	 * Name of the main author relationship
	 * @return string Relationship name
	 */
	public function getMainAuthorRelationshipKey()
	{
		return "first_author";
	}

	/**
	 * Name of the category attribute
	 * @return string Category attribute name
	 */
	public function getCategoryAttributeKey()
	{
		return "ca_objects.category_list_attr";
	}

	/**
	 * Names of child types that may be borrowed from SLUB
	 * @return array Array of strings
	 */
	public function getTypesWithObjectSource()
	{
		return array("copy", "attachment", "digitalcontent");
	}
}