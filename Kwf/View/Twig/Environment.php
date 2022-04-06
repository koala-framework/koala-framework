<?php
class Kwf_View_Twig_Environment extends Twig_Environment
{
    public function __construct()
    {
        $config = array(
            'cache' => 'cache/twig',
            'auto_reload' => false
        );

        if (Kwf_Config::getValue('debug.twig')) {
            $config['debug'] = true;
        }
        parent::__construct(new Kwf_View_Twig_FilesystemLoader('.'), $config);

        if (isset($config['debug'])) {
            $this->addExtension(new Twig_Extension_Debug());
        }

        $this->addFilter(new Twig_SimpleFilter('date',
            array('Kwf_Component_Renderer_Twig_Environment', 'date'),
            array('needs_context' => true)));
        $this->addFilter(new Twig_SimpleFilter('dateTime',
            array('Kwf_Component_Renderer_Twig_Environment', 'dateTime'),
            array('needs_context' => true)));
        $this->addFilter(new Twig_SimpleFilter('money',
            array('Kwf_Component_Renderer_Twig_Environment', 'money'),
            array('needs_context' => true)));
        $this->addFilter(new Twig_SimpleFilter('mailEncodeText',
            array('Kwf_Component_Renderer_Twig_Environment', 'mailEncodeText')));
        $this->addFilter(new Twig_SimpleFilter('mailLink',
            array('Kwf_Component_Renderer_Twig_Environment', 'mailLink')));
        $this->addFilter(new Twig_SimpleFilter('hiddenOptions',
            array('Kwf_Component_Renderer_Twig_Environment', 'hiddenOptions')));
        $this->addFilter(new Twig_SimpleFilter('fileSize',
            array('Kwf_Component_Renderer_Twig_Environment', 'fileSize')));
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
        if (!$date || (is_string($date) && substr($date, 0, 10) == '0000-00-00')) return '';

        if (!$date instanceof Kwf_Date) {
            $date = new Kwf_Date($date);
        }
        return $date->format($format, $language);
    }

    public static function dateTime($context, $date, $format = null)
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

    public static function money($context, $amount)
    {
        $data = null;
        if (isset($context['data']) && $context['data'] instanceof Kwf_Component_Data) {
            $data = $context['data'];
        } else if (isset($context['item']) && $context['item'] instanceof Kwf_Component_Data) {
            $data = $context['item'];
        }

        if ($data) {
            $helperClass = $data->getBaseProperty('money.helperClass');
        } else {
            $helperClass = Kwf_Config::getValue('money.helperClass');
        }

        $helper = new $helperClass();
        if ($data) {
            $helper->setData($data);
        }
        return new Twig_Markup($helper->money($amount), 'utf-8');
    }

    public static function mailEncodeText($text)
    {
        $helper = new Kwf_View_Helper_MailEncodeText();
        return $helper->mailEncodeText($text);
    }

    public static function mailLink($mailAddress, $linkText = null, $cssClass = null)
    {
        $helper = new Kwf_View_Helper_MailLink();
        return new Twig_Markup($helper->mailLink($mailAddress, $linkText, $cssClass), 'utf-8');
    }

    public static function hiddenOptions($options, $class = 'options')
    {
        $helper = new Kwf_View_Helper_HiddenOptions();
        return new Twig_Markup($helper->hiddenOptions($options, $class), 'utf-8');
    }

    public static function fileSize($filesize)
    {
        $helper = new Kwf_View_Helper_FileSize();
        return new Twig_Markup($helper->fileSize($filesize), 'utf-8');
    }
}
