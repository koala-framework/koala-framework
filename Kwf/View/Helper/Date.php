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
        $language = null;
        if (isset($this->_view) && $this->_view->data && $this->_view->data instanceof Kwf_Component_Data) {
            if (!$format) $format = $this->_view->data->trlKwf('Y-m-d');
            $language = $this->_view->data->getLanguage();
        } else if (isset($this->_view) && $this->_view->item && $this->_view->item instanceof Kwf_Component_Data) {
            if (!$format) $format = $this->_view->item->trlKwf('Y-m-d');
            $language = $this->_view->item->getLanguage();
        } else {
            if (!$format) $format = trlKwf('Y-m-d');
        }

        if (!$date || substr($date, 0, 10) == '0000-00-00') return '';

        $d = new Kwf_Date($date);
        return $d->format($format, $language);
    }
}
