<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Component extends Vpc_Abstract
{
    protected $_link;

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'     => 'Vpc_Basic_LinkTag_Model',
            'componentName' => 'LinkTag',
            'componentIcon' => new Vps_Asset('page_link'),
            'childComponentClasses'   => array(
                'intern' => 'Vpc_Basic_LinkTag_Intern_Component',
                'extern' => 'Vpc_Basic_LinkTag_Extern_Component',
                'mail'     => 'Vpc_Basic_LinkTag_Mail_Component'
            ),
            'default'       => array(
                'component'    => 'intern'
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsFormCards';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['linkTag'] = $this->getData()->getChildComponent();
        return $ret;
    }
}
