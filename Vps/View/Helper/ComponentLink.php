<?php
class Vps_View_Helper_ComponentLink
{
    public function componentLink($m, $text = null, $cssClass = null, $get = array())
    {
        
        if ($m instanceof Vps_Component_Data) {
            $m = $m->getPage();
            $m = array(
                'href' => $m->url,
                'rel' => $m->rel,
                'text' => $m->name
            );
        }
        if (!empty($get)) {
            $m['href'] .= '?';
        }
        foreach ($get as $key => $val) {
            $m['href'] .= "&$key=$val";
        }
        if (!$text) $text = $m['text'];
        if ($cssClass) {
            $cssClass = " class=\"$cssClass\"";
        } else {
            $cssClass = '';
        }
        return "<a href=\"{$m['href']}\" rel=\"{$m['rel']}\"$cssClass>$text</a>";
    }
}
