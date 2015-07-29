<?php
class Kwf_Component_Renderer_Twig_Environment extends Kwf_View_Twig_Environment
{
    private $_renderer;
    public function __construct(Kwf_Component_Renderer_Abstract $renderer)
    {
        parent::__construct();
        $this->addGlobal('renderer', new Kwf_Component_Renderer_Twig_Helper($renderer));
        $this->addFilter(new Twig_SimpleFilter('bemClass',
            array('Kwf_Component_Renderer_Twig_Environment', 'bemClass'),
            array('needs_context'=>true)));
        $this->addFunction('includeCode', new Twig_SimpleFunction('includeCode', array($this, 'includeCode'), array(
            'needs_context' => true,
        )));
        $this->_renderer = $renderer;
    }

    public static function bemClass($context, $class)
    {
        $bemClass = $context['bemClass'];
        if ($bemClass === false) return $class;
        return $bemClass.$class;
    }

    public function includeCode($context, $position)
    {
        $helper = $this->_renderer->getHelper('includeCode');
        $helper->setView((object)$context);
        return new Twig_Markup($helper->includeCode($position), 'utf-8');
    }
}
