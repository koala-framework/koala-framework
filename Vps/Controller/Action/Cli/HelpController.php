<?php
class Vps_Controller_Action_Cli_HelpController extends Vps_Controller_Action
{
    public function indexAction()
    {
        echo "VPS CLI\n\n";
        echo "verfuegbare Befehle:\n";
        echo "tc: Regenerate TreeCache\n";
        echo "text-components: Regenerate vpc_basic_text_components\n";
        exit;
    }
}
