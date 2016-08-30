<?php
class Kwc_Directories_Category_Directory_CategoriesController
    extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';

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

    protected  function _getCategoryDirectory()
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true));
        return $c->getChildComponent(array('componentClass'=>$this->_getParam('class'), 'ignoreVisible'=>true));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_model->hasColumn('component_id')) {
            $ret->whereEquals('component_id', $this->_getCategoryDirectory()->dbId);
        }
        return $ret;
    }

    protected function _initColumns()
    {
        $this->setModel(Kwc_Abstract::createChildModel($this->_getParam('class')));
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Category'), 300))
            ->setEditor(new Kwf_Form_Field_TextField());
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        if ($this->_model->hasColumn('component_id')) {
            $row->component_id = $this->_getCategoryDirectory()->dbId;
        }
    }
}
