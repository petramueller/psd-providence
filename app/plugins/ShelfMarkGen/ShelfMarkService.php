<?php
 /* ----------------------------------------------------------------------
 * ShelfmarkService.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

class ShelfMarkService {
	/**
	 * @param $authorSurname string Surname of main author
	 * @param $categoryAbbr string Abbreviation (value) of category
	 * @return string The generated shelf mark
	 */
	public function getShelfmark($authorSurname, $categoryAbbr)
	{
		$category = $this->normalizeCategory($categoryAbbr);
		$firstLetter = $this->getFirstLetterNormalized($authorSurname);
		return $this->generateShelfMark($firstLetter, $category);
	}

	private function getFirstLetterNormalized($string){
		if($string === ""){
			$firstName = "X";
		} else {
			$firstName = mb_strtoupper(mb_substr(Normalizer::Normalize($string, Normalizer::FORM_D), 0, 1));
		}
		return $firstName;
	}

	private function normalizeCategory($category){
		return mb_strtoupper(mb_substr($category, 0, 3));
	}

	private function generateShelfMark($firstLetter, $category){
		$maxIndex = null;
		try {
			$con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$cmd = $con->prepare("SELECT MAX(`index`) FROM tud_shelf_marks WHERE author = :author AND category = :category;");
			$cmd->bindParam(":author", $firstLetter);
			$cmd->bindParam(":category", $category);
			$cmd->execute();
			$result = $cmd->fetchAll(PDO::FETCH_COLUMN, 0);
			$maxIndex = $result[0];
			if($maxIndex == null){
				$maxIndex = 1;
				$cmd = $con->prepare("INSERT INTO tud_shelf_marks(author, category, `index`) VALUES(:author, :category, 1);");
				$cmd->bindParam(":author", $firstLetter);
				$cmd->bindParam(":category", $category);
				$cmd->execute();
			} else {
				$maxIndex++;
				$cmd = $con->prepare("UPDATE tud_shelf_marks SET `index` = `index` + 1 WHERE author = :author AND category = :category;");
				$cmd->bindParam(":author", $firstLetter);
				$cmd->bindParam(":category", $category);
				$cmd->execute();
			}
			$con = null;
		}
		catch (PDOException $e) {
			echo "There was an error! " . $e->getMessage();
			exit;
		}



		return $this->formatShelfMark($firstLetter, $category, $maxIndex);
	}

	private function formatShelfMark($firstLetter, $category, $index){
		return sprintf("E-%s-%s%s", $category, $firstLetter, str_pad($index, 3, "0", STR_PAD_LEFT));
	}

	public function updateCopy($copyId, $shelfmark)
	{
		/*
		$maxIndex = null;
		try {
			$con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$cmd = $con->prepare("UPDATE ca_object_labels SET `name` = :shelfmark, name_sort = :shelfmark WHERE object_id = :objectid;");
			$cmd->bindParam(":objectid", $copyId);
			$cmd->bindParam(":shelfmark", $shelfmark);
			$cmd->execute();
			$con = null;
		}
		catch (PDOException $e) {
			echo "There was an error! " . $e->getMessage();
			exit;
		}
		*/
	}
}