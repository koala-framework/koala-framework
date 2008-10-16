<?php
class Vps_View_Helper_ComponentLink
{
    public function componentLink($m, $text = null, $cssClass = null, $get = array())
    {
        if ($m instanceof Vps_Component_Data) {
            $m = $m->getPage();
            $m = array(
                'url' => $m->url,
                'rel' => $m->rel,
                'name' => $m->name
            );
        }
        if (!empty($get)) {
            $m['url'] .= '?';
        }
        foreach ($get as $key => $val) {
            $m['url'] .= "&$key=$val";
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
