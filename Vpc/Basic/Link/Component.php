<?php
class Vpc_Basic_Link_Component extends Vpc_Abstract
{
    public $linkTag;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_Link_Model',
            'componentName' => 'Link',
            'childComponentClasses'   => array(
                'linkTag' => 'Vpc_Basic_LinkTag_Component',
            )
        ));
    }
    public function _init()
    {
        $class = $this->_getClassFromSetting('linkTag', 'Vpc_Basic_LinkTag_Component');
        $this->linkTag = $this->createComponent($class, 'tag');
    }

    public function getChildComponents()
    {
        return array($this->linkTag);
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['linkTag'] = $this->linkTag->getTemplateVars();
        $return['text'] = $this->_getRow()->text;
        return $return;
    }

}
