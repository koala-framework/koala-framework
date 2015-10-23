<?php
class Kwc_Directories_Menu_Component extends Kwc_Menu_Abstract_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['categories'] = array();
        $itemDirectory = $this->_getItemDirectory();

        if ($itemDirectory) {
            $classes = Kwc_Abstract::getChildComponentClasses($itemDirectory->componentClass);
            foreach ($classes as $c) {
                if (Kwc_Abstract::hasSetting($c, 'categoryName')) {
                    $name = Kwc_Abstract::getSetting($c, 'categoryName');
                    $parent = $itemDirectory->getChildComponent(array('componentClass'=>$c));
                    if ($parent) {
                        $ret['categories'][$name] = $this->_getMenuData($parent);
                    }
                }
            }
        }
        return $ret;
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        return false;
    }

    protected function _getItemDirectory()
    {
        $data = $this->getData();
        while ($data = $data->parent) {
            if (is_instance_of($data->componentClass, 'Kwc_Directories_List_Component')) {
                return $data->getComponent()->getItemDirectory();
            }
        }
        return null;
    }

}
