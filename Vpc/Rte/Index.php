<?php
class Vpc_Rte_Index extends Vpc_Abstract
{
  protected $_defaultSettings = array('text' => '');

    function getTemplateVars()
    {
        $return['text'] = $this->getSetting('text');
        $return['id'] = $this->getComponentId();
        $return['template'] = 'Rte.html';
        return $return;
    }
}