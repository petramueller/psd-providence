<?php
// save ObjectIDs from POST to DB


require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

$func = new Functions();
$save = $func->saveRemoveList($_POST['list']);  //type check inside of saveRemoveList



if ($save == true) {
    printf("Liste wurde erfolgreich gespeichert.  <a href=" . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/remove >Anzeigen</a> ");
} else {
    printf("Es ist ein Fehler aufgetreten.");
}

?>

