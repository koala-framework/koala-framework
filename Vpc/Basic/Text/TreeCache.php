<?php
class Vpc_Basic_Text_TreeCache extends Vpc_TreeCache_Table
{
    protected $_tableName = 'Vpc_Basic_Text_ChildComponentsModel';


    protected function _getIdFromRow($row)
    {
        return substr($row->component, 0, 1).$row->nr;
    }

    protected function _getSelect($parentData, $constraints)
    {
        if (isset($constraints['id'])) {
            $id = $constraints['id'];
            unset($constraints['id']);
        }
        $select = parent::_getSelect($parentData, $constraints);
        if (!$select) return null;

        if (isset($id)) {
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
