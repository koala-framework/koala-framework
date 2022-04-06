<?php
class Kwf_Events_Event_Model_Serialization_ColumnChanged extends Kwf_Events_Event_Row_Abstract
{
    public $serializationColumn;
    public function __construct(Kwf_Model_Row_Abstract $row, $serializationColumn)
    {
        $this->serializationColumn = $serializationColumn;
        parent::__construct($row);
    }
}
