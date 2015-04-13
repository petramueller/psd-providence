<?php

// clear remove table

require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

$func = new Functions();

if ($func->clearRemoveList() == true) {
    echo _t("List successfully deleted");
} else {
    echo _t("An error occurred");
}

?>
