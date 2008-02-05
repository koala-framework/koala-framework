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
            'componentIcon' => new Vps_Asset('page_white_link'),
            'childComponentClasses'   => array(
                'Internal Link' => 'Vpc_Basic_LinkTag_Intern_Component',
                'External Link' => 'Vpc_Basic_LinkTag_Extern_Component',
                'Mail Link'     => 'Vpc_Basic_LinkTag_Mail_Component'
            ),
            'default'       => array(
                'link_class'    => 'Vpc_Basic_LinkTag_Intern_Component'
            )
        ));
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/LinkTag/Panel.js';
        return $ret;
    }

    public function getChildComponent()
    {
        if (!$this->_link) {
            $class = $this->_getRow()->link_class;
            if (class_exists($class) &&
                is_subclass_of($class, 'Vpc_Basic_LinkTag_Abstract_Component')
            ) {
                $this->_link = $this->createComponent($class, 1);
            } else {
                throw new Vpc_Exception('Link class does not exist or does not have
                Vpc_Basic_LinkTag_Abstract_Component as parent class: ' . $class);
            }
        }
        return $this->_link;
    }

    public function getChildComponents()
    {
        return array($this->getChildComponent());
    }

    public function getTemplateVars()
    {
        return $this->getChildComponent()->getTemplateVars();
    }
}
