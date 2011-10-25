<?php
class Kwf_View_Helper_DateTime extends Kwf_View_Helper_Date
{
    public function dateTime($date, $format = null)
    {
        if (!$format) {
            if ($this->_view && $this->_view->item && $this->_view->item instanceof Kwf_Component_Data) {
                $format = $this->_view->item->trlKwf('Y-m-d H:i');
            } else if ($this->_view && $this->_view->data && $this->_view->data instanceof Kwf_Component_Data) {
                $format = $this->_view->data->trlKwf('Y-m-d H:i');
            } else {
                $format = trlKwf('Y-m-d H:i');
            }
        }
        return $this->date($date, $format);
    }
}
