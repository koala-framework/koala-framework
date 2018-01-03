<?php
class Kwc_Advanced_IntegratorTemplate_Trl_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            $url = $this->getComponent()->getRow()->url;
            if (!$url) {
                $url = $this->chained->url;
            }
            return $url ? $url : '';
        } else {
            return parent::__get($var);
        }
    }

    public function getAbsoluteUrl()
    {
        return $this->url;
    }
}
