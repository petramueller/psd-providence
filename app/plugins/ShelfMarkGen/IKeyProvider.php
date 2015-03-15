<?php
 /* ----------------------------------------------------------------------
 * IKeyProvider.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

/**
 * Interface IKeyProvider
 * Provides field and relationship keys needed during shelf mark generation
 */
interface IKeyProvider {
	/**
	 * Name of the main author relationship
	 * @return string Relationship name
	 */
	public function getMainAuthorRelationshipKey();

	/**
	 * Name of the category attribute
	 * @return string Category attribute name
	 */
	public function getCategoryAttributeKey();

	/**
	 * Names of child types that may be borrowed from SLUB
	 * @return array Array of strings
	 */
	public function getTypesWithObjectSource();
}