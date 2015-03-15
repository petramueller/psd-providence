<?php
 /* ----------------------------------------------------------------------
 * KeyProviderFactory.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once("KeyProviderFactory.php");
require_once("BookKeyProvider.php");
require_once("PaperKeyProvider.php");
require_once("OtherKeyProvider.php");

class KeyProviderFactory {
	/**
	 * Returns a key provider, depending on the given type code
	 * @param $typeCode string Type code
	 * @return IKeyProvider Key provider
	 */
	public function createKeyProvider($typeCode){
		if(!isset($typeCode) || empty($typeCode) || !is_string($typeCode)){
			throw new InvalidArgumentException("typeCode");
		}

		switch ($typeCode) {
			case "book":
				return new BookKeyProvider();
				break;
			case "paper":
				return new PaperKeyProvider();
				break;
			case "other":
				return new OtherKeyProvider();
				break;
			default:
				throw new InvalidArgumentException("typeCode");
		}
	}
}