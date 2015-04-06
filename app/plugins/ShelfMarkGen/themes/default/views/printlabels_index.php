<?php
require_once(__CA_APP_DIR__ . "/plugins/ShelfMarkGen/models/Label.php");
$labels = $this->getVar("labels");
?>
<div class="sectionBox">
	<form method="post" action="<?php echo __CA_URL_ROOT__; ?>/index.php/ShelfMarkGen/PrintLabels/PrintLabels">
	<table id="tudLabelList" class="listtable" width="100%" border="0" cellpadding="0" cellspacing="1">
		<thead>
		<tr>
			<th>
				<?php _p('Shelf Mark'); ?>
			</th>
			<th>
				<?php _p('Print'); ?>
			</th>
			<th>
				<?php _p('Number of Labels'); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if (sizeof($labels)) {
			$i = -1;
			foreach($labels as $label) {
				$i++;

				?>
				<tr>
					<td>
						<?php print $label->getShelfMark(); ?>
					</td>
					<td>
						<input type="checkbox" checked name="print[<?php echo $i; ?>][shelfmark]" value="<?php echo $label->getShelfMark(); ?>"/>
					</td>
					<td>
						<select name="print[<?php echo $i; ?>][number_of_labels]">
							<option value="1">1</option>
							<option value="2" selected>2</option>
						</select>
					</td>
				</tr>
			<?php
			}
		} else {
			?>
			<tr>
				<td colspan="3" align="center"><?php print _t("No labels to print."); ?></td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>
	<input type="submit" value="Print Labels"/>
	<input type="submit" formaction="<?php echo __CA_URL_ROOT__; ?>/index.php/ShelfMarkGen/PrintLabels/Delete" value="Delete from Queue"/>
	</form>
</div>