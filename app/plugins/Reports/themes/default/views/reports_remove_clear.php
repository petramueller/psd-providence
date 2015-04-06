<?php

// clear remove table

require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

$func = new Functions();

if ($func->clearRemoveList() == true) {
    echo 'Die Aussonderungsliste wurde erfolgreich gelöscht.';
} else {
    echo 'Es ist ein Fehler aufgetreten.';
}

?>
