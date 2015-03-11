<?php
 /* ----------------------------------------------------------------------
 * Checkouts.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

require_once(__CA_APP_DIR__ . "/plugins/MyCheckouts/models/Checkout.php");

/**
 * Class Checkouts
 * Get checkouts with custom notes and stores/updates notes
 */
class Checkouts {
	public function setCheckoutNote($checkout){
		if(!($checkout instanceof Checkout)){
			throw new InvalidArgumentException("checkout");
		}

		try {
			//TODO: Use table locks
			$con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$cmd = $con->prepare("INSERT INTO tud_checkout_notes (checkout_id, note, prename, surname, email, due_date)
									VALUES (:checkoutId, :note, :prename, :surname, :email, :dueDate)
									ON DUPLICATE KEY
										UPDATE note = VALUES(note), prename = VALUES(prename), surname = VALUES(surname), email = VALUES(email), due_date = VALUES(due_date)");
			$cmd->bindParam(":checkoutId", $checkout->getCheckoutId());
			$cmd->bindParam(":note", $checkout->getNote());
			$cmd->bindParam(":prename", $checkout->getPrename());
			$cmd->bindParam(":surname", $checkout->getSurname());
			$cmd->bindParam(":email", $checkout->getEmail());
			$cmd->bindParam(":dueDate", $checkout->getStudentDueDate()->format("Y-m-d"));
			$cmd->execute();
			$con = null;
		}
		catch (PDOException $e) {
			echo "There was an error! " . $e->getMessage();
			exit;
		}
	}

	/**
	 * Gets the currently checked out items of a user
	 * @param $userId int User ID
	 * @param $page int Page number
	 * @param $pageSize int Page size
	 * @return array Array of Checkout objects
	 */
	public function getCurrentCheckouts($userId, $page, $pageSize){
		$this->validateArgs($userId, $page, $pageSize);
		return $this->getCheckouts(SelectionMode::Current, $userId, $page, $pageSize);
	}

	/**
	 * Gets the previously checked out items of a user
	 * @param $userId int User ID
	 * @param $page int Page number
	 * @param $pageSize int Page size
	 * @return array Array of Checkout objects
	 */
	public function getHistoricCheckout($userId, $page, $pageSize){
		$this->validateArgs($userId, $page, $pageSize);
		return $this->getCheckouts(SelectionMode::Past, $userId, $page, $pageSize);
	}

	/**
	 * Gets the all (previously and currently) checked out items of a user
	 * @param $userId int User ID
	 * @param $page int Page number
	 * @param $pageSize int Page size
	 * @return array Array of Checkout objects
	 */
	public function getAllCheckouts($userId, $page, $pageSize){
		$this->validateArgs($userId, $page, $pageSize);
		return $this->getCheckouts(SelectionMode::All, $userId, $page, $pageSize);
	}

	/**
	 * Gets checked out items
	 * @param $selectionMode int Type of checkouts to select
	 * @param $userId int User ID
	 * @param $page int Page number
	 * @param $pageSize int Page size
	 * @return array Checkout objects
	 */
	private function getCheckouts($selectionMode, $userId, $page, $pageSize){
		$pageOffset = $pageSize * ($page - 1);
		$selectionModeClause = array(0 => "AND co.return_date IS NULL", 1 => "AND co.return_date IS NOT NULL", 2 => "");

		try {
			//TODO: Use table locks
			$con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$cmd = $con->prepare("SELECT lbl.name as shelfmark, plbl.name AS name, co.checkout_id, co.object_id, co.checkout_date, co.return_date, co.due_date, con.note, con.prename, con.surname, con.email, con.due_date as student_due_date
									FROM ca_object_checkouts AS co
										INNER JOIN ca_objects AS obj ON co.object_id = obj.object_id
										INNER JOIN ca_object_labels AS lbl ON obj.object_id = lbl.object_id
										INNER JOIN ca_object_labels AS plbl ON obj.parent_id = plbl.object_id
										LEFT JOIN tud_checkout_notes AS con ON co.checkout_id = con.checkout_id
									WHERE co.user_id = :userId  AND lbl.is_preferred = 1 AND co.checkout_date IS NOT NULL
									ORDER by co.due_date ASC, lbl.name ASC;");

			$cmd->bindParam(":userId", $userId, PDO::PARAM_INT);
			//$cmd->bindParam(":pageOffset", $pageOffset, PDO::PARAM_INT);
			//$cmd->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
			$cmd->execute();
			$result = $cmd->fetchAll(PDO::FETCH_CLASS, "Checkout");
			$con = null;
		}
		catch (PDOException $e) {
			echo "There was an error! " . $e->getMessage();
			exit;
		}

		return $result;

	}

	/**
	 * Validate arguments, throws if argument is invalid/out of range
	 *
	 * @param $userId int User ID
	 * @param $page int Page number
	 * @param $pageSize int  Page size
	 */
	private function validateArgs($userId, $page, $pageSize)
	{
		if(!is_integer($userId)) {
			throw new InvalidArgumentException("userId");
		}
		if(!is_integer($page)) {
			throw new InvalidArgumentException("page");
		}
		if(!is_integer($pageSize)) {
			throw new InvalidArgumentException("pageSize");
		}
		if($userId < 1){
			throw new OutOfBoundsException("userId");
		}
		if($page < 0){
			throw new OutOfBoundsException("page");
		}
		if($pageSize < 1){
			throw new OutOfBoundsException("pageSize");
		}
	}
}

/**
 * Class SelectionModel
 * Well, we don't have enums in PHP *sigh*
 */
abstract class SelectionMode{
	const Current = 0;
	const Past = 1;
	const All = 2;
}