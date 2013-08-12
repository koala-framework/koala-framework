<?php
class Kwc_Advanced_SocialBookmarks_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Social Bookmarks');
        $ret['ownModel'] = 'Kwc_Advanced_SocialBookmarks_Model';
        $ret['cssClass'] = 'webStandard';
        $ret['iconSet'] = 'Rounded';
        $ret['flags']['hasAlternativeComponent'] = true;
        $ret['extConfig'] = 'Kwc_Abstract_Composite_ExtConfigForm';
        return $ret;
    }

    private function _getNetworks($currentPage)
    {
        $networks = array();
        foreach (Kwf_Model_Abstract::getInstance('Kwc_Advanced_SocialBookmarks_AvaliableModel')->getRows() as $n) {
            $networks[$n->id] = $n->toArray();
        }
        $s = new Kwf_Model_Select();
        $s->order('pos');
        $ret = array();
        foreach ($this->getRow()->getChildRows('Networks', $s) as $net) {
            if (isset($networks[$net->network_id])) {
                $icon = '/Kwc/Advanced/SocialBookmarks/Icons/'.$this->_getSetting('iconSet').'/';
                if (file_exists(KWF_PATH.$icon.$net->network_id.'.jpg')) {
                    $icon .= $net->network_id.'.jpg';
                } else if (file_exists(KWF_PATH.$icon.$net->network_id.'.png')) {
                    $icon .= $net->network_id.'.png';
                } else {
                    $icon = false;
                }
                if ($icon) $icon = '/assets/kwf'.$icon;
                $url = str_replace('{0}', $currentPage->getAbsoluteUrl(), $networks[$net->network_id]['url']);
                $ret[] = array(
                    'name' => $networks[$net->network_id]['name'],
                    'url' => $url,
                    'icon' => $icon
                );
            }
        }
        return $ret;
    }

    public function hasContent()
    {
        return count($this->_getNetworks($this->getData())) > 0;
    }

    public function getTemplateVarsWithNetworks($currentPage)
    {
        $ret = parent::getTemplateVars();
        $ret['networks'] = $this->_getNetworks($currentPage);
        return $ret;
    }

    public function getTemplateVars()
    {
        return $this->getTemplateVarsWithNetworks($this->getData()->parent);
    }

    public static function getAlternativeComponents()
    {
        return array(
            'inherit'=>'Kwc_Advanced_SocialBookmarks_Inherit_Component'
        );
    }

    public static function useAlternativeComponent($componentClass, $parentData, $generator)
    {
        $c = $parentData;
        while (!$c->inherits) $c = $c->parent;

        $c = $c->parent;
        if (!$c) return false;
        while (!$c->inherits) $c = $c->parent;

        $instances = Kwf_Component_Generator_Abstract::getInstances($c, array(
                'inherit' => true
        ));
        if (in_array($generator, $instances, true)) {
            //wir wurden geerbt weils über uns ein parentData mit dem gleichen generator gibt
            return 'inherit';
        } else {
            return false;
        }
    }
}
