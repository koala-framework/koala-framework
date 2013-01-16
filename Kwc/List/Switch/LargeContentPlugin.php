<?php
class Kwc_List_Switch_LargeContentPlugin extends Kwf_Component_Plugin_View_Abstract
{
    protected $_currentItem;

    public static function getExecutionPoint()
    {
        return Kwf_Component_Plugin_Interface_View::EXECUTE_BEFORE;
    }

    public function processOutput($output)
    {
        if (is_null($this->_currentItem)) {
            $output = str_replace(array('%largeContentBegin%', '%largeContentEnd%'), '', $output);
        } else {
            $helper = new Kwf_Component_View_Helper_Component();
            $html = $helper->component($this->_currentItem);
            $output = preg_replace(
                '#%largeContentBegin%.*?%largeContentEnd%#',
                $html,
                $output
            );
            $output = str_replace('class="listSwitchItem defaultActive', 'class="listSwitchItem', $output);
            $output = str_replace(
                '<div id="'.$this->_currentItem->parent->componentId.'" class="listSwitchItem',
                '<div id="'.$this->_currentItem->parent->componentId.'" class="listSwitchItem defaultActive',
                $output
            );
        }

        return $output;
    }

    public function setCurrentItem($item)
    {
        $this->_currentItem = $item;
    }
}
