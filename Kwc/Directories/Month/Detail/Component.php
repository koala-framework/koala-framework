<?php
class Kwc_Directories_Month_Detail_Component extends Kwc_Directories_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['useDirectorySelect'] = false;
        $ret['flags']['hasComponentLinkModifiers'] = true;
        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $dateColumn = Kwc_Abstract::getSetting($this->getData()->parent->componentClass, 'dateColumn');
        $select = $this->_getDateSelect($select, $dateColumn);
        return $select;
    }

    protected function _getDateSelect($select, $dateColumn)
    {
        $monthDate = substr($this->getData()->row->$dateColumn, 0, 7);
        $select->where(new Kwf_Model_Select_Expr_HigherEqual($dateColumn, new Kwf_Date("$monthDate-01")));
        $select->where(new Kwf_Model_Select_Expr_LowerEqual($dateColumn, new Kwf_Date("$monthDate-31")));
        $select->order($dateColumn, 'DESC');
        return $select;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        return self::_getParentItemDirectoryClasses($directoryClass, 1);
    }

    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent;
    }

    public function getComponentLinkModifiers()
    {
        $cnt = $this->getItemDirectory()->countChildComponents($this->getSelect());
        return array(
            array(
                'type' => 'appendText',
                'text' => ' ('.$cnt.')'
            )
        );
    }
}
