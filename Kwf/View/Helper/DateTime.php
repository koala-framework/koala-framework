<?php
class Vps_View_Helper_DateTime extends Vps_View_Helper_Date
{
    public function dateTime($date, $format = null)
    {
        if (!$format) {
            if ($this->_view && $this->_view->item && $this->_view->item instanceof Vps_Component_Data) {
                $format = $this->_view->item->trlVps('Y-m-d H:i');
            } else if ($this->_view && $this->_view->data && $this->_view->data instanceof Vps_Component_Data) {
                $format = $this->_view->data->trlVps('Y-m-d H:i');
            } else {
                $format = trlVps('Y-m-d H:i');
            }
        }
        return $this->date($date, $format);
    }
}
