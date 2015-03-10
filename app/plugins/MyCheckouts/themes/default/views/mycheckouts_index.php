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
		</tr>
		</thead>
		<tbody>
		<?php
		//$t_order->set('order_type', 'L');
		if (sizeof($checkouts)) {
			foreach($checkouts as $checkout) {
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
				</tr>
			<?php
			}
		} else {
			?>
			<tr>
				<td colspan="3" align="center"><?php print _t("You don't have any items checked out."); ?></td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>
</div>


