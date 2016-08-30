<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_AnchorLink_Data extends Kwc_Basic_LinkTag_Abstract_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            $anchor = $this->getComponent()->getRow()->anchor;
            if ($anchor) {
                return '#' . $anchor;
            }
            return '';
        } else if ($var == 'rel') {
            return '';
        } else {
            return parent::__get($var);
        }
    }
}
