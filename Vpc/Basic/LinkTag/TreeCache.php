<?php
class Vpc_Basic_LinkTag_TreeCache extends Vpc_TreeCache_Table
{
    protected function _formatConstraints($parentData, $constraints)
    {
        //es gibt exakt eine unterkomponente mit der id 'link'
        if (isset($constraints['id'])) {
            if ($constraints['id'] != '-link') {
                return null;
            }
            unset($constraints['id']); //contraint nicht weiterreichen
        }
        $select = parent::_formatConstraints($parentData, $constraints);
        if (!$select) return null;
        return $select;
    }

    protected function _getIdFromRow($row)
    {
        return 'link';
    }
}
