<?php
class Vpc_Advanced_SocialBookmarks_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Social Bookmarks');
        $ret['ownModel'] = 'Vpc_Advanced_SocialBookmarks_Model';
        $ret['cssClass'] = 'webStandard';
        $ret['iconSet'] = 'Default';
        $ret['flags']['alternativeComponent'] = 'Vpc_Advanced_SocialBookmarks_Inherit_Component';
        return $ret;
    }

    private function _getNetworks($currentPage)
    {
        //TODO: funktioniert mit mehreren domains nicht korrekt
        $pageUrl = 'http://'.Vps_Registry::get('config')->server->domain.$currentPage->url;

        $networks = array();
        foreach (Vps_Model_Abstract::getInstance('Vpc_Advanced_SocialBookmarks_AvaliableModel')->getRows() as $n) {
            $networks[$n->id] = $n->toArray();
        }
        $s = new Vps_Model_Select();
        $s->order('pos');
        $ret = array();
        foreach ($this->getRow()->getChildRows('Networks', $s) as $net) {
            if (isset($networks[$net->network_id])) {
                $icon = '/Vpc/Advanced/SocialBookmarks/Icons/'.$this->_getSetting('iconSet').'/';
                if (file_exists(VPS_PATH.$icon.$net->network_id.'.jpg')) {
                    $icon .= $net->network_id.'.jpg';
                } else if (file_exists(VPS_PATH.$icon.$net->network_id.'.png')) {
                    $icon .= $net->network_id.'.png';
                } else {
                    $icon = false;
                }
                if ($icon) $icon = '/assets/vps'.$icon;
                $url = str_replace('{0}', $pageUrl, $networks[$net->network_id]['url']);
                $ret[] = array(
                    'name' => $networks[$net->network_id]['name'],
                    'url' => $url,
                    'icon' => $icon
                );
            }
        }
        return $ret;
    }

    public function getTemplateVarsWithNetworks($currentPage)
    {
        $ret = parent::getTemplateVars();
        $ret['networks'] = $this->_getNetworks($this->getData()->parent);
        return $ret;
    }

    public function getTemplateVars()
    {
        return $this->getTemplateVarsWithNetworks($this->getData()->parent);
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        $c = $parentData;
        while (!$c->inherits) $c = $c->parent;

        $c = $c->parent;
        if (!$c) return false;
        while (!$c->inherits) $c = $c->parent;

        $instances = Vps_Component_Generator_Abstract::getInstances($c, array(
                'inherit' => true
        ));
        if (in_array($generator, $instances, true)) {
            //wir wurden geerbt weils über uns ein parentData mit dem gleichen generator gibt
            return true;
        } else {
            return false;
        }
    }
}
