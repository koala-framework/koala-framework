<?php
class Kwf_Component_Renderer_Twig_Environment extends Twig_Environment
{
    public function __construct(Kwf_Component_Renderer_Abstract $renderer)
    {
        parent::__construct(new Kwf_Component_Renderer_Twig_FilesystemLoader('.'), array(
            'cache' => 'cache/twig',
            'auto_reload' => false
        ));
        $this->addGlobal('renderer', new Kwf_Component_Renderer_Twig_Helper($renderer));
    }
}
