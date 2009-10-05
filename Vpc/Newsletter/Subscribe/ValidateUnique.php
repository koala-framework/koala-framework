<?php
class Vpc_Newsletter_Subscribe_ValidateUnique extends Vps_Validate_Row_Unique
{
    public function isValidRow($value, $row)
    {
        $valueString = (string)$value;
        $this->_setValue($valueString);

        $select = $row->getModel()->select()
            ->whereEquals($this->_field, $valueString)
            ->whereEquals('unsubscribed', 0); // if unsubscribed, can subscribe again
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
