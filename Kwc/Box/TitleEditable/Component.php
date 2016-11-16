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

    protected function _getParentTitleComponents()
    {
        $ret = array();
        $data = $this->getData();
        $ids = array();
        while ($data && !$data->inherits) {
            $ids[] = strrchr($data->componentId, '-');
            $data = $data->parent;
        }
        while ($data) {
            if (($data->inherits && Kwc_Abstract::getFlag($data->componentClass, 'subroot')) || $data->componentId == 'root') {
                $d = $data;
                foreach (array_reverse($ids) as $id) {
                    $d = $d->getChildComponent($id);
                }
                if ($d && $this->getData()->componentClass == $d->componentClass) {
                    $ret[] = $d;
                }
            }
            $data = $data->parent;
        }
        return $ret;
    }

    protected function _getTitle()
    {
        if (trim($this->_getRow()->title)) return $this->_getRow()->title;

        foreach ($this->_getParentTitleComponents() as $component) {
            $title = $component->getComponent()->_getRow()->title;
            if ($title) {
                $ret = $this->getData()->getTitle(); //append own title
                if ($ret) $ret .= ' - ';
                return $ret.$title;
            }
        }
        return parent::_getTitle();
    }
}
