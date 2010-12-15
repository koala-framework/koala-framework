<?php
class Vpc_Directories_Year_Directory_Generator extends Vpc_Directories_Month_Directory_Generator
{
    protected function _formatSelectFilename(Vps_Component_Select $select)
    {
        if ($select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
            $filename = $select->getPart(Vps_Component_Select::WHERE_FILENAME);
            if (!preg_match('#^([0-9]{4})$#', $filename, $m)) return null;
            $dateColumn = Vpc_Abstract::getSetting($this->_class, 'dateColumn');
            $select->where("YEAR($dateColumn) = ?", $m[1]);
        }
        return $select;
    }

    protected function _formatSelectId(Vps_Component_Select $select)
    {
        if ($select->hasPart(Vps_Model_Select::WHERE_ID)) {
            $id = $select->getPart(Vps_Model_Select::WHERE_ID);
            if (!preg_match('#^_([0-9]{4})$#', $id, $m)) return null;
            $dateColumn = Vpc_Abstract::getSetting($this->_class, 'dateColumn');
            $select->where("YEAR($dateColumn) = ?", $m[1]);
            $select->unsetPart(Vps_Model_Select::WHERE_ID);
        }
        return $select;
    }

    protected function _getSelectGroup($dateColumn)
    {
        return array('YEAR('.$dateColumn.')');
    }

    protected function _getNameFromRow($row)
    {
        return substr(parent::_getNameFromRow($row), -4);
    }

    protected function _getFilenameFromRow($row)
    {
        return substr(parent::_getFilenameFromRow($row), 0, 4);
    }

    protected function _getIdFromRow($row)
    {
        return substr(parent::_getIdFromRow($row), 0, 4);
    }
}
