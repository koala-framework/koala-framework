<?php
class Vps_View_Helper_FormField
{
    public function formField($vars)
    {
        extract($vars);
        if (isset($preHtml)) { echo $preHtml; }
        if (isset($html)) {
            echo $html;
        } elseif (isset($items)) {
            foreach ($items as $i) {
                $this->formField($i);
            }
        } elseif (isset($component)) {
            echo Vps_View_Component::renderCachedComponent($component);
        }
        if (isset($postHtml)) { echo $postHtml; }
    }
}
