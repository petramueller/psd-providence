<?php
 /* ----------------------------------------------------------------------
 * main_html.php
 * ----------------------------------------------------------------------
 *
 * Copyright (c) 2015, Jasper Dunker.
 * All rights reserved.
 *
 *  ----------------------------------------------------------------------
 */
$checkouts = $this->getVar('checkouts');
?>
<style type="text/css">
	.tud-clearfix:after {
		content:"";
		display:table;
		clear:both;
	}
	.tud-book-title-row{
		width:424px;
		float:left;
	}
	.tud-book-title-display{
		display: inline-block;
		width:325px;
		white-space: nowrap;
		overflow: hidden;
		float:left;
		text-overflow:ellipsis;
	}
	.tud-book-title-due-date{
		display: inline-block;
		float:right
	}
	.tud-student-row{
		width:412px;
		margin-left:12px;
		float: left;
	}
	.tud-student-name{
		display: inline-block;
		width:318px;
		white-space: nowrap;
		overflow: hidden;
		float:left;
		text-overflow:ellipsis;
	}
	.tud-message{
		display: block;
		text-align:center;
		width:424px;
	}
</style>

	<?php
	if (sizeof($checkouts)) {
		foreach ($checkouts as $checkout) {
			$student_due_date;
			if ($checkout->getStudentDueDate() instanceof DateTime) {
				$student_due_date = $checkout->getStudentDueDate();
			} else {
				$student_due_date = DateTime::createFromFormat("Y-m-d", $checkout->getStudentDueDate());
			}
			$student_due_date = $student_due_date->format("d.m.y");
			$dueDate = $checkout->getDueDate()->format("d.m.y");

			$daysLeft = $checkout->getDaysLeft();
			$color = "#008800";
			if(substr($daysLeft, 0, 1) === "-"){
				$color = "#cf0000";
			} else if ($daysLeft === "+0"){
				$color = "#d1d100";
				$daysLeft = "0";
			}

			$studentDaysLeft = $checkout->getDaysLeftStudent();
			$colorStudent = "#008800";
			if(substr($studentDaysLeft, 0, 1) === "-"){
				$colorStudent = "#cf0000";
			} else if ($studentDaysLeft === "+0"){
				$colorStudent = "#d1d100";
				$studentDaysLeft = "0";
			}

			?>
			<div class="tud-clearfix">
			<div class="tud-book-title-row">
				<span class="tud-book-title-display"><em><?php echo $checkout->getShelfMark(); ?></em>: <?php echo $checkout->getName(); ?></span> <span
					class="tud-book-title-due-date"><?php echo "{$dueDate} (<span style=\"color:{$color}\">{$daysLeft}"; ?></span>)</span>
			</div>
			<?php
				if($checkout->getPrename() != "" && $checkout->getSurname()  != "" && $checkout->getEmail() != "" ) {
					?>
					<div class="tud-student-row">
						<span class="tud-student-name"><?php echo $checkout->getPrename(); ?> <?php echo $checkout->getSurname();?>, <a href="mailto:<?php echo $checkout->getEmail(); ?>"><?php echo $checkout->getEmail(); ?></a></span>
						<span class="tud-book-title-due-date"><?php echo"{$student_due_date} (<span style=\"color:{$colorStudent}\">{$studentDaysLeft}"; ?></span>)</span>
					</div>
				<?php
				}
				?>
			</div>
		<?php
		}
	} else {
		?>
<div class="tud-clearfix">
			<em class="tud-message"><?php _p("You do not have any items checke out."); ?></em>
</div>
<?php
	}
?>
