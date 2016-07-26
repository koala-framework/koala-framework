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

    public static function getTitleFromNextSubroot($data) {
        $c = $data->parent;
        while($c) {
            if (($c->inherits && Kwc_Abstract::getFlag($c->componentClass, 'subroot')) || $c->componentId == 'root') {
                $title = $c->getChildComponent(array('id'=>'-'.$data->id, 'componentClass'=>$data->componentClass));
                if ($title) {
                    $title = $title->getComponent()->_getRow()->title;
                }
                if ($title) {
                    $ret = $data->getTitle(); //append own title
                    if ($ret) $ret .= ' - ';
                    return $ret.$title;
                }
            }
            $c = $c->parent;
        }
        return false;
    }

    protected function _getTitle()
    {
        if (trim($this->_getRow()->title)) return $this->_getRow()->title;

        if ($ret = self::getTitleFromNextSubroot($this->getData())) return $ret;
        return parent::_getTitle();
    }
}
