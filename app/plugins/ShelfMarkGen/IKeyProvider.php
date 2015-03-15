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

interface IKeyProvider {
	public function getMainAuthorRelationshipKey();
	public function getCategoryAttributeKey();
	public function getTypesWithObjectSource();
}