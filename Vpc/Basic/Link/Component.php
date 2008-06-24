<?php
class Vpc_Basic_Link_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_Link_Model',
            'componentName' => 'Link',
            'componentIcon' => new Vps_Asset('page_white_link'),
            'default' => array(),
            'childComponentClasses'   => array(
                'linkTag' => 'Vpc_Basic_LinkTag_Component',
            )
        ));
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['text'] = $this->_getRow()->text;
        return $ret;
    }

    public function getSearchVars()
    {
        $ret = parent::getSearchVars();
        $ret['text'] .= ' '.$this->_getRow()->text;
        return $ret;
    }
}
