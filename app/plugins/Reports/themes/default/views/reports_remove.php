<?php

// serve remove table

require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

echo "<b>"._t("Sort out list")."</b><br><br>";
$func = new Functions();

$IDs = $func->getRemoveIDs();
$sizeofIDs = count($IDs);

$inventory = $func->getUnused();
$sizeofInventory = count($inventory);

if ($IDs == false || $inventory == false) {
    echo _("There are not items to be sorted out") . '<br><br>
            <a href=' . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/unused >"._("Show unused media")."</a>";
} else {
    //print list, if remove_list and inventory list aren't empty
    if ($sizeofIDs != 0 || $sizeofInventory != 0) {
        ?>
        <div class="sectionBox">
            <table id="tudUnusedList" class="listtable" width="100%" border="0" cellpadding="0" cellspacing="1">
                <thead>
                <tr>
                    <th>
                        <?php _p("Authors"); ?>
                    </th>
                    <th>
						<?php _p("Title"); ?>
                    </th>
                    <th>
						<?php _p("Publisher"); ?>
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

                </tr>
                </thead>
                <tbody>

                <?php
                for ($i = 0; $i <= $sizeofInventory - 1; $i++) {

                    //only print objects, which are in remove_list
                    if (in_array($inventory[$i]['Object_ID'], $IDs)) {

                        print("<tr><td>" . $inventory[$i]['Autoren'] . "</td><td>" . $inventory[$i]['Titel'] . "</td> <td>" . $inventory[$i]['Verlag'] . "</td> <td>" . $inventory[$i]['Jahr'] . "</td><td>" . $inventory[$i]['Objekttyp'] . "</td><td>" . $inventory[$i]['Signatur'] . "</td><td>" . $inventory[$i]['Barcode'] . "</td><td>" . $inventory[$i]['Last Used'] . "</td></tr>");

                    }
                }

                ?>
                </tbody>

            </table>
            <?php
            //print links
            if ($sizeofIDs != 0 || $sizeofInventory != 0) {
                printf(" <a href=" . __CA_URL_ROOT__ . "/service.php/Reports/Service/remove_dl >"._t("Download list")."</a> /
                        <a href=" . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/remove_clear >"._t("Discard list")."</a> /
                        <a href=" . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/remove_delete >"._t("Delete all items")."</a>  ");
            }

            ?>

        </div>

    <?php

    } else {
        echo _t("No items to be sorted out");
    }

}

?>
