<?php
class Kwf_Component_Renderer_Twig_Helper
{
    private $_renderer;
    public function __construct(Kwf_Component_Renderer_Abstract $renderer)
    {
        $this->_renderer = $renderer;
    }

    public function componentWithMaster(array $componentWithMaster)
    {
        return new Twig_Markup($this->_renderer->getHelper('componentWithMaster')->componentWithMaster($componentWithMaster), 'utf-8');
    }

    public function component(Kwf_Component_Data $component = null)
    {
        return new Twig_Markup($this->_renderer->getHelper('component')->component($component), 'utf-8');
    }

    public function componentLink(Kwf_Component_Data $component, $text = null, $config = array())
    {
        return new Twig_Markup($this->_renderer->getHelper('componentLink')->componentLink($component, $text, $config), 'utf-8');
    }

    public function link($target, $text = null, $config = array())
    {
        $helper = new Kwf_View_Helper_Link();
        return new Twig_Markup($helper->link($target, $text, $config), 'utf-8');
    }

    public function partials($component, $params = array())
    {
        return new Twig_Markup($this->_renderer->getHelper('partials')->partials($component, $params), 'utf-8');
    }

    public function dynamic($class)
    {
        return new Twig_Markup($this->_renderer->getHelper('dynamic')->dynamic($class), 'utf-8');
    }

    public function image($image, $alt = '', $cssClass = null)
    {
        return new Twig_Markup($this->_renderer->getHelper('image')->image($image, $alt, $cssClass), 'utf-8');
    }

    public function multiBox($boxName)
    {
        return new Twig_Markup($this->_renderer->getHelper('multiBox')->multiBox($boxName), 'utf-8');
    }

    public function formField($vars)
    {
        $helper = new Kwf_View_Helper_FormField();
        return new Twig_Markup($helper->formField($vars), 'utf-8');
    }

    public function getComponentTemplate($componentClass)
    {
        return Kwf_Component_Renderer_Twig_TemplateLocator::getComponentTemplate($componentClass);
    }
}
