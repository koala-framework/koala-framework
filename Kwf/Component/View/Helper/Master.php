<?php
class Kwf_Component_View_Helper_Master extends Kwf_Component_View_Renderer
{
    public function master(Kwf_Component_Data $component)
    {
        $viewCacheSettings = $component->getComponent()->getViewCacheSettings();
        return $this->_getRenderPlaceholder($component->componentId, array(), null, $viewCacheSettings['enabled']);
    }

    public function render($componentId, $config)
    {
        $component = $this->_getComponentById($componentId);
        $componentWithMaster = $this->getComponentsWithMasterTemplate($component);
        $helper = new Kwf_Component_View_Helper_ComponentWithMaster();
        $helper->setRenderer($this->_getRenderer());
        return $helper->componentWithMaster($componentWithMaster);
    }

    public static function getComponentsWithMasterTemplate($component)
    {
        $ret = array();
        $ret[] = array(
            'type' => 'component',
            'data' => $component
        );
        while ($component) {
            if ($component->getComponent()->hasMasterTemplate()) {
                $ret[] = array(
                    'type' => 'master',
                    'data' => $component
                );
            }
            if (Kwc_Abstract::getFlag($component->componentClass, 'resetMaster')) {
                break;
            }
            $component = $component->parent;
        }
        return $ret;
    }

    public function getViewCacheSettings($componentId)
    {
        $component = $this->_getComponentById($componentId);
        while ($component && !$component->getComponent()->hasMasterTemplate()) {
            $component = $component->parent;
        }
        return $component->getComponent()->getMasterViewCacheSettings();
    }

}
