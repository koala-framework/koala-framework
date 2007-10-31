<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Html_Component extends Vpc_Abstract
{
    protected $_settings = array(
        'width' => 400,
        'height' => 400,
        'content' => 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.'
    );
    protected $_tablename = 'Vpc_Basic_Html_Model';
    const NAME = 'Standard.Html';

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->getSetting('content');
        $ret['template'] = 'Basic/Html.html';
        return $ret;
    }
}
