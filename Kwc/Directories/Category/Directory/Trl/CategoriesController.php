<?php
class Kwc_Directories_Category_Directory_Trl_CategoriesController
    extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save', 'reload');
    protected $_modelName = 'Kwc_Directories_Category_Directory_Trl_AdminModel';

    protected function _isAllowedComponent()
    {
        if ($this->_getParam('componentId')) {
            $class = $this->_getParam('class');
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($this->_getParam('componentId'),
                    array('ignoreVisible'=>true, 'limit'=>1));
            $c = $c->getChildComponent(array('componentClass'=>$class));
            $allowed = Kwf_Registry::get('acl')
                ->isAllowedComponentById($c->componentId, $class, $this->_getAuthData());
        } else {
            $allowed = false;
            $class = $this->_getParam('class');
            foreach (Kwf_Registry::get('acl')->getAllResources() as $r) {
                if ($r instanceof Kwf_Acl_Resource_ComponentClass_Interface) {
                    if ($class == $r->getComponentClass()) {
                        $allowed = Kwf_Registry::get('acl')->getComponentAcl()
                            ->isAllowed($this->_getAuthData(), $this->_getParam('class'));
                        break;
                    }
                }
            }
        }
        return $allowed;
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_getModel()->setComponentId($this->_getCategoryDirectory()->dbId);
    }

    private function _getCategoryDirectory()
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true));
        return $c->getChildComponent(array('componentClass'=>$this->_getParam('class'), 'ignoreVisible'=>true));
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Category'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        $this->_columns->add(new Kwf_Grid_Column('original_name', trlKwf('Original Category'), 150));
    }
}
