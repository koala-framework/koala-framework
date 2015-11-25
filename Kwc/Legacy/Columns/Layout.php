<?php
class Kwc_Legacy_Columns_Layout extends Kwf_Component_Layout_Default
{
    private function _getWidth(Kwf_Component_Data $data, Kwf_Component_Data $child, $ownWidth)
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
            $columns = $data->countChildComponents($data->getComponent()->getSelect());
            $ownWidth -= $this->_getSetting('contentMargin')*($columns-1);
            return (int)round($ownWidth * $w/100);
        }
        return null; //unbekanntes format
    }

    //unterstützt werden:
    // - breitenangaben in px oder ohne einheit (ist eh klar)
    // - breitenangaben in % (wird in px umgerechnet, margin wird berücksichtigt)
    // - keine breitenangabe (restliche breite wird aufgezeilt)
    public function getChildContentWidth(Kwf_Component_Data $data, Kwf_Component_Data $child)
    {
        $ownWidth = parent::getChildContentWidth($data, $child);

        $w = $this->_getWidth($data, $child, $ownWidth);
        if (is_int($w)) {
            return $w; //fix with
        }

        $sumUsedWith = 0;
        $columns = 0;
        $noWidthColumns = 0;
        foreach ($data->getChildComponents($data->getComponent()->getSelect()) as $c) {
            $columns++;
            $w = $this->_getWidth($data, $c, $ownWidth);
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
}
