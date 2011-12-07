<?php
class Kwf_Component_View_Helper_Master extends Kwf_Component_View_Renderer
{
    public function master(Kwf_Component_Data $component)
    {
        return $this->_getRenderPlaceholder($component->componentId, array(), null, 'master', array());
    }

    public function render($componentId, $config)
    {
        $component = $this->_getComponentById($componentId);

        $componentWithMaster = array();
        $componentWithMaster[] = array(
            'type' => 'component',
            'data' => $component
        );
        $c = $component;
        while ($c) {
            if (Kwc_Abstract::getTemplateFile($c->componentClass, 'Master')) {
                $componentWithMaster[] = array(
                    'type' => 'master',
                    'data' => $c
                );
            }
            if (Kwc_Abstract::getFlag($c->componentClass, 'resetMaster')) {
                $c = null;
            } else {
                $c = $c->parent;
            }
        }
        $helper = new Kwf_Component_View_Helper_ComponentWithMaster();
        $helper->setRenderer($this->_getRenderer());
        return $helper->componentWithMaster($componentWithMaster);
    }
}
