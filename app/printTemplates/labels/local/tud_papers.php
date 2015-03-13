<?php
/**
 * Created by PhpStorm.
 * User: Mathias Gude
 * Date: 09.03.2015
 * Time: 15:41
 */
/* ----------------------------------------------------------------------
 * app/printTemplates/labels/local/tud_papers.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2014 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This source code is free and modifiable under the terms of
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * -=-=-=-=-=- CUT HERE -=-=-=-=-=-
 * Template configuration:
 *
 * @name TUD Arbeiten
 * @type label
 * @pageSize a4
 * @pageOrientation portrait
 * @tables ca_objects
 * @marginLeft 0.0cm
 * @marginRight 0.0cm
 * @marginTop 0.45cm
 * @marginBottom 0.45cm
 * @horizontalGutter 0.0cm
 * @verticalGutter 0.ocm
 * @labelWidth 10.5cm
 * @labelHeight 4.8cm
 *
 * ----------------------------------------------------------------------
 */

require_once(__CA_MODELS_DIR__ . "/ca_objects.php");
require_once(__CA_MODELS_DIR__ . "/ca_entities.php");


$vo_result = $this->getVar('result');
?>

<head>
    <link href="/var/www/html/int/app/printTemplates/labels/pdf.css" rel="stylesheet">
</head>

<div class="smallText" style="position: absolute; left: 0.7cm; top: 0.4cm; width: 8cm; height: 2cm; font-size: 10px; overflow: hidden;">
    <?php
    $parent_id = $vo_result->get('ca_objects.parent_id');
    $paper_copy = new ca_objects($parent_id);
    $paper_name = $paper_copy->get('ca_objects.preferred_labels.name').", ";

    $identifier = "main_paper_author";
    $author = $paper_copy->get("ca_entities", array("returnAsArray" => true, "restrictToRelationshipTypes" => array($identifier)));
    $authorSurname = reset($author)["surname"];
    $authorForename = reset($author)["forename"];
    $authorForename = $authorForename[0].".";
    $authorMain = $authorSurname. ", ". $authorForename. "; ";

    echo $authorMain, $paper_name;
    ?>
    {{{^ca_objects.parent.year}}}
</div>

<div class="titleText" style="position: absolute; left: 0.7cm; top: 3.7cm; width: 8cm; height: 1cm; overflow: hidden;">
    {{{^ca_objects.preferred_labels.name}}}
</div>