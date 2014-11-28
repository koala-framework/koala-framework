<?php
class Kwc_List_Switch_FirstLargeContentPlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeCache
{
    public function processOutput($output, $renderer)
    {
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible'=>true))
            ->getComponent();
        $itemPage = $component->getDefaultItemPage();

        if ($itemPage) {
            $helper = new Kwf_Component_View_Helper_Component();
            $helper->setRenderer($renderer);
            $html = $helper->component($component->getLargeComponent($itemPage));
            $output = str_replace(
                '<div class="listSwitchLargeContent"></div>',
                '<div class="listSwitchLargeContent">%largeContentBegin%'.$html.'%largeContentEnd%</div>',
                $output
            );
            $preview = $component->getPreviewComponent($itemPage);
            $output = str_replace(
                '<div id="'.$preview->componentId.'" class="listSwitchItem',
                '<div id="'.$preview->componentId.'" class="listSwitchItem defaultActive',
                $output
            );
        }

        return $output;
    }
}
