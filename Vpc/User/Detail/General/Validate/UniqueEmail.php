<?php
class Vpc_User_Detail_General_Validate_UniqueEmail extends Vps_Validate_Row_Abstract
{
    const NOT_UNIQUE_USER = 'notUniqueUser';

    public function __construct()
    {
        $this->_messageTemplates[self::NOT_UNIQUE_USER] = trlVps("User '%value%' does already exist");
    }

    public function isValidRow($value, $row)
    {
        $valueString = (string)$value;
        $this->_setValue($valueString);

        $select = $row->getModel()->select()
            ->whereEquals($this->_field, $valueString)
            ->whereEquals('deleted', '0');
        $primaryKey = $row->getModel()->getPrimaryKey();
        if ($row->$primaryKey) {
            $select->whereNotEquals($primaryKey, $row->$primaryKey);
        }

        $row->getModel()->lockCreateUser();
        $cnt = $row->getModel()->countRows($select);
        $row->getModel()->unlockCreateUser();

        if ($cnt) {
            $this->_error(self::NOT_UNIQUE_USER);
            return false;
        }

        return true;
    }
}
