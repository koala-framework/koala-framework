<?php
class Kwf_Component_Renderer_Mail extends Kwf_Component_Renderer_Abstract
{
    const RENDER_HTML = 'html';
    const RENDER_TXT = 'txt';

    private $_renderFormat = self::RENDER_HTML;
    private $_recipient;
    private $_htmlStyles;

    public function getRenderFormat()
    {
        return $this->_renderFormat;
    }

    public function setRenderFormat($renderFormat)
    {
        $this->_renderFormat = $renderFormat;
    }

    public function getRecipient()
    {
        return $this->_recipient;
    }

    public function setRecipient(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        $this->_recipient = $recipient;
    }

    protected function _getRendererName()
    {
        return 'mail_' . $this->_renderFormat;
    }

    public function setHtmlStyles($htmlStyles)
    {
        $this->_htmlStyles = $htmlStyles;
    }

    public function getTemplate(Kwf_Component_Data $component, $type)
    {
        if ($type == 'Component') {
            $mailType = 'Mail.' . $this->getRenderFormat();
        } else if ($file == 'Partial') {
            $mailType = 'Partial.' . $this->getRenderFormat();
        }
        $template = Kwc_Abstract::getTemplateFile($component->componentClass, $mailType);
        if (!$template) {
            $template = parent::getTemplate($component, $type);
        }
        return $template;
    }

    public function renderComponent($component)
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        $this->_renderComponent = $component;

        $content = false;
        if ($this->_enableCache && $component->isPage) { //use fullPage cache only for pages
            $content = Kwf_Component_Cache::getInstance()->load($component->componentId, $this->_getRendererName(), 'fullPage');
            $this->_minLifetime = null;
        }

        Kwf_Benchmark::checkpoint('load fullPage cache');

        $statType = null;
        if (!$content) {

            $helper = new Kwf_Component_View_Helper_Component();
            $helper->setRenderer($this);
            $content = $helper->component($component);

            $pass1Cacheable = true;
            $content = $this->_render(1, $content, $pass1Cacheable);
            Kwf_Benchmark::checkpoint('render pass 1');

            if (strpos($content, '<kwc2 ') === false) {
                //if there are no components that need second render cycle start HtmlParser now
                //and cache result in fullPage cache
                if ($this->_renderFormat == self::RENDER_HTML && $this->_htmlStyles) {
                    $p = new Kwc_Mail_HtmlParser($this->_htmlStyles);
                    $content = $p->parse($content);
                    Kwf_Benchmark::checkpoint('html parser (in fullPage)');
                }
            }
            if ($this->_enableCache && $pass1Cacheable && $component->isPage) {
                Kwf_Component_Cache::getInstance()->save($component, $content, $this->_getRendererName(), 'fullPage', '', $this->_minLifetime);
            }
        }
        $hasPass2Placeholders = strpos($content, '<kwc2 ')!==false;

        $content = $this->_render(2, $content);
        Kwf_Benchmark::checkpoint('render pass 2');

        //if there where components that needed second render cycle the HtmlParser wasn't started yet
        //do that now (should be avoided as it's slow)
        if ((!$component->isPage || $hasPass2Placeholders) && $this->_renderFormat == self::RENDER_HTML && $this->_htmlStyles) {
            $p = new Kwc_Mail_HtmlParser($this->_htmlStyles);
            $content = $p->parse($content);
            Kwf_Benchmark::checkpoint('html parser');
        }

        Kwf_Component_Cache::getInstance()->writeBuffer();
        return $content;
    }
}
