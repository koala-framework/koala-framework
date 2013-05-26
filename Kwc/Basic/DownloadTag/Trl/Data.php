<?php
class Kwc_Basic_DownloadTag_Trl_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            $m = Kwc_Abstract::createModel($this->componentClass);
            $row = $m->getRow($this->dbId);
            if (!$row || !$row->own_download) {
                return $this->chained->url;
            }
            return $this->getChildComponent('-download')->url;
        } else if ($var == 'rel') {
            return $this->chained->rel;
        } else {
            return parent::__get($var);
        }
    }

    public function getAbsoluteUrl()
    {
        return $this->url;
    }
}
