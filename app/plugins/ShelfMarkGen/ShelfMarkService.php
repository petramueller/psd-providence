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
	 * Gets a shelf mark for a given author and category combination
	 * @param $authorSurname string Surname of main author
	 * @param $categoryAbbr string Abbreviation (value) of category
	 * @return string The generated shelf mark
	 */
	public function getShelfMark($authorSurname, $categoryAbbr)
	{
		$category = $this->normalizeCategory($categoryAbbr);
		$firstLetter = $this->getFirstLetterNormalized($authorSurname);
		$index = $this->generateShelfMark($firstLetter, $category);
		$shelfMark = $this->formatShelfMark($firstLetter, $category, $index);
		return $shelfMark;
	}

	/**
	 * Get the first letter of a string in normalized form, uppercase
	 * @param $string string string to get first letter from
	 * @return string Uppercased, normalized first letter, "X" if $string is empty.
	 */
	private function getFirstLetterNormalized($string){
		if($string === ""){
			$firstName = "0";
		} else {
			$firstName = mb_strtoupper(mb_substr(Normalizer::Normalize($string, Normalizer::FORM_D), 0, 1));
		}
		return $firstName;
	}

	/**
	 * Normalizes category string
	 * @param $category string category abbreviation
	 * @return string category abbreviation uppercased
	 */
	private function normalizeCategory($category){
		return mb_strtoupper($category);
	}

	/**
	 * Gets the index from a custom SQL table
	 * @param $firstLetter string First letter of author surname
	 * @param $category string Category abbreviation
	 * @return int
	 */
	private function generateShelfMark($firstLetter, $category){
		$maxIndex = null;
		try {
			//TODO: Use table locks
			$con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$cmd = $con->prepare("SELECT MAX(`index`) FROM tud_shelf_marks WHERE author = :author AND category = :category;");
			$cmd->bindParam(":author", $firstLetter);
			$cmd->bindParam(":category", $category);
			$cmd->execute();
			$result = $cmd->fetchAll(PDO::FETCH_COLUMN, 0);
			$maxIndex = $result[0];
			if($maxIndex == null){
				//We don't have this combination of author (first letter of surname) and category yet
				$maxIndex = 1;
				$cmd = $con->prepare("INSERT INTO tud_shelf_marks(author, category, `index`) VALUES(:author, :category, 1);");
				$cmd->bindParam(":author", $firstLetter);
				$cmd->bindParam(":category", $category);
				$cmd->execute();
			} else {
				//We have this combination of author (first letter of surname) and category already, increment index by 1
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

		return $maxIndex;
	}

	/**
	 * Format shelf mark string
	 * @param $firstLetter string First letter of author surname
	 * @param $category string Category abbreviation
	 * @param $index int Index number
	 * @return string shelf mark
	 */
	private function formatShelfMark($firstLetter, $category, $index){
		return sprintf("E-%s-%s%s", $category, $firstLetter, str_pad($index, 3, "0", STR_PAD_LEFT));
	}
}