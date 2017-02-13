<?php
class Kwf_Util_Model_Redirects extends Kwf_Model_Db
{
    protected $_table = 'kwf_redirects';

    public function findRedirectUrl($type, $source, $host = null)
    {
        $target = $this->_fetchRedirectUrl($type, $source, $host);

        if (!$target && ($type == 'path' || $type == 'domainPath') && strpos($source, '?') !== false) {
            $queryParams = substr($source, strpos($source, '?')+1);
            $source = substr($source, 0, strpos($source, '?')-1);
            $target = $this->_fetchRedirectUrl($type, $source, $host);
            if ($target) {
                if (strpos($source, '?') !== false) {
                    $target .= '&';
                } else {
                    $target .= '?';
                }
                $target .= $queryParams;
            }
        }

        return $target;
    }

    private function _fetchRedirectUrl($type, $source, $host)
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
            $s->whereEquals('source', $sources);
        } else {
            if (substr($source, 0, 6) == '/media') {
                $parts = explode('/', $source);
                if (isset($parts[6])) $source = str_replace($parts[6], '%', $source);
                $s->where(new Kwf_Model_Select_Expr_Like('source', $source));
            } else {
                $sources = array(
                    $source,
                    $source.'/',
                );
                $s->whereEquals('source', $sources);
            }
        }
        $s->whereEquals('active', true);
        if ($type == 'path') {
            if ($root = Kwf_Component_Data_Root::getInstance()) {
                $domainComponents = $root->getDomainComponents(array('ignoreVisible' => true));
                if (count($domainComponents) > 1) {
                    $path = $root->getComponent()->formatPath(array('host' => $host, 'path' => ''));
                    if (!is_null($path)) {
                        $path = trim($path, '/');
                        $component = $root->getComponent()->getPageByUrl($path, null);
                        if ($component) {
                            $s->whereEquals('domain_component_id', $component->getDomainComponent()->dbId);
                        } else {
                            return null;
                        }
                    } else {
                        return null;
                    }
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
