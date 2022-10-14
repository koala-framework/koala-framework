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
        if ($amount === null || $amount === '') return '';

        $component = $this->_data;
        if ($component) {
            $format = $component->getBaseProperty('money.format');
            $decimals = $component->getBaseProperty('money.decimals');
            $decimalSeparator = $component->getBaseProperty('money.decimalSeparator');
            $thousandSeparator = $component->getBaseProperty('money.thousandSeparator');
            $amountFormat = $component->getBaseProperty('money.amountFormat');

            if (is_null($decimalSeparator)) $decimalSeparator = $component->trlcKwf('decimal separator', ".");
            if (is_null($thousandSeparator)) $thousandSeparator = $component->trlcKwf('thousands separator', ",");
        } else {
            $format = Kwf_Config::getValue('money.format');
            $decimals = Kwf_Config::getValue('money.decimals');
            $decimalSeparator = trlcKwf('decimal separator', ".");
            $thousandSeparator = trlcKwf('thousands separator', ",");
            $amountFormat = Kwf_Config::getValue('money.amountFormat');
        }
        $number = number_format($amount, $decimals, $decimalSeparator, $thousandSeparator);
        if ($amountFormat) {
            $number = str_replace('{0}', $number, $amountFormat);
        }
        return str_replace('{0}', '<span class="kwfUp-amount">'.Kwf_Util_HtmlSpecialChars::filter($number).'</span>', $format);
    }
}
