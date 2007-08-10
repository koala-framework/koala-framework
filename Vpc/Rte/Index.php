<?php
class Vpc_Rte_Index extends Vpc_Abstract
{
   protected $_settings = array(
        'text' => '',
        'fieldLabel' => 'Rich Text Editor',
		'width' => 500,
		'height' => 200,
		'enableAlignments' => 1,
		'enableColors' => 1,
		'enableFont' => 1,
		'enableFontSize' => 1,
		'enableFormat' => 1,
		'enableLinks' => 1,
		'enableLists' => 1,
		'enableSourceEdit' => 1);

    protected $_tablename = 'Vpc_Rte_IndexModel';
    public $controllerClass = 'Vpc_Rte_IndexController';
   	const NAME = 'Standard.Rte';

    function getTemplateVars()
    {
        $return['text'] = $this->getSetting('text');
        $return['id'] = $this->getDbId().$this->getComponentKey();
        $return['template'] = 'Rte.html';
        return $return;
    }


}