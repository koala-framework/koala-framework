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
        if ($this->_view && $this->_view->data && $this->_view->data instanceof Kwf_Component_Data) {
            $component = $this->_view->data;
        }

        $decimals = 2;
        if ($component) {
            $decimalSeparator = $component->trlcKwf('decimal separator', ".");
            $thousandSeparator = $component->trlcKwf('thousands separator', ",");
        } else {
            $decimalSeparator = trlcKwf('decimal separator', ".");
            $thousandSeparator = trlcKwf('thousands separator', ",");
        }
        $format = Kwf_Registry::get('config')->moneyFormat;

        if ($component) {
            $c = $component;
            while ($c && !Kwc_Abstract::getFlag($c->componentClass, 'hasMoneyFormat')) {
                $c = $c->parent;
            }
            if ($c) {
                $formats = $c->getComponent()->getMoneyFormat();
                if (isset($formats['format'])) {
                    $format = $formats['format'];
                }
                if (isset($formats['decimals'])) {
                    $decimals = $formats['decimals'];
                }
                if (isset($formats['decimalSeparator'])) {
                    $decimalSeparator = $formats['decimalSeparator'];
                }
                if (isset($formats['thousandSeparator'])) {
                    $thousandSeparator = $formats['thousandSeparator'];
                }
            }
        }

        $number = number_format($amount, $decimals, $decimalSeparator, $thousandSeparator);
        return str_replace('{0}', $number, $format);
    }
}
