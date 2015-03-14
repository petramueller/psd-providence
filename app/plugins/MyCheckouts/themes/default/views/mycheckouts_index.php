<?php
require_once(__CA_APP_DIR__ . "/plugins/MyCheckouts/models/Checkout.php");
$checkouts = $this->getVar("checkouts");
?>
<div class="sectionBox">
	<table id="tudCheckoutList" class="listtable" width="100%" border="0" cellpadding="0" cellspacing="1">
		<thead>
		<tr>
			<th>
				<?php _p('Name'); ?>
			</th>
			<th>
				<?php _p('Shelf Mark'); ?>
			</th>
			<th>
				<?php _p('Due Date'); ?>
			</th>
			<th>
				<?php _p('Actions'); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if (sizeof($checkouts)) {
			$i = -1;
			foreach($checkouts as $checkout) {
				$i++;

				$student_due_date;
				if($checkout->getStudentDueDate() instanceof DateTime){
					$student_due_date= $checkout->getStudentDueDate();
				} else {
					$student_due_date = DateTime::createFromFormat("Y-m-d", $checkout->getStudentDueDate());
				}

				?>
				<tr>
					<td>
						<?php print $checkout->getName(); ?>
					</td>
					<td>
						<?php print $checkout->getShelfMark(); ?>
					</td>
					<td>
						<?php
						$dueDate = $checkout->getDueDate()->format("d.m.Y");
						$daysLeft = $checkout->getDaysLeft();
						$color = "#008800";
						if(substr($daysLeft, 0, 1) === "-"){
							$color = "#cf0000";
						} else if ($daysLeft === "+0"){
							$color = "#d1d100";
							$daysLeft = "0";
						}

						echo "{$dueDate} (<span style=\"color:{$color}\">{$daysLeft}</span>)";
						?>
					</td>
					<td>
						<a href="#" class="note-form-edit" data-tud-id="<?php echo $i; ?>">Show Note</a>
					</td>
				</tr>
				<tr class="tud-note-form-row" id="tudNoteFormRow<?php echo $i; ?>">
					<td colspan="4">
						<form class="tud-note-form" id="tudNoteForm<?php echo $i; ?>">
							<div>
							<input type="hidden" name="checkout_id" value="<?php echo $checkout->getCheckoutId();?>"/>
							<div>
								<label for="prename<?php echo $i;?>">Vorname</label>
								<input type="text" name="prename" placeholder="Vorname" value="<?php echo $checkout->getPrename(); ?>" class="prename" id="prename<?php echo $i;?>"/>
							</div>
							<div>
								<label for="surname<?php echo $i;?>">Nachname</label>
								<input type="text" name="surname" placeholder="Nachname"  value="<?php echo $checkout->getSurname(); ?>" class="surname" id="surname<?php echo $i;?>"/>
							</div>
							<div>
								<label for="email<?php echo $i;?>">E-Mail</label>
								<input type="email" name="email" placeholder="E-Mail-Adresse"  value="<?php echo $checkout->getEmail(); ?>" class="email" id="email<?php echo $i;?>"/>
							</div>
							<div>
								<label for="duedate<?php echo $i;?>">Fällig</label>
								<input type="date" name="due_date" value="<?php echo $student_due_date->format("Y-m-d"); ?>" data-date='{"startView": 2, "openOnMouseFocus": true}' placeholder="dd.mm.jjjj" class="due-date-human" id="due-date<?php echo $i;?>"/>
							</div>
							</div>
							<div class="tud-note">
								<textarea placeholder="Anmerkung" name="note" rows="3"><?php echo $checkout->getNote(); ?></textarea>
							</div>
							<div class="tud-submit">
								<input type="submit" class="tud-note-submit" value="Speichern" id="submit<?php echo $i;?>">
								<span class="tud-ajax-state"></span>
							</div>
						</form>
					</td>
				</tr>
			<?php
			}
		} else {
			?>
			<tr>
				<td colspan="4" align="center"><?php print _t("You don't have any items checked out."); ?></td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>
</div>
<script type="text/javascript" src="<?php echo __CA_URL_ROOT__; ?>/app/plugins/MyCheckouts/themes/default/js/polyfiller.js"></script>
<script type="text/javascript" src="<?php echo __CA_URL_ROOT__; ?>/app/plugins/MyCheckouts/themes/default/js/mycheckouts.js"></script>