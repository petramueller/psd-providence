<?php
/* ----------------------------------------------------------------------
 * app/printTemplates/labels/local/tud_all_copies.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Mathias Gude
 * Copyright 2015
 *
 * This label was made for the Chair of Wirtschaftsinformatik, esp. Systems Development
 * at the Technische Universität Dresden.
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name TUD LS Systementwicklung
 * @type label
 * @pageSize a4
 * @pageOrientation portrait
 * @tables ca_objects
 * @marginLeft 0.0cm
 * @marginRight 0.0cm
 * @marginTop 0.45cm
 * @marginBottom 0.45cm
 * @horizontalGutter 0.0cm
 * @verticalGutter 0.0cm
 * @labelWidth 10.5cm
 * @labelHeight 4.8cm
 *
 * ----------------------------------------------------------------------
 */

require_once(__CA_MODELS_DIR__ . "/ca_objects.php");
require_once(__CA_MODELS_DIR__ . "/ca_entities.php");

$vo_result = $this->getVar('result');

$path = getcwd()."/app/printTemplates/labels/pdf.css";
$path = "\"".$path."\"";

?>

<head>
    <link href=<?php $path?> rel="stylesheet">
</head>

<div class="smallText" style="position: absolute; left: 0.7cm; top: 0.4cm; width: 8cm; height: 2cm; font-size: 10px; overflow: hidden;">

    <?php
    $parent_id = $vo_result->get('ca_objects.parent_id');
    $copy = new ca_objects($parent_id);
    $type_code = $copy->getTypeCode();

    if ($type_code == "book" || $type_code == "other") {

        $object = new ca_objects($parent_id);
        $object_name = $object->get('ca_objects.preferred_labels.name').", ";

        if ($type_code == "book") {
            $identifiers = array("first_author", "author");
            $identifier = "published";
        } else {
            $identifiers = array("first_author","author");
            $identifier = "published";
        }
        $authors = "";

        foreach($identifiers as $id) {

            $author = $object->get("ca_entities", array("returnAsArray" => true, "restrictToRelationshipTypes" => array($id)));

            foreach($author as $x) {
                reset($x);
                $authorSurname = $x["surname"];
                $authorForename = mb_substr($x["forename"], 0, 1, "UTF-8").".";
                $authors = $authors. $authorSurname. ", ". $authorForename. "; ";
            }
        }

        $publisher = $object->get("ca_entities", array("returnAsArray" => true, "restrictToRelationshipTypes" => array($identifier)));
        $publisher_name = reset($publisher)["surname"].", ";
        $publisher_id = reset($publisher)["entity_id"];
        $publisher = new ca_entities($publisher_id);
        $publisher_location = $publisher->get("ca_entities.publisher_location").", ";

        echo $authors, $object_name, $publisher_name, $publisher_location;

    } elseif ($type_code == "paper") {

        $paper = new ca_objects($parent_id);
        $paper_name = $paper->get('ca_objects.preferred_labels.name').", ";

        $identifier = "first_author";
        $author = $paper->get("ca_entities", array("returnAsArray" => true, "restrictToRelationshipTypes" => array($identifier)));
        $authorSurname = reset($author)["surname"];
        $authorForename = reset($author)["forename"];
        $authorForename = $authorForename[0].".";
        $authorMain = $authorSurname. ", ". $authorForename. "; ";

        echo $authorMain, $paper_name;
    }  else {
        print "The object type is not supported, only child objects of 'book', 'paper' and 'other' are!";
        return;
    }
    ?>

    {{{^ca_objects.parent.year}}}
</div>

<div class="barcode" style="position: absolute; left: 0.45cm; top: 2.5cm; width: 8cm; height: 1cm; overflow: hidden;">
    {{{barcode:code128:30:^ca_objects.barcode}}}
</div>

<div class="titleText" style="position: absolute; left: 0.7cm; top: 3.7cm; width: 8cm; height: 1cm; overflow: hidden;">
    {{{^ca_objects.preferred_labels.name}}}
</div>





