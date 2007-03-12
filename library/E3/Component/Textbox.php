<?php
require_once 'E3/Component/Abstract.php';

class E3_Component_Textbox extends E3_Component_Abstract {
    public function getTemplateVars()
    {
        return array("Text" => "foo");
    }
}
?>
