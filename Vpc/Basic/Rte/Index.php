<?php
class Vpc_Basic_Rte_Index extends Vpc_Abstract
{
   protected $_settings = array(
        'text' => 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.',
        'fieldLabel' => 'Rich Text Editor',
        'width' => 500,
        'height' => 200,
        'enableAlignments' => true,
        'enableColors' => true,
        'enableFont' => true,
        'enableFontSize' => true,
        'enableFormat' => true,
        'enableLinks' => true,
        'enableLists' => true,
        'enableSourceEdit' => true
    );

    protected $_tablename = 'Vpc_Basic_Rte_IndexModel';
    const NAME = 'Standard.Rte';

    function getTemplateVars()
    {
        $return['text'] = $this->getSetting('text');
        $return['template'] = 'Rte.html';
        return $return;
    }


}