<?php
class Vps_Util_Component
{
    public static function getHtmlLocations($components)
    {
        $ids = array();
        $msg = '';
        foreach ($components as $c) {
            if ($c->getPage()) {
                if (in_array($c->getPage()->componentId, $ids)) continue;
                $ids[] = $c->getPage()->componentId;
                $url = $c->getPage()->url;
                $msg .= "<br /><a href=\"$url\" target=\"_blank\">".$c->getTitle()."</a>";
            } else {
                if (in_array($c->componentId, $ids)) continue;
                $ids[] = $c->componentId;
                $t = array();
                do {
                    if (is_instance_of($c->componentClass, 'Vpc_Root_Abstract')) continue;
                    if (is_instance_of($c->componentClass, 'Vpc_Root_Category_Component')) continue;
                    if (is_instance_of($c->componentClass, 'Vpc_Root_DomainRoot_Domain_Component')) continue;
                    $n = Vpc_Abstract::getSetting($c->componentClass, 'componentName');
                    if ($n) {
                        $t[] = str_replace('.', ' ', $n);
                    }
                } while($c = $c->parent);
                if ($t) {
                    $msg .= "<br />".implode(' - ', array_reverse($t));
                } else {
                    $msg .= "<br />".trlVps("on multiple pages, eg. in a footer");
                }
            }
        }
        return $msg;
    }
}
