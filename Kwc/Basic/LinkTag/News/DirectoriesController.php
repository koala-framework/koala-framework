<?php
class Kwc_Basic_LinkTag_News_DirectoriesController extends Kwf_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('id'));
        $this->_columns->add(new Kwf_Grid_Column('name'));

        if ($this->_getParam('id')) {
            $subRootComponentId = $this->_getParam('id');
        } else if ($this->_getParam('parent_id')) {
            $subRootComponentId = $this->_getParam('parent_id');
        } else if ($this->_getParam('componentId')) {
            $subRootComponentId = $this->_getParam('componentId');
        } else {
            throw new Kwf_Exception("componentId, id or parent_id required");
        }
        $subroot = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($subRootComponentId, array('ignoreVisible' => true));

        $data = array();
        $classes = Kwc_Admin::getInstance($this->_getParam('class'))->getDirectoryComponentClasses();
        $news = Kwf_Component_Data_Root::getInstance()
            ->getComponentsBySameClass($classes, array('subroot'=>$subroot, 'ignoreVisible'=>true));
        foreach ($news as $new) {
            $data[] = array(
                'id' => $new->dbId,
                'name' => $new->getTitle(),
            );
        }
        $this->_model = new Kwf_Model_FnF(array(
            'data' => $data
        ));
        parent::_initColumns();
    }

    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }
}
