<?php
class Vpc_Root_LanguageRoot_Generator extends Vps_Component_Generator_PseudoPage_Static
{
    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['showInPageTreeAdmin'] = true;
        return $ret;
    }
}