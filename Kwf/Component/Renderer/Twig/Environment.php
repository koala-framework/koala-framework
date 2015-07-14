<?php
class Kwf_Component_Renderer_Twig_Environment extends Kwf_View_Twig_Environment
{
    public function __construct(Kwf_Component_Renderer_Abstract $renderer)
    {
        parent::__construct();
        $this->addGlobal('renderer', new Kwf_Component_Renderer_Twig_Helper($renderer));
        $this->addFilter(new Twig_SimpleFilter('bemClass',
            array('Kwf_Component_Renderer_Twig_Environment', 'bemClass'),
            array('needs_context'=>true)));
    }

    public static function bemClass($context, $class)
    {
        $bemClasses = $context['bemClasses'];
        if ($bemClasses === false) return $class;
        $ret = array();
        foreach ($bemClasses as $i) {
            $ret[] = $i.$class;
        }
        return implode(' ', $ret);
    }
}
