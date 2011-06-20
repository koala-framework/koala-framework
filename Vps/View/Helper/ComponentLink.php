<?php
class Vps_View_Helper_ComponentLink extends Vps_Component_View_Helper_Abstract
{
    public function componentLink($target, $text = null, $cssClass = null, $get = array(), $anchor = null)
    {
        if ($target instanceof Vps_Component_Data) {
            $target = $this->getTargetPage($target);
            if (!$text) $text = $target->name;
            return $this->getLink($target->url, $target->rel, $text, $cssClass, $get, $anchor);
        } else {
            if (is_array($target)) {
                $url = $target['url'];
                $rel = isset($target['rel']) ? $target['rel'] : '';
            } else {
                $url = $target;
                $rel = '';
            }
            return $this->getLink($url, $rel, $text, $cssClass, $get, $anchor);
        }
    }

    public function getLink($url, $rel, $text, $cssClass, $get, $anchor)
    {
        if (!empty($get)) {
            $url .= '?';
            foreach ($get as $key => $val) $url .= "&$key=$val";
        }
        if ($this->_getRenderer() instanceof Vps_View_MailInterface) {
            $url = '*redirect*' . $url . '*';
        }

        if ($anchor) $url .= "#$anchor";
        if (is_array($cssClass)) $cssClass = implode(' ', $cssClass);
        $cssClass = $cssClass ? " class=\"$cssClass\"" : '';
        return "<a href=\"$url\" rel=\"$rel\"$cssClass>$text</a>";
    }

    public function getTargetPage($component)
    {
        $ret = $component->getPage();
        if (is_instance_of($ret->componentClass, 'Vpc_Basic_LinkTag_Abstract_Component')) {
            if (!$ret->getComponent()->hasContent()) {
                return null;
            }
        }
        return $ret;
    }

}
