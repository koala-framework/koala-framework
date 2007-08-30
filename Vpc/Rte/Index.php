<?php
class Vpc_Rte_Index extends Vpc_Abstract
{
   protected $_settings = array(
        'text' => 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.',
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
        'enableSourceEdit' => 1
    );

    protected $_tablename = 'Vpc_Rte_IndexModel';
    const NAME = 'Standard.Rte';

    function getTemplateVars()
    {
        $return['text'] = $this->getSetting('text');
        $return['template'] = 'Rte.html';
        return $return;
    }


}