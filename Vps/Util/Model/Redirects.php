<?php
class Vps_Util_Model_Redirects extends Vps_Model_Db
{
    protected $_table = 'vps_redirects';

    public function findRedirectUrl($type, $source)
    {
        $s = new Vps_Model_Select();
        $s->whereEquals('type', $type);
        $s->whereEquals('source', $source);
        $s->whereEquals('active', 1);
        $row = Vps_Model_Abstract::getInstance('Vps_Util_Model_Redirects')->getRow($s);
        $target = null;
        if ($row) {
            if (substr($row->target, 0, 1) == '/') {
                $target = $row->target;
            } else if (Vps_Component_Data_Root::getComponentClass()) {
                $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->target);
                if ($c) $target = $c->url;
            }
        }
        return $target;
    }
}
