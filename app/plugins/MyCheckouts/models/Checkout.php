<?php
 /* ----------------------------------------------------------------------
 * Checkout.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */

/**
 * Class Checkout
 * Simple class for representing records (might be called POPO)
 */
class Checkout {
	public $shelfmark = null;
	public $name = null;
	public $checkout_id = null;
	public $object_id = null;
	public $checkout_date = null;
	public $due_date = null;
	public $note = null;
	public $prename = null;
	public $surname = null;
	public $email = null;
	public $student_due_date = null;
	public $note_id = null;

	/**
	 * @return string
	 */
	public function getShelfMark()
	{
		return $this->shelfmark;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getCheckoutId()
	{
		return $this->checkout_id;
	}

	/**
	 * @return int
	 */
	public function getObjectId()
	{
		return $this->object_id;
	}

	/**
	 * @return DateTime
	 */
	public function getCheckoutDate()
	{
		$utcDate = DateTime::createFromFormat('U', $this->checkout_date, new DateTimeZone("UTC"));
		return $utcDate->setTimezone(new DateTimeZone(date_default_timezone_get()));
	}

	/**
	 * @return DateTime
	 */
	public function getDueDate()
	{
		$utcDate = DateTime::createFromFormat('U', $this->due_date);
		return $utcDate->setTimezone(new DateTimeZone(date_default_timezone_get()));
	}

	/**
	 * @return string $note
	 */
	public function getNote()
	{
		return $this->note;
	}

	/**
	 * @return string Prename
	 */
	public function getPrename()
	{
		return $this->prename;
	}

	/**
	 * @return string Surname
	 */
	public function getSurname()
	{
		return $this->surname;
	}

	/**
	 * @return string Email
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @return DateTime Student due date
	 */
	public function getStudentDueDate()
	{
		return $this->student_due_date;
	}

	/**
	 * @return int Note ID
	 */
	public function getNoteId()
	{
		return $this->note_id;
	}

	public function getDaysLeft(){
		$now = new DateTime("NOW");
		$now->setTime(0, 0, 0);
		$now->setTimezone(new DateTimeZone(date_default_timezone_get()));
		$interval = $now->diff($this->getDueDate());
		//PHP sucks: We cannot format the difference with DateInterval::format("%R%d") because PHP sucks...
		$prefix = "+";
		if($now > $this->getDueDate()){
			$prefix = "-";
		} else if ($now === $this->getDueDate()){
			$prefix = "";
		}
		return $prefix . $interval->days;
	}
}