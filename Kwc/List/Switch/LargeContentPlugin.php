<?php
class Kwc_List_Switch_LargeContentPlugin extends Kwf_Component_Plugin_View_Abstract
{
    private $_currentItem;
    public function getExecutionPoint()
    {
        return Kwf_Component_Plugin_Interface_View::EXECUTE_BEFORE;
    }

    public function processOutput($output)
    {
        if (!$this->_currentItem) {
            $this->_currentItem = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($this->_componentId)
                ->getChildComponent(array('generator'=>'child', 'limit'=>1))
                ->getChildComponent('-large');
        }
        $helper = new Kwf_Component_View_Helper_Component();
        $html = $helper->component($this->_currentItem);
        $output = str_replace(
            '<div class="listSwitchLargeContent"></div>',
            '<div class="listSwitchLargeContent">'.$html.'</div>',
            $output
        );

        //add active
        $output = str_replace(
            '<div id="'.$this->_currentItem->parent->componentId.'" class="listSwitchItem',
            '<div id="'.$this->_currentItem->parent->componentId.'" class="listSwitchItem defaultActive',
            $output
        );

        return $output;
    }

    public function setCurrentItem($item)
    {
        $this->_currentItem = $item;
    }
}
