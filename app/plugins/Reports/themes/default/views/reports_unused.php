<?php


// input of unused days & submission to /unused_list via POST

require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

?>

<form action="<?php echo substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/unused_list" ?> "
      method="post">
    Tage, nach denen ein Buch als ungenutzt gilt: <input type="text" name="days"/><br/>
    <input type="submit" name="submit" value="Anzeigen"/>
</form>

<?php

?>
