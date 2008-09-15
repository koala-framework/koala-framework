<?php
class Vpc_Basic_Text_Generator extends Vps_Component_Generator_Table
{
    protected function _getIdFromRow($row)
    {
        return substr($row->component, 0, 1).$row->nr;
    }

    protected function _formatSelectId(Vps_Component_Select $select)
    {
        if ($select->hasPart(Vps_Model_Select::WHERE_ID)) {
            $id = $select->getPart(Vps_Model_Select::WHERE_ID);
            $select->unsetPart(Vps_Model_Select::WHERE_ID);
            if (substr($id, 0, 2)=='-l') {
                $select->where("component = 'link'");
            } else if (substr($id, 0, 2)=='-d') {
                $select->where("component = 'download'");
            } else if (substr($id, 0, 2)=='-i') {
                $select->where("component = 'image'");
            } else {
                return null;
            }
            $select->where("nr = ?", substr($id, 2));
        }
        return $select;
    }
}
