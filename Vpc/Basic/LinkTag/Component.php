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
                'Internal Link' => 'Vpc_Basic_LinkTag_Intern_Component',
                'External Link' => 'Vpc_Basic_LinkTag_Extern_Component',
                'Mail Link'     => 'Vpc_Basic_LinkTag_Mail_Component'
            ),
            'default'       => array(
                'link_class'    => 'Vpc_Basic_LinkTag_Intern_Component'
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsFormCards';
        return $ret;
    }
    public function getTemplateVars()
    {
        return $this->getTreeCacheRow()->findChildComponents()->current()
                ->getComponent()->getTemplateVars();
    }
}
