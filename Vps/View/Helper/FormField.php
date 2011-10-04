<?php
class Vps_View_Helper_FormField
{
    public function formField($vars)
    {
        echo $this->returnFormField($vars);
    }

    /**
     * Diese Methode returned. Die eigentliche (obere) funktion wurde aus
     * rückwärtskompatibilität belassen. Diese Methode wird zB im
     * getTemplateVars der MultiCheckbox verwendet.
     */
    public function returnFormField($vars)
    {
        extract($vars);
        $ret = '';
        if (isset($mask)) $ret .= '{' . $mask . '}';
        if (isset($preHtml)) { $ret .= $preHtml; }
        if (isset($html)) {
            $ret .= $html;
        } elseif (isset($items)) {
            foreach ($items as $i) {
                $ret .= $this->returnFormField($i);
            }
        } elseif (isset($component)) {
            $view = new Vps_Component_Renderer();
            $ret .= $view->renderComponent($component);
        }
        if (isset($postHtml)) { $ret .= $postHtml; }
        if (isset($mask)) $ret .= '{/' . $mask . '}';
        return $ret;
    }
}
