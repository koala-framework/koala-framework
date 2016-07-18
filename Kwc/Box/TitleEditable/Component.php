<?php
class Kwc_Box_TitleEditable_Component extends Kwc_Box_Title_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Title');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    protected function _getTitle()
    {
        if (trim($this->_getRow()->title)) return $this->_getRow()->title;

        //if no title is configured get from next subroot/root
        $title = $this->_getSubrootTitle();
        if ($title) {
            $ret = $this->getData()->getTitle(); //append own title
            if ($ret) $ret .= ' - ';
            return $ret.$title;
        }

        return parent::_getTitle();
    }

    protected function _getSubrootTitle()
    {
        $c = $this->getData()->parent;
        while($c) {
            if (($c->inherits && Kwc_Abstract::getFlag($c->componentClass, 'subroot')) || $c->componentId == 'root') {
                $title = $c->getChildComponent(array('id'=>'-'.$this->getData()->id, 'componentClass'=>$this->getData()->componentClass));
                if ($title) {
                    $title = $title->getComponent()->_getRow()->title;
                    if ($title) return $title;
                }
            }
            $c = $c->parent;
        }
        return null;
    }
}
