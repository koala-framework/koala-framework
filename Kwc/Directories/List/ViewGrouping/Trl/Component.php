<?php
class Kwc_Directories_List_ViewGrouping_Trl_Component extends Kwc_Directories_List_Trl_Component
{
    public function getSelect()
    {
        $ret = parent::getSelect();
        $select = $this->getData()->parent->chained->getGenerator('detail')->getFormattedSelect($this->getData()->parent->chained, array('ignoreVisible' => true));
        $model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->parent->componentClass, 'childModel'));
        $visible = ($model->hasColumn('visible')) ? 'AND visible = 1' : '';
        $select->where(new Kwf_Model_Select_Expr_Sql('id IN (
                    SELECT SUBSTRING(component_id, '. (strlen($this->getData()->parent->dbId . '_')+1) .')
                    FROM ' . $model->getTableName() . '
                    WHERE component_id LIKE '.Kwf_Registry::get('db')->quote(str_replace('_', '\_', $this->getData()->parent->dbId . '_') . '%').'
                    ' . $visible . '
                )'
        ));
        $dependentModel = Kwc_Abstract::getSetting($this->getData()->chained->componentClass, 'dependentModel');
        if (!$dependentModel) throw new Kwf_Exception('Set dependentModel for correct grouping');
        $ret->where(new Kwf_Model_Select_Expr_Child_Contains($dependentModel, $select));
        $ret->order('pos', 'ASC');
        return $ret;
    }
}

