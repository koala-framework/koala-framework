<?php
/**
 * Wenn eine Row des Childmodels geändert wird, wird der Komponentencache mit dem
 * Wert der Spalte component_id gelöscht
 */
class Kwf_Component_Cache_Meta_Static_ChildModel extends Kwf_Component_Cache_Meta_Static_OwnModel
{
    public function getModelname($componentClass)
    {
        return Kwc_Abstract::getSetting($componentClass, 'childModel');
    }
}