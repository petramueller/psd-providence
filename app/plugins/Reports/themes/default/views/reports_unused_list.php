<?php

// DB Query for unused items & redirect to /unused_save with POST of objectIDs

require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

if (!isset($_POST['days']) /*|| !is_int($_POST['days']) */ || $_POST['days'] < 0) {

    echo _t("Please specify a date range") + '<br> <a href="' . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/unused" . '">'._t("back").'</a>';

} else {

    $func = new Functions();
    $inventory = $func->getUnused();
    $days = htmlspecialchars($_POST['days'], ENT_QUOTES, "UTF-8");

    $sizeofInventory = count($inventory);

if ($sizeofInventory != 0) {

//javascript code to check all checkboxes
    ?>
    <script language="JavaScript">
        function toggle(source) {
            checkboxes = document.getElementsByName('list[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>

    <div class="sectionBox">
        <form
            action="<?php echo substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/unused_save" ?>"
            method="POST">
            <table id="tudUnusedList" class="listtable" width="100%" border="0" cellpadding="0" cellspacing="1">
                <thead>
                <tr>
                    <th>
                        <?php _p("Category"); ?>
                    </th>
                    <th>
						<?php _p("Authors"); ?>
                    </th>
                    <th>
						<?php _p("Title"); ?>
                    </th>
                    <th>
						<?php _p("Year"); ?>
                    </th>
                    <th>
						<?php _p("Item Type"); ?>
                    </th>
                    <th>
						<?php _p("Shelf Mark"); ?>
                    </th>
                    <th>
						<?php _p("Bar Code"); ?>
                    </th>
                    <th>
						<?php _p("Unused for Days"); ?>
                    </th>
                    <th>
						<?php _p("Sort Out"); ?>
                    </th>

                </tr>
                </thead>

                <tbody>


                <?php

                for ($i = 0; $i <= $sizeofInventory - 1; $i++) {
                    //only print items which are unused
                    if ($inventory[$i]['Last Used'] >= $days) {

                            print("<tr><td>".$inventory[$i]['Kategorie']."</td><td>" . $inventory[$i]['Autoren'] . "</td><td>" . $inventory[$i]['Titel'] . "</td><td>" . $inventory[$i]['Jahr'] . "</td><td>" . $inventory[$i]['Objekttyp'] . "</td><td>" . $inventory[$i]['Signatur'] . "</td><td>" . $inventory[$i]['Barcode'] . "</td><td>" . $inventory[$i]['Last Used'] . '</td><td><input type="checkbox" name="list[]"  value="' . $inventory[$i]['Object_ID'] . '" ></td></tr>');


                    }
                }

                ?>

                </tbody>

            </table>
            <?php

            print ('<p align="right"><input type="checkbox" onClick="toggle(this)" /> '._t("Select All").'</p>
                        <p align="left"><input type="submit" name="submit" value="'._t("Save for sort out").'" /></p>
                        <a href="' . __CA_URL_ROOT__ . '/service.php/Reports/Service/unused_dl?days=' . $days . '" >'._t("Download list").'</a>  </p>');

            } else {
                print '<td colspan="3" align="center">' . _t("There are not items that haven not been used for") . ' ' . $days . ' '. _t("days.") .'</td>';
            }

            ?>

        </form>
    </div>


<?php
}

?>
