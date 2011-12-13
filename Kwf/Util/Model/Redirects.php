<?php
class Kwf_Util_Model_Redirects extends Kwf_Model_Db
{
    protected $_table = 'kwf_redirects';

    public function findRedirectUrl($type, $source)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('type', $type);
        $s->whereEquals('source', $source);
        $s->whereEquals('active', true);
        $row = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Redirects')->getRow($s);
        $target = null;
        if ($row) {
            if ($row->target_type == 'extern') {
                $target = $row->target;
            } else if ($row->target_type == 'intern' || $row->target_type == 'downloadTag') {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->target);
                if ($c) $target = $c->url;
            }
        }
        return $target;
    }
}
