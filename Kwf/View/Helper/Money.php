<?php
class Kwf_View_Helper_Money
{
    protected $_view = null;
    public function setView($view)
    {
        $this->_view = $view;
    }

    public function money($amount)
    {
        $component = null;
        if (isset($this->_view) && $this->_view && $this->_view->data &&
            $this->_view->data instanceof Kwf_Component_Data
        ) {
            $component = $this->_view->data;
        }
        if ($component) {
            $format = $component->getBaseProperty('money.format');
            $decimals = $component->getBaseProperty('money.decimals');
            $decimalSeparator = $component->getBaseProperty('money.decimalSeparator');
            $thousandSeparator = $component->getBaseProperty('money.thousandSeparator');

            if (is_null($decimalSeparator)) $decimalSeparator = $component->trlcKwf('decimal separator', ".");
            if (is_null($thousandSeparator)) $thousandSeparator = $component->trlcKwf('thousands separator', ",");
        } else {
            $format = Kwf_Config::getValue('money.format');
            $decimals = Kwf_Config::getValue('money.decimals');
            $decimalSeparator = trlcKwf('decimal separator', ".");
            $thousandSeparator = trlcKwf('thousands separator', ",");
        }

        $number = number_format($amount, $decimals, $decimalSeparator, $thousandSeparator);
        return str_replace('{0}', $number, $format);
    }
}
