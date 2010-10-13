<?php
class Vps_View_Helper_Date
{
    private $_view;
    public function setView($view)
    {
        $this->_view = $view;
    }

    public function date($date, $format = null)
    {
        if (!$format) {
            if ($this->_view->item && $this->_view->item instanceof Vps_Component_Data) {
                $format = $this->_view->item->trlVps('Y-m-d');
            } else if ($this->_view->data && $this->_view->data instanceof Vps_Component_Data) {
                $format = $this->_view->data->trlVps('Y-m-d');
            } else {
                $format = trlVps('Y-m-d');
            }
        }

        if (!$date || substr($date, 0, 10) == '0000-00-00') return '';

        $d = new Vps_Date($date);
        return $d->format($format);

        /*
        Das ist schneller, kann aber keine übersetzung bei Monatsnamen etc
        $datetime = new DateTime($date);
        return $datetime->format($format);
        */
    }
}
