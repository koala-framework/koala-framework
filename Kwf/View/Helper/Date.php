<?php
class Kwf_View_Helper_Date
{
    protected $_view = null;
    public function setView($view)
    {
        $this->_view = $view;
    }

    public function date($date, $format = null)
    {
        if (!$format) {
            if ($this->_view && $this->_view->item && $this->_view->item instanceof Kwf_Component_Data) {
                $format = $this->_view->item->trlKwf('Y-m-d');
            } else if ($this->_view && $this->_view->data && $this->_view->data instanceof Kwf_Component_Data) {
                $format = $this->_view->data->trlKwf('Y-m-d');
            } else {
                $format = trlKwf('Y-m-d');
            }
        }

        if (!$date || substr($date, 0, 10) == '0000-00-00') return '';

        $d = new Kwf_Date($date);
        return $d->format($format);

        /*
        Das ist schneller, kann aber keine übersetzung bei Monatsnamen etc
        $datetime = new DateTime($date);
        return $datetime->format($format);
        */
    }
}
