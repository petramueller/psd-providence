<?php
/*
    Database interaction
*/

require_once(__CA_MODELS_DIR__ . "/ca_objects.php");
require_once(__CA_MODELS_DIR__ . "/ca_entities.php");



class Functions
{
//queries database to retrieve full inventory
    public function getInventory()
    {

        try {

            $sql = "SELECT DISTINCT     GETCATEGORY(t1.Parent_ID, 57) AS 'Kategorie',     t1.Signatur,     t1.Parent_ID,     t1.Object_ID,     t1.Titel,     t1.Objekttyp,     t2.Autoren,     t4.Verlag,     GETATTRIBUTE(t1.Parent_ID, 57, 'year') AS 'Jahr',     GETATTRIBUTE(t1.Object_ID, 57, 'barcode') AS 'Barcode',     t5.ISBN_ISSN,     t1.Quelle FROM     (SELECT DISTINCT         cal1.object_id AS 'Parent_ID',             co.object_id AS 'Object_ID',             cal1.name AS 'Titel',             clit1.name_singular AS 'Objekttyp',             cal2.name AS 'Signatur',             clit2.name_singular as 'Quelle'     FROM         ca_objects AS co     INNER JOIN ca_object_labels AS cal1 ON co.parent_id = cal1.object_id     INNER JOIN ca_object_labels AS cal2 ON co.object_id = cal2.object_id     INNER JOIN ca_list_item_labels AS clit1 ON co.type_id = clit1.item_id     LEFT JOIN ca_list_item_labels AS clit2 ON co.source_id = clit2.item_id     WHERE         clit1.locale_id = get_language_code('Deutsch') AND co.deleted = 0) AS t1         LEFT JOIN     (SELECT         ce.entity_id AS 'Entity_ID',             coxe.object_id AS 'Object_ID',             GROUP_CONCAT(CONCAT_WS('', cel.surname, ', ', SUBSTRING(cel.forename, 1, 1), '. ')                 SEPARATOR '; ') AS 'Autoren'     FROM         ca_entities AS ce     INNER JOIN ca_entity_labels AS cel ON ce.entity_id = cel.entity_id     INNER JOIN ca_list_item_labels AS clit ON ce.type_id = clit.item_id     LEFT JOIN ca_objects_x_entities AS coxe ON ce.entity_id = coxe.entity_id     INNER JOIN ca_relationship_type_labels AS crtl ON coxe.type_id = crtl.type_id     WHERE         clit.locale_id = 2             AND crtl.locale_id = 2             AND ce.deleted = 0             AND clit.name_singular <> 'Verlag'     GROUP BY coxe.object_id     ORDER BY coxe.object_id , crtl.type_id) AS t2 ON t1.Parent_ID = t2.Object_ID         LEFT JOIN     (SELECT         ce.entity_id AS 'Entity_ID',             coxe.object_id AS 'Object_ID',             crtl.typename AS 'Beziehung',             clit.name_singular AS 'Typ',             cel.surname AS 'Verlag'     FROM         ca_entities AS ce     INNER JOIN ca_entity_labels AS cel ON ce.entity_id = cel.entity_id     INNER JOIN ca_list_item_labels AS clit ON ce.type_id = clit.item_id     LEFT JOIN ca_objects_x_entities AS coxe ON ce.entity_id = coxe.entity_id     INNER JOIN ca_relationship_type_labels AS crtl ON coxe.type_id = crtl.type_id     WHERE         clit.locale_id = 2             AND crtl.locale_id = 2             AND ce.deleted = 0             AND clit.name_singular = 'Verlag') AS t4 ON t1.Parent_ID = t4.Object_ID         LEFT JOIN     (SELECT         ca.row_id AS 'Object_ID',             ca.element_id,             cml.element_code,             cav.value_longtext1 AS 'ISBN_ISSN'     FROM         ca_attributes AS ca     INNER JOIN ca_metadata_elements AS cml ON cml.element_id = ca.element_id     INNER JOIN ca_attribute_values AS cav ON cav.attribute_id = ca.attribute_id     WHERE         table_num = 57 AND element_code = 'isbn'             OR element_code = 'issn') AS t5 ON t1.Parent_ID = t5.Object_ID ORDER BY Kategorie, t1.Parent_ID, t1.Objekttyp DESC;";
            $con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cmd = $con->prepare($sql);
            $cmd->execute();

            $result = $cmd->fetchALL(PDO::FETCH_ASSOC);

            $con = NULL;

        } catch (PDOException $e) {
            $con = null;
            throw $e;
            return false;
        }

        return $result;

    }

//queries database to retrieve SLUB items only
    public function getSlub()
    {

        try {

            $sql = "SELECT DISTINCT     GETATTRIBUTE(t1.Object_ID, 57, 'barcode') AS 'Barcode',     t1.Signatur,     t2.Autoren,     t1.Titel,     t4.Verlag,     GETATTRIBUTE(t1.Parent_ID, 57, 'year') AS 'Jahr',     t5.ISBN_ISSN FROM     (SELECT DISTINCT         cal1.object_id AS 'Parent_ID',             co.object_id AS 'Object_ID',             cal1.name AS 'Titel',             clit1.name_singular AS 'Objekttyp',             cal2.name AS 'Signatur',             clit2.name_singular as 'Quelle'     FROM         ca_objects AS co     INNER JOIN ca_object_labels AS cal1 ON co.parent_id = cal1.object_id     INNER JOIN ca_object_labels AS cal2 ON co.object_id = cal2.object_id     INNER JOIN ca_list_item_labels AS clit1 ON co.type_id = clit1.item_id     LEFT JOIN ca_list_item_labels AS clit2 ON co.source_id = clit2.item_id     WHERE         clit1.locale_id = get_language_code('Deutsch') AND co.deleted = 0) AS t1         LEFT JOIN     (SELECT         ce.entity_id AS 'Entity_ID',             coxe.object_id AS 'Object_ID',             GROUP_CONCAT(CONCAT_WS('', cel.surname, ', ', SUBSTRING(cel.forename, 1, 1), '. ')                 SEPARATOR '; ') AS 'Autoren'     FROM         ca_entities AS ce     INNER JOIN ca_entity_labels AS cel ON ce.entity_id = cel.entity_id     INNER JOIN ca_list_item_labels AS clit ON ce.type_id = clit.item_id     LEFT JOIN ca_objects_x_entities AS coxe ON ce.entity_id = coxe.entity_id     INNER JOIN ca_relationship_type_labels AS crtl ON coxe.type_id = crtl.type_id     WHERE         clit.locale_id = 2             AND crtl.locale_id = 2             AND ce.deleted = 0             AND clit.name_singular <> 'Verlag'     GROUP BY coxe.object_id     ORDER BY coxe.object_id , crtl.type_id) AS t2 ON t1.Parent_ID = t2.Object_ID         LEFT JOIN     (SELECT         ce.entity_id AS 'Entity_ID',             coxe.object_id AS 'Object_ID',             crtl.typename AS 'Beziehung',             clit.name_singular AS 'Typ',             cel.surname AS 'Verlag'     FROM         ca_entities AS ce     INNER JOIN ca_entity_labels AS cel ON ce.entity_id = cel.entity_id     INNER JOIN ca_list_item_labels AS clit ON ce.type_id = clit.item_id     LEFT JOIN ca_objects_x_entities AS coxe ON ce.entity_id = coxe.entity_id     INNER JOIN ca_relationship_type_labels AS crtl ON coxe.type_id = crtl.type_id     WHERE         clit.locale_id = 2             AND crtl.locale_id = 2             AND ce.deleted = 0             AND clit.name_singular = 'Verlag') AS t4 ON t1.Parent_ID = t4.Object_ID         LEFT JOIN     (SELECT         ca.row_id AS 'Object_ID',             ca.element_id,             cml.element_code,             cav.value_longtext1 AS 'ISBN_ISSN'     FROM         ca_attributes AS ca     INNER JOIN ca_metadata_elements AS cml ON cml.element_id = ca.element_id     INNER JOIN ca_attribute_values AS cav ON cav.attribute_id = ca.attribute_id     WHERE         table_num = 57 AND element_code = 'isbn'             OR element_code = 'issn') AS t5 ON t1.Parent_ID = t5.Object_ID WHERE GETATTRIBUTE(t1.Object_ID, 57, 'barcode') IS NOT NULL AND (Quelle = 'SLUB' OR Quelle IS NULL) ORDER BY Barcode";
            $con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cmd = $con->prepare($sql);
            $cmd->execute();

            $result = $cmd->fetchALL(PDO::FETCH_ASSOC);

            $con = NULL;

        } catch (PDOException $e) {
            $con = null;
            throw $e;
            return false;
        }

        return $result;

    }

//queries database to retrieve unused items
    public function getUnused()
    {
        try {
            $sql = "SELECT DISTINCT     GETCATEGORY(t1.Parent_ID, 57) AS 'Kategorie',     t1.Signatur,     t1.Parent_ID,     t1.Object_ID,     t1.Titel,     t1.Objekttyp,     t2.Autoren,     t4.Verlag,     GETATTRIBUTE(t1.Parent_ID, 57, 'year') AS 'Jahr',     GETATTRIBUTE(t1.Object_ID, 57, 'barcode') AS 'Barcode',     t5.ISBN_ISSN,     LASTUSED(t1.Object_ID) AS 'Last Used',     t1.Quelle FROM     (SELECT DISTINCT         cal1.object_id AS 'Parent_ID',             co.object_id AS 'Object_ID',             cal1.name AS 'Titel',             clit1.name_singular AS 'Objekttyp',             cal2.name AS 'Signatur',             clit2.name_singular as 'Quelle'     FROM         ca_objects AS co     INNER JOIN ca_object_labels AS cal1 ON co.parent_id = cal1.object_id     INNER JOIN ca_object_labels AS cal2 ON co.object_id = cal2.object_id     INNER JOIN ca_list_item_labels AS clit1 ON co.type_id = clit1.item_id     LEFT JOIN ca_list_item_labels AS clit2 ON co.source_id = clit2.item_id     WHERE         clit1.locale_id = get_language_code('Deutsch') AND co.deleted = 0) AS t1         LEFT JOIN     (SELECT         ce.entity_id AS 'Entity_ID',             coxe.object_id AS 'Object_ID',             GROUP_CONCAT(CONCAT_WS('', cel.surname, ', ', SUBSTRING(cel.forename, 1, 1), '. ')                 SEPARATOR '; ') AS 'Autoren'     FROM         ca_entities AS ce     INNER JOIN ca_entity_labels AS cel ON ce.entity_id = cel.entity_id     INNER JOIN ca_list_item_labels AS clit ON ce.type_id = clit.item_id     LEFT JOIN ca_objects_x_entities AS coxe ON ce.entity_id = coxe.entity_id     INNER JOIN ca_relationship_type_labels AS crtl ON coxe.type_id = crtl.type_id     WHERE         clit.locale_id = 2             AND crtl.locale_id = 2             AND ce.deleted = 0             AND clit.name_singular <> 'Verlag'     GROUP BY coxe.object_id     ORDER BY coxe.object_id , crtl.type_id) AS t2 ON t1.Parent_ID = t2.Object_ID         LEFT JOIN     (SELECT         ce.entity_id AS 'Entity_ID',             coxe.object_id AS 'Object_ID',             crtl.typename AS 'Beziehung',             clit.name_singular AS 'Typ',             cel.surname AS 'Verlag'     FROM         ca_entities AS ce     INNER JOIN ca_entity_labels AS cel ON ce.entity_id = cel.entity_id     INNER JOIN ca_list_item_labels AS clit ON ce.type_id = clit.item_id     LEFT JOIN ca_objects_x_entities AS coxe ON ce.entity_id = coxe.entity_id     INNER JOIN ca_relationship_type_labels AS crtl ON coxe.type_id = crtl.type_id     WHERE         clit.locale_id = 2             AND crtl.locale_id = 2             AND ce.deleted = 0             AND clit.name_singular = 'Verlag') AS t4 ON t1.Parent_ID = t4.Object_ID         LEFT JOIN     (SELECT         ca.row_id AS 'Object_ID',             ca.element_id,             cml.element_code,             cav.value_longtext1 AS 'ISBN_ISSN'     FROM         ca_attributes AS ca     INNER JOIN ca_metadata_elements AS cml ON cml.element_id = ca.element_id     INNER JOIN ca_attribute_values AS cav ON cav.attribute_id = ca.attribute_id     WHERE         table_num = 57 AND element_code = 'isbn'             OR element_code = 'issn') AS t5 ON t1.Parent_ID = t5.Object_ID ORDER BY Kategorie, t1.Parent_ID, t1.Objekttyp DESC;";
            $con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cmd = $con->prepare($sql);
            $cmd->execute();

            $result = $cmd->fetchALL(PDO::FETCH_ASSOC);

            $con = NULL;

        } catch (PDOException $e) {
            $con = null;
            throw $e;
            return false;
        }

        return $result;

    }

    //saves object_ids of items to remove list
    public function saveRemoveList($object_IDs)
    {
        if ($object_IDs == NULL) {
            return false;
        }

        $sql = "INSERT IGNORE INTO tud_remove (object_id) VALUES ";

        $IDsize = sizeof($object_IDs);
        $i = 1;

        //add object_ids to SQL query
        foreach ($object_IDs as $object_ID) {
            if ($i <= $IDsize - 1) {
                $object_ID = intval($object_ID);
                $sql .= " ( $object_ID ), ";
                $i++;
            } else {
                $object_ID = intval($object_ID);
                $sql .= " ( $object_ID ); ";
            }

        }

        //execute SQLstatement
        try {

            $con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cmd = $con->prepare($sql);
            $cmd->execute();

            $con = NULL;

        } catch (PDOException $e) {
            $con = null;
            throw $e;
            return false;
        }

        return true;
    }

    //retrieve object_ids of items which are on remove list
    public function getRemoveIDs()
    {

        try {
            $sql = "SELECT * FROM tud_remove";
            $con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cmd = $con->prepare($sql);
            $cmd->execute();

            $result = $cmd->fetchALL(PDO::FETCH_COLUMN);

            $con = NULL;

        } catch (PDOException $e) {
            $con = null;
            throw $e;
            return false;
        }

        return $result;

    }

    //clear remove list
    public function clearRemoveList()
    {

        try {
            $sql = "TRUNCATE TABLE tud_remove;";

            $con = new PDO(sprintf("mysql:host=%s;dbname=%s", __CA_DB_HOST__, __CA_DB_DATABASE__), __CA_DB_USER__, __CA_DB_PASSWORD__);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $cmd = $con->prepare($sql);
            $cmd->execute();

            $con = NULL;

        } catch (PDOException $e) {
            $con = null;
            throw $e;
            return false;
        }

        return true;


    }

    //delete items
    //returns error array($Obj_ID, ...)
    public function deleteItems($IDs)
    {
        $error = NULL;

        foreach ($IDs as $ID) {

            $object = new ca_objects($ID);

            if (!($object instanceof ca_objects)) {
                $error = true;
            }

            if (!$object->setMode(ACCESS_WRITE)) {
                $error = true;
            }
            if (!$object->delete()) {
                $error = true;
            }

            unset($object);

        }

        return $error;

    }
}
