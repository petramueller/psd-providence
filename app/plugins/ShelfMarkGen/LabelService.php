<?php
 /* ----------------------------------------------------------------------
 * LabelService.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once(__CA_APP_DIR__ . "/plugins/ShelfMarkGen/models/Label.php");

class LabelService {
	/**
	 * @param $shelfMark string
	 */
	public function addObjectToPrintQueue($shelfMark){
		try {
			$con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$cmd = $con->prepare("INSERT INTO tud_labels(shelfmark) VALUES(:shelfmark);");
			$cmd->bindParam(":shelfmark", $shelfMark, PDO::PARAM_STR);
			$cmd->execute();
			$con = null;
		}
		catch (PDOException $e) {
			echo "There was an error! " . $e->getMessage();
			exit;
		}
	}

	/**
	 * @param $shelfMarks array
	 */
	public function removeObjectFromPrintQueue($shelfMarks){
		if(!sizeof($shelfMarks)){
			return;
		}

		try {
			$con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			foreach($shelfMarks as $shelfMark){
				$cmd = $con->prepare("DELETE FROM tud_labels WHERE shelfmark = :shelfmark;");
				$cmd->bindParam(":shelfmark", $shelfMark, PDO::PARAM_STR);
				$cmd->execute();
			}
			$con = null;
		}
		catch (PDOException $e) {
			echo "There was an error! " . $e->getMessage();
			exit;
		}
	}

	public function getLabels(){
		try {
			$con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$cmd = $con->prepare("SELECT id, shelfmark FROM tud_labels;");

			$cmd->execute();
			$result = $cmd->fetchAll(PDO::FETCH_CLASS, "Label");
			$con = null;
		}
		catch (PDOException $e) {
			$con = null;
			throw $e;
		}
		return $result;
	}
}