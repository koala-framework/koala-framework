<?php
class Kwf_Util_Model_Redirects extends Kwf_Model_Db
{
    protected $_table = 'kwf_redirects';

    public function findRedirectUrl($type, $source, $host = null)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('type', $type);
        $source = rtrim($source, '/');
        if ($type == 'domain' || $type == 'domainPath') {
            $sourceWithoutWww = preg_replace('#^www\\.#', '', $source);
            $sources = array(
                $source,
                'http://'.$source,
                $source.'/',
                'http://'.$source.'/',
                $sourceWithoutWww,
                'http://'.$sourceWithoutWww,
                $sourceWithoutWww.'/',
                'http://'.$sourceWithoutWww.'/',
            );
        } else {
            $sources = array(
                $source,
                $source.'/',
            );
        }
        $s->whereEquals('source', $sources);
        $s->whereEquals('active', true);
        if ($type == 'path') {
            foreach (Kwc_Abstract::getComponentClasses() as $c) {
                if (Kwc_Abstract::hasSetting($c, 'baseProperties') &&
                    in_array('domain', Kwc_Abstract::getSetting($c, 'baseProperties'))
                ) {
                    $domain = call_user_func(array($c, 'getComponentForHost'), $host);
                    $s->whereEquals('domain_component_id', $domain->dbId);
                    break;
                }
            }
        }
        $row = $this->getRow($s);
        $target = null;

        if ($row) {
            if ($row->target_type == 'extern') {
                $target = $row->target;
            } else if ($row->target_type == 'intern' || $row->target_type == 'downloadTag') {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->target);
                if ($c) $target = $c->getAbsoluteUrl();
            }
        }
        return $target;
    }
}
