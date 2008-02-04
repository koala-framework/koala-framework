<?php
class Vpc_Basic_Link_Component extends Vpc_Abstract
{
    protected $_linkTag;

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

    public function getChildComponent()
    {
        if (!$this->_linkTag) {
            $class = $this->_getClassFromSetting('linkTag', 'Vpc_Basic_LinkTag_Component');
            $this->_linkTag = $this->createComponent($class, 'tag');
        }
        return $this->_linkTag;
    }

    public function getChildComponents()
    {
        return array($this->getChildComponent());
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['linkTag'] = $this->getChildComponent()->getTemplateVars();
        $return['text'] = $this->_getRow()->text;
        return $return;
    }

}
