<?php
class Kwf_Component_Renderer_Twig_Environment extends Kwf_View_Twig_Environment
{
    public function __construct(Kwf_Component_Renderer_Abstract $renderer)
    {
        parent::__construct();
        $this->addGlobal('renderer', new Kwf_Component_Renderer_Twig_Helper($renderer));
    }
}
