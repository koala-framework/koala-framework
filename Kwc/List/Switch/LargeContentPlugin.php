<?php
class Kwc_List_Switch_LargeContentPlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeChildRender
{
    protected $_currentItem;

    public function processOutput($output, $renderer)
    {
        if (is_null($this->_currentItem)) {
            $output = str_replace(array('%largeContentBegin%', '%largeContentEnd%'), '', $output);
        } else {
            $helper = new Kwf_Component_View_Helper_Component();
            $helper->setRenderer($renderer);
            $html = $helper->component($this->_currentItem);
            $output = preg_replace(
                '/%largeContentBegin%.*?%largeContentEnd%/s',
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
