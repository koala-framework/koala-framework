<?php
class Kwc_Abstract_List_Columns_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/Abstract/List/Columns/Component.js';
        $ret['extConfig'] = 'Kwc_Abstract_List_Columns_ExtConfig';
        $ret['contentMargin'] = 10;
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (Kwc_Abstract::hasSetting($componentClass, 'dimensions')) {
            throw new Kwf_Exception("Setting 'dimensions' must NOT exist");
        }
    }

    protected function _getColumns()
    {
        return $this->_getRow()->columns;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['cssClass'] .= ' col'.$this->_getColumns();
        $ret['imagesPerLine'] = $this->_getColumns();
        if (!$ret['imagesPerLine']) $ret['imagesPerLine'] = 1;
        $ret['downloadAll'] = $this->getData()->getChildComponent('-downloadAll');
        return $ret;
    }

    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        $ownWidth = parent::_getChildContentWidth($child);
        $contentMargin = $this->_getSetting('contentMargin');
        $columns = (int)$this->_getColumns();
        $ownWidth -= ($columns-1) * $contentMargin;

        if (!$columns) $columns = 1;
        if ($columns >=4 && $columns <= 10) {
            if ($columns == 4) {
                $columns = '2';
            }
            if ($columns == 6) {
                $columns = '3';
            }
            if ($columns % 2 == 0) {
                $columns = '4';
            } else {
                $columns = '3';
            }
            $ret = (int)floor((590 - ($columns-1) * $contentMargin) / $columns);
        } else {
            $ret = (int)floor($ownWidth / $columns);
        }
        return $ret;
    }
}
