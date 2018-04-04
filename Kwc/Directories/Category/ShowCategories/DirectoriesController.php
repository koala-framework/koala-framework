<?php
class Kwc_Directories_Category_ShowCategories_DirectoriesController extends Kwf_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('id'));
        $this->_columns->add(new Kwf_Grid_Column('name'));

        $data = array();
        foreach ($this->_getCategoryComponents() as $category) {
            $categoriesModel = $category->getComponent()->getChildModel();
            $select = $categoriesModel->select()
                ->whereEquals('component_id', $category->componentId)
                ->order('pos');
            foreach ($categoriesModel->getRows($select) as $categoryRow) {
                $data[] = array(
                    'id' => $categoryRow->id,
                    'name' => $this->_getCategoryTitle($category->parent, $categoryRow),
                );
            }
        }
        $this->_model = new Kwf_Model_FnF(array(
            'data' => $data
        ));
        parent::_initColumns();
    }

    protected function _getCategoryComponents()
    {
        $ret = array();

        $subroot = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true));
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Directories_Category_Directory_Component', array('subroot'=>$subroot, 'ignoreVisible'=>true));
        foreach ($components as $component) {
            $itemDirectory = $component->parent;

            $showDirectoryClass = Kwc_Abstract::getSetting($this->_getParam('class'), 'showDirectoryClass');
            if (!is_instance_of($itemDirectory->componentClass, $showDirectoryClass)) continue;

            $hideDirectoryClasses = Kwc_Abstract::getSetting($this->_getParam('class'), 'hideDirectoryClasses');
            foreach ($hideDirectoryClasses as $c) {
                if (is_instance_of($itemDirectory->componentClass, $c)) {
                    continue 2;
                }
            }

            $ret[] = $component;
        }
        return $ret;

    }

    protected function _getCategoryTitle(Kwf_Component_Data $itemDirectory, Kwf_Model_Row_Abstract $categoryRow)
    {
        return $itemDirectory->getTitle() . ' -> ' . $categoryRow->name;
    }
}
