<?php
// handle error messages from remove_list deletion

require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

$error = $this->getVar("error");

if ($error != NULL) {
	echo _t("An error occurred");
} else {
	echo _t("All selected items successfully deleted");
}


?>
