<?php
class Vpc_Posts_Post_Edit_Form_Component extends Vpc_Posts_Write_Form_Component
{
    protected function _getPostsComponent()
    {
        return $this->getData()->parent->parent->parent;
    }
    
    protected function _initForm()
    {
        parent::_initForm();
        $table = Vpc_Abstract::createTable($this->getData()->parent->parent->parent->componentClass);
        $model = new Vps_Model_Db(array(
            'table' => $table
        ));
        $row = $model->find($this->getData()->parent->parent->row->id)->current();
        $this->_form->setPostRow($row);
    }
}
