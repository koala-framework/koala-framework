<?php
class Vpc_Basic_LinkTag_FirstChildPage_Data extends Vps_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            $page = $this->getChildPage();
            if (!$page) return '';
            return $page->url;
        } else if ($var == 'rel') {
            $page = $this->getChildPage();
            if (!$page) return '';
            return $page->rel;
        } else {
            return parent::__get($var);
        }
    }

}
