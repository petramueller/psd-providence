<?php
/* ServiceController is used to push data to client without sending normal page layout.
 * needed to send costum header information
 * call: /service.php/Reports/Service/remove_dl
 */

require_once(__CA_LIB_DIR__ . '/ca/Service/BaseServiceController.php');
require_once(__CA_APP_DIR__ . "/plugins/Reports/models/functions.php");

//sends headers & data to browser as file download
function sendHeader($filename, $input){

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . sizeof($input));
    echo $input;

}

//returns input as csv table
function putcsv($anyNumberOfParameters)
{
    $input = func_get_args();

    foreach ($input as $one) {
        if ($one == NULL || $one == "") {
            $one = "-";
        }
        $result .= '"' . $one . '",';
    }

    return $result;
}


class ServiceController extends BaseServiceController
{
    public function __construct(&$po_request, &$po_response, $pa_view_paths = null)
    {
        parent::__construct($po_request, $po_response, $pa_view_paths);
    }

    //gets ObjIDs of unused from DB, queries for metadata and sends it as csv file to browser
    public function remove_dl()
    {

        $func = new Functions();
        $inventory = $func->getUnused();

        $IDs = $func->getRemoveIDs();
        $sizeofIDs = count($IDs);

        $csv = NULL; //string containing raw csv data
        $sizeofInventory = count($inventory); //count first dimension of array

        $csv = putcsv("Autoren", "Titel", "Verlag", "Jahr", "ISBN/SSN", "Medientyp", "Signatur", "Barcode") . "\n";

        for ($i = 0; $i <= $sizeofInventory - 1; $i++) {

            //if object_id is in remove list -> delete
            if (in_array($inventory[$i]['Object_ID'], $IDs)) {

                $csv .= putcsv($inventory[$i]['Autoren'], $inventory[$i]['Titel'], $inventory[$i]['Verlag'], $inventory[$i]['Jahr'], $inventory[$i]['ISBN_ISSN'], $inventory[$i]['Objekttyp'], $inventory[$i]['Signatur'], $inventory[$i]['Barcode']) . "\n";

            }
        }

        sendHeader("aussonderungsliste.csv", $csv);


    }

    //queries DB for full inventory and sends it as csv file to browser
    public function inventory_dl_readable()
    {

        $Functions = new Functions();
        $inventory = $Functions->getInventory();

        $csv = NULL; //string containing raw csv data
        $sizeofInventory = count($inventory); //count first dimension of array

        $csv = putcsv("Kategorie", "Autoren", "Titel", "Jahr", "ISBN/SSN", "Medientyp", "Signatur", "Barcode") . "\n";

        for ($i = 0; $i <= $sizeofInventory - 1; $i++) {

            if ($inventory[$i]['Kategorie'] == NULL){
                $inventory[$i]['Kategorie'] = "ohne Kategorie";
            }
                $csv .= putcsv($inventory[$i]['Kategorie'],  $inventory[$i]['Autoren'], $inventory[$i]['Titel'], $inventory[$i]['Jahr'], $inventory[$i]['ISBN_ISSN'], $inventory[$i]['Objekttyp'], $inventory[$i]['Signatur'], $inventory[$i]['Barcode']) . "\n";

        }

        sendHeader("inventar_maschinenlesbar.csv", $csv);

    }

    public function inventory_dl()
    {

        $Functions = new Functions();
        $inventory = $Functions->getInventory();

        $csv = NULL; //string containing raw csv data
        $sizeofInventory = count($inventory); //count first dimension of array

        $csv = putcsv("Kategorie", "Autoren", "Titel", "Jahr", "ISBN/SSN", "Medientyp", "Signatur", "Barcode") . "\n";

        for ($i = 0; $i <= $sizeofInventory - 1; $i++) {

            //print new category, if it doesnt match with the one before
            if ($i == 0 || $inventory[$i]['Kategorie'] != $inventory[$i - 1]['Kategorie']) {

                if ($inventory[$i]['Kategorie'] == NULL) { //check if category is NULL

                    $inventory[$i]['Kategorie'] = "ohne Kategorie";
                }

                $csv .= putcsv($inventory[$i]['Kategorie']) . "\n";
            }

            //omit author/title/year... if it was already printed
            if ($inventory[$i]['Parent_ID'] == $inventory[$i - 1]['Parent_ID']) {

                $csv .= putcsv("", "", "", "", $inventory[$i]['ISBN_ISSN'], $inventory[$i]['Objekttyp'], $inventory[$i]['Signatur'], $inventory[$i]['Barcode']) . "\n";
            } else {    //print full entry
                $csv .= putcsv("",  $inventory[$i]['Autoren'], $inventory[$i]['Titel'], $inventory[$i]['Jahr'], $inventory[$i]['ISBN_ISSN'], $inventory[$i]['Objekttyp'], $inventory[$i]['Signatur'], $inventory[$i]['Barcode']) . "\n";

            }
        }

        sendHeader("inventar.csv", $csv);

    }


    //queries DB for SLUB items and sends it as csv file to browser
    public function slub_dl()
    {

        $Functions = new Functions();
        $inventory = $Functions->getSlub();

        $csv = NULL; //string containing raw csv data
        $sizeofInventory = count($inventory); //count first dimension of array

        //Barcode (sort) , Signatur, Autor, Titel, Verlag, Jahr, ISBN_ISSN

        $csv = putcsv("Barcode", "Signatur", "Autor", "Titel", "Verlag", "Jahr", "ISBN_SSN") . "\n";

        for ($i = 0; $i <= $sizeofInventory - 1; $i++) {
            $csv .= putcsv($inventory[$i]['Barcode'], $inventory[$i]['Signatur'], $inventory[$i]['Autoren'], $inventory[$i]['Titel'], $inventory[$i]['Verlag'], $inventory[$i]['Jahr'], $inventory[$i]['ISBN_ISSN']) . "\n";
        }

        sendHeader("slub.csv", $csv);

    }

    //queries DB for items & unused time and sends it as csv file to browser
    public function unused_dl()
    {

        $Functions = new Functions();
        $inventory = $Functions->getUnused();
        $days = intval ( $_GET['days']);

        $csv = NULL; //string containing raw csv data
        $sizeofInventory = count($inventory); //count first dimension of array

        $csv = putcsv("Kategorie", "Autoren", "Titel", "Jahr", "ISBN/SSN", "Medientyp", "Signatur", "Barcode") . "\n";

        for ($i = 0; $i <= $sizeofInventory - 1; $i++) {

            //only print values older than 'Last Used'
            if ($inventory[$i]['Last Used'] >= $days) {

                    $csv .= putcsv($inventory[$i]['Kategorie'], $inventory[$i]['Autoren'], $inventory[$i]['Titel'], $inventory[$i]['Jahr'], $inventory[$i]['ISBN_ISSN'], $inventory[$i]['Objekttyp'], $inventory[$i]['Signatur'], $inventory[$i]['Barcode']) . "\n";


            }
        }

        sendHeader("ungenutzt.csv", $csv);

    }
}