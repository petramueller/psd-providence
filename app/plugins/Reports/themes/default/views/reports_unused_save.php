<?php
// save ObjectIDs from POST to DB


require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

$func = new Functions();
$save = $func->saveRemoveList($_POST['list']);  //type check inside of saveRemoveList



if ($save == true) {
    printf(_t("List saved successfully") . "  <a href=" . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/remove >"._t("Show")."</a> ");
} else {
    printf(_t("An error occurred."));
}

?>

