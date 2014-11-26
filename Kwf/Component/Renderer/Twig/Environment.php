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

        $this->addFilter(new Twig_SimpleFilter('date',
            array('Kwf_Component_Renderer_Twig_Environment', 'date'),
            array('needs_context' => true)));
        $this->addFilter(new Twig_SimpleFilter('dateTime',
            array('Kwf_Component_Renderer_Twig_Environment', 'dateTime'),
            array('needs_context' => true)));
    }

    public static function date($context, $date, $format = null)
    {
        $language = null;
        if (isset($context['data']) && $context['data'] instanceof Kwf_Component_Data) {
            if (!$format) $format = $context['data']->trlKwf('Y-m-d');
            $language = $context['data']->getLanguage();
        } else if (isset($context['item']) && $context['item'] instanceof Kwf_Component_Data) {
            if (!$format) $format = $context['item']->trlKwf('Y-m-d');
            $language = $context['item']->getLanguage();
        } else {
            if (!$format) $format = trlKwf('Y-m-d');
        }

        if (!$date || substr($date, 0, 10) == '0000-00-00') return '';

        $d = new Kwf_Date($date);
        return $d->format($format, $language);
    }

    public function dateTime($context, $date, $format = null)
    {
        if (isset($context['data']) && $context['data'] instanceof Kwf_Component_Data) {
            if (!$format) $format = $context['data']->trlKwf('Y-m-d H:i');
        } else if (isset($context['item']) && $context['item'] instanceof Kwf_Component_Data) {
            if (!$format) $format = $context['item']->trlKwf('Y-m-d H:i');
        } else {
            if (!$format) $format = trlKwf('Y-m-d H:i');
        }
        return self::date($context, $date, $format);
    }
}
