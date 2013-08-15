<?php
class Kwc_Columns_Component extends Kwc_Abstract_List_Component
{
    public static $needsParentComponentClass = true;
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = $parentComponentClass;
        $ret['componentName'] = trlKwfStatic('Columns');
        $ret['componentIcon'] = new Kwf_Asset('application_tile_horizontal');

        $ret['extConfig'] = 'Kwc_Columns_ExtConfig';

        $ret['contentMargin'] = 20;

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach($ret['listItems'] as $k => $v) {
            $ret['listItems'][$k]['width'] = $this->_getChildContentWidth($v['data']).'px';
        }
        return $ret;
    }

    private function _getWidth($child, $ownWidth)
    {
        $w = trim($child->row->width);
        if (substr($w, -2)=='px') {
            $w = substr($w, 0, -2); //px abschneiden
        }
        if (is_numeric($w) && $w > 0) {
            //px (standard, wenn keine einheit)
            return (int)$w;
        }
        if (substr($w, -1)=='%' && substr($w, 0, -1) > 0) {
            $w = substr($w, 0, -1);
            $columns = $this->getData()->countChildComponents($this->_getSelect());
            $ownWidth -= $this->_getSetting('contentMargin')*($columns-1);
            return (int)round($ownWidth * $w/100);
        }
        return null; //unbekanntes format
    }

    //unterstützt werden:
    // - breitenangaben in px oder ohne einheit (ist eh klar)
    // - breitenangaben in % (wird in px umgerechnet, margin wird berücksichtigt)
    // - keine breitenangabe (restliche breite wird aufgezeilt)
    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        $ownWidth = parent::_getChildContentWidth($child);

        $w = $this->_getWidth($child, $ownWidth);
        if (is_int($w)) {
            return $w; //fix with
        }

        $sumUsedWith = 0;
        $columns = 0;
        $noWidthColumns = 0;
        foreach ($this->getData()->getChildComponents($this->_getSelect()) as $c) {
            $columns++;
            $w = $this->_getWidth($c, $ownWidth);
            if ($w) {
                $sumUsedWith += $w;
            } else {
                $noWidthColumns++;
            }
        }
        $ownWidth -= $this->_getSetting('contentMargin')*($columns-1) + $sumUsedWith;
        if ($noWidthColumns == 0) $noWidthColumns = 1;
        return round($ownWidth / $noWidthColumns);
    }

    //TODO: cache meta für breite geändert
}
