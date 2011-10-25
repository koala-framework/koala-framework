<?php
class Kwf_Util_Model_Redirects extends Kwf_Model_Db
{
    protected $_table = 'kwf_redirects';

    public function findRedirectUrl($type, $source)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('type', $type);
        $s->whereEquals('source', $source);
        $s->whereEquals('active', 1);
        $row = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Redirects')->getRow($s);
        $target = null;
        if ($row) {
            if (substr($row->target, 0, 1) == '/') {
                $target = $row->target;
            } else if (Kwf_Component_Data_Root::getComponentClass()) {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->target);
                if ($c) $target = $c->url;
            }
        }
        return $target;
    }
}
