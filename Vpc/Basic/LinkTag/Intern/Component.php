<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Intern_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'     => 'Vpc_Basic_LinkTag_Intern_Model',
            'showRel'       => false,
            'showParameters' => false,
            'componentName' => 'Link.Intern'
        ));
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/LinkTag/Intern/LinkField.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $target = $this->_getRow()->target;
        $page = $this->getPageCollection()->getPageById($target);
        if ($page) {
            $href = $this->getPageCollection()->getUrl($page);
        } else {
            $href = $target;
        }

        $ret = parent::getTemplateVars();
        $ret['href'] = $href;
        return $ret;
    }
}
