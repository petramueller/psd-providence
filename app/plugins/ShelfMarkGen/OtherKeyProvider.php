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

/**
 * Class OtherKeyProvider
 * Provides field and relationship keys needed during shelf mark generation
 */
class OtherKeyProvider extends BookKeyProvider{
	/**
	 * Name of the main author relationship
	 * @return string Relationship name
	 */
	public function getMainAuthorRelationshipKey()
	{
		return "first_author";
	}

	/**
	 * Names of child types that may be borrowed from SLUB
	 * @return array Array of strings
	 */
	public function getTypesWithObjectSource()
	{
		return array("other_copy", "other_attachment", "other_digitalcontent");
	}
}