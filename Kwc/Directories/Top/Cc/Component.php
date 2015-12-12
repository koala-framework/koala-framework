<?php
class Kwc_Directories_Top_Cc_Component extends Kwc_Directories_List_Cc_Component
{
    public function getSelect()
    {
        $select = parent::getSelect();
        if (!$select) return null;
        $limit = Kwc_Abstract::getSetting($this->getData()->chained->componentClass, 'limit');
        if ($limit) $select->limit($limit);
        return $select;
    }
}
