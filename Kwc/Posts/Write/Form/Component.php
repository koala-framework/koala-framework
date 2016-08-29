<?php
class Kwc_Posts_Write_Form_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['success'] = 'Kwc_Posts_Write_Form_Success_Component';
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel($this->getData()->parent->getComponent()->getPostsModel());
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        if ($row->getModel()->hasColumn('component_id')) {
            $row->component_id = $this->getData()->parent->getComponent()->getPostsDirectory()->dbId;
        }
    }
}
