<?php
/**
 * Wenn bei $targetComponent der Cache gelöscht wird, soll er auch hier gelöscht
 * werden
 */
class Kwf_Component_Cache_Meta_Component extends Kwf_Component_Cache_Meta_Abstract
{
    private $_sourceComponent;

    public function __construct(Kwf_Component_Data $sourceComponent)
    {
        $this->_sourceComponent = $sourceComponent;
    }

    public function getSourceComponent()
    {
        return $this->_sourceComponent;
    }

    public static function getDeleteWhere($row)
    {
        return array(
            'db_id' => $row->target_db_id
        );
    }
}