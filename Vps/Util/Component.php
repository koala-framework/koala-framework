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

    public static function getDuplicateProgressSteps(Vps_Component_Data $source)
    {
        return $source->generator->getDuplicateProgressSteps($source);
    }

    public static function duplicate(Vps_Component_Data $source, Vps_Component_Data $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        $new = $source->generator->duplicateChild($source, $parentTarget, $progressBar);

        if (!$new) {
            throw new Vps_Exception("Failed duplicating '$source->componentId'");
        }

        Vps_Component_Generator_Abstract::clearInstances();
        Vps_Component_Data_Root::reset();

        //TODO: schöner wär ein flag bei den komponenten ob es diese fkt im admin
        //gibt und dann für alle admins aufrufen
        //ändern sobald es für mehrere benötigt wird
        Vpc_Root_TrlRoot_Chained_Admin::duplicated($source, $new);

        return $new;
    }

    public static function dispatchRender()
    {
        if (!isset($_REQUEST['url']) || !$_REQUEST["url"]) {
            throw new Vps_Exception('Url Parameter required');
        }
        $url = $_REQUEST['url'];
        $componentId = isset($_REQUEST['componentId']) ? $_REQUEST['componentId'] : null;
        $parsedUrl = parse_url($url);
        $_GET = array();
        if (isset($parsedUrl['query'])) {
            foreach (explode('&' , $parsedUrl['query']) as $get) {
                if (!$get) continue;
                $pos = strpos($get, '=');
                $_GET[substr($get, 0, $pos)] = substr($get, $pos+1); //ouch
            }
        }
        if ($componentId) {
            $data = Vps_Component_Data_Root::getInstance()->getComponentById($componentId);
        } else {
            $data = Vps_Component_Data_Root::getInstance()->getPageByUrl($url, null);
        }
        if (!$data) throw new Vps_Exception_NotFound();
        $contentSender = Vpc_Abstract::getSetting($data->componentClass, 'contentSender');
        $contentSender = new $contentSender($data);
        $contentSender->sendContent(false);

        Vps_Benchmark::shutDown();
        exit;
    }
}
