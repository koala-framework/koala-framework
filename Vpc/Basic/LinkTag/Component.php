<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'dataClass'     => 'Vpc_Basic_LinkTag_Data',
            'tablename'     => 'Vpc_Basic_LinkTag_Model',
            'componentName' => 'LinkTag',
            'componentIcon' => new Vps_Asset('page_link'),
            'default'       => array(
                'component'    => 'intern'
            )
        ));
        $ret['generators']['link'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => array(
                'intern' => 'Vpc_Basic_LinkTag_Intern_Component',
                'extern' => 'Vpc_Basic_LinkTag_Extern_Component',
                'mail'     => 'Vpc_Basic_LinkTag_Mail_Component'
            )
        );
        $ret['assetsAdmin']['dep'][] = 'VpsFormCards';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $childs = $this->getData()->getChildComponents(array(
            'generator' => 'link'
        ));
        $ret['linkTag'] = $childs[0];
        return $ret;
    }
}
