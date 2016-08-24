<?php
class Kwc_Basic_LinkTag_Abstract_Cc_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            return $this->chained->url;;
        } else if ($var == 'url_mail_html') {
            return $this->chained->url_mail_html;
        } else if ($var == 'url_mail_txt') {
            return $this->chained->url_mail_txt;
        } else if ($var == 'rel') {
            return $this->chained->rel;
        } else {
            return parent::__get($var);
        }
    }

    public function getLinkDataAttributes()
    {
        return $this->chained->getLinkDataAttributes();
    }

    public function getLinkClass()
    {
        return $this->chained->getLinkClass();
    }
}
