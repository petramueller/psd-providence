<?php

// serve remove table

require_once(__CA_APP_DIR__ . "/plugins/Reports/models/class.php");
require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

echo "<b>Aussonderungsliste</b><br><br>";
$func = new Functions();

$IDs = $func->getRemoveIDs();
$sizeofIDs = count($IDs);

$inventory = $func->getUnused();
$sizeofInventory = count($inventory);

if ($IDs == false || $inventory == false) {
    echo 'Es sind keine Medien in der Aussonderungsliste oder es ist ein Fehler aufgetreten.<br><br>
            <a href=' . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/unused >ungenutzte Medien anzeigen</a>";
} else {
    //print list, if remove_list and inventory list aren't empty
    if ($sizeofIDs != 0 || $sizeofInventory != 0) {
        ?>
        <div class="sectionBox">
            <table id="tudUnusedList" class="listtable" width="100%" border="0" cellpadding="0" cellspacing="1">
                <thead>
                <tr>
                    <th>
                        Autoren
                    </th>
                    <th>
                        Titel
                    </th>
                    <th>
                        Verlag
                    </th>
                    <th>
                        Jahr
                    </th>
                    <th>
                        Medientyp
                    </th>
                    <th>
                        Signatur
                    </th>
                    <th>
                        Barcode
                    </th>
                    <th>
                        ungenutzt in Tagen
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
                printf(" <a href=" . __CA_URL_ROOT__ . "/service.php/Reports/Service/remove_dl >Liste herunterladen</a> /
                        <a href=" . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/remove_clear >Liste verwerfen</a> /
                        <a href=" . substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], "/")) . "/remove_delete >alle Medien löschen</a>  ");
            }

            ?>

        </div>

    <?php

    } else {
        echo 'Es sind keine Medien in der Aussonderungsliste.';
    }

}

?>
