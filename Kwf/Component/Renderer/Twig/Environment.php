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
        static $up;
        if (!isset($up)) $up = Kwf_Config::getValue('application.uniquePrefix');
        if (!$up) return $class;

        $classes = Kwc_Abstract::getSetting($context['data']->componentClass, 'processedCssClass');;
        $classes = explode(' ', $classes);
        $ret = array();
        foreach ($classes as $i) {
            $ret[] = $i.'__'.$class;
        }
        return implode(' ', $ret);
    }

}
