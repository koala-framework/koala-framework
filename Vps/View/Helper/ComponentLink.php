<?php
class Vps_View_Helper_ComponentLink
{
    public function componentLink($m, $text = null, $cssClass = null, $get = array(), $anchor = null)
    {
        if ($m instanceof Vps_Component_Data) {
            $m = $m->getPage();
            if (is_instance_of($m->componentClass, 'Vpc_Basic_LinkTag_Abstract_Component')) {
                if (!$m->getComponent()->hasContent()) {
                    return '';
                }
            }
            $m = array(
                'url' => $m->url,
                'rel' => $m->rel,
                'name' => $m->name
            );
        }
        if (!$get) $get = array();
        if (!empty($get)) {
            $m['url'] .= '?';
        }
        foreach ($get as $key => $val) {
            $m['url'] .= "&$key=$val";
        }
        if ($anchor) {
            $m['url'] .= "#$anchor";
        }
        if (!$text) $text = $m['name'];
        if ($cssClass) {
            $cssClass = " class=\"$cssClass\"";
        } else {
            $cssClass = '';
        }
        return "<a href=\"{$m['url']}\" rel=\"{$m['rel']}\"$cssClass>$text</a>";
    }
}
