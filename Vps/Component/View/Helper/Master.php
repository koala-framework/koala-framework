<?php
class Vps_Component_View_Helper_Master extends Vps_Component_View_Renderer
{
    public function master(Vps_Component_Data $component)
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
            if (Vpc_Abstract::getTemplateFile($c->componentClass, 'Master')) {
                $componentWithMaster[] = array(
                    'type' => 'master',
                    'data' => $c
                );
            }
            $c = $c->parent;
        }
        $helper = new Vps_Component_View_Helper_ComponentWithMaster();
        $helper->setRenderer($this->_getRenderer());
        return $helper->componentWithMaster($componentWithMaster);
    }
}
