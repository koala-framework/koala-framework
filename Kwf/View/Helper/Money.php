<?php
class Kwf_View_Helper_Money
{
    protected $_data = null;
    public function setView($view)
    {
        if ($view && $view->data && $view->data instanceof Kwf_Component_Data) {
            $this->setData($view->data);
        }
    }

    public function setData(Kwf_Component_Data $data)
    {
        $this->_data = $data;
    }

    public function money($amount)
    {
        $component = $this->_data;
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
        return str_replace('{0}', '<span class="kwfUp-amount">'.$number.'</span>', $format);
    }
}
