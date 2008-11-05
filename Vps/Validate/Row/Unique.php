<?php
class Vps_Validate_Row_Unique extends Vps_Validate_Row_Abstract
{
    const NOT_UNIQUE = 'notUnique';

    public function __construct()
    {
        $this->_messageTemplates[self::NOT_UNIQUE] = trlVps("'%value%' does allready exist");
    }

    public function isValidRow($value, $row)
    {
        $valueString = (string)$value;
        $this->_setValue($valueString);

        $select = $row->getModel()->select()
            ->whereEquals($this->_field, $valueString);
        $primaryKey = $row->getModel()->getPrimaryKey();
        if ($row->$primaryKey) {
            $select->whereNotEquals($primaryKey, $row->$primaryKey);
        }
        if ($row->getModel()->countRows($select)) {
            $this->_error(self::NOT_UNIQUE);
            return false;
        }

        return true;
    }
}
