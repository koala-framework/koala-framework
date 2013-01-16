<?php
class Kwc_List_Switch_FirstLargeContentPlugin extends Kwf_Component_Plugin_View_Abstract
{
    public static function getExecutionPoint()
    {
        return Kwf_Component_Plugin_Interface_View::EXECUTE_BEFORE_CACHE;
    }

    public function processOutput($output)
    {
        $child = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible'=>true))
            ->getChildComponent(array('generator'=>'child', 'limit'=>1));
        if ($child) {
            $firstItem = $child->getChildComponent('-large');
            if ($firstItem) {
                $helper = new Kwf_Component_View_Helper_Component();
                $html = $helper->component($firstItem);
                $output = str_replace(
                    '<div class="listSwitchLargeContent"></div>',
                    '<div class="listSwitchLargeContent">%largeContentBegin%'.$html.'%largeContentEnd%</div>',
                    $output
                );
                $output = str_replace(
                    '<div id="'.$firstItem->parent->componentId.'" class="listSwitchItem',
                    '<div id="'.$firstItem->parent->componentId.'" class="listSwitchItem defaultActive',
                    $output
                );
            }
        }

        return $output;
    }
}
