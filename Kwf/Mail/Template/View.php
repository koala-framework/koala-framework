<?php
class Kwf_Mail_Template_View extends Kwf_View_Mail
{
    protected $_mailTplViewMasterTemplate = null;
    protected $_txtTemplate;
    protected $_htmlTemplate;

    /**
    * Create a .txt.tpl or .html.tpl file and set $template to the path.
    * @param string|Kwc_Abstract|Kwf_Component_Data $template: If it's a
    *          string it should point to the template in /views/mails. It's
    *          also possible to use a 'Kwc_Abstract' or 'Kwf_Component_Data'
    *          (This is used when the template destination is in this component-folder).
    *          There are no absolute paths allowed.
    * @param string $masterTemplate
    */
    public function __construct($template, $masterTemplate = 'Master')
    {
        parent::__construct();

        if (is_object($template) || in_array($template, Kwc_Abstract::getComponentClasses())) {
            if (is_object($template)) {
                if ($template instanceof Kwc_Abstract) {
                    $template = $template->getData();
                }
                if (!$template instanceof Kwf_Component_Data) {
                    throw new Kwf_Exception("template must be instance of 'Kwc_Abstract' or 'Kwf_Component_Data'");
                }
                $template = $template->componentClass;
            }

            $this->_txtTemplate = Kwc_Admin::getComponentFile($template, 'Component', 'txt.tpl');
            if (!$this->_txtTemplate) {
                throw new Kwf_Exception("Component class '$template' needs at least a .txt.tpl mail template.");
            }
            $this->_htmlTemplate = Kwc_Admin::getComponentFile($template, 'Component', 'html.tpl');
        } else {
            if (substr($template, 0, 1) == '/') {
                throw new Kwf_Exception("Absolute mail template paths are not allowed. You called '$template'.");
            }

            if (false === $this->getScriptPath("$template.txt.tpl")) {
                $template = "mails/$template";
                if (false === $this->getScriptPath("$template.txt.tpl")) {
                    throw new Kwf_Exception("There has to exist at least a .txt.tpl mail template for '$template'.");
                }
            }
            $this->_txtTemplate = "$template.txt.tpl";

            if (false !== $this->getScriptPath("$template.html.tpl")) {
                $this->_htmlTemplate = "$template.html.tpl";
            }
        }

        $this->_mailTplViewMasterTemplate = $masterTemplate;

        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Kwf_Registry::get('config')->server->domain;
        }
        $this->webUrl = (Kwf_Util_Https::supportsHttps() ? 'https' : 'http') . '://'.$host;
        $this->host = $host;

        $this->applicationName = Kwf_Registry::get('config')->application->name;
    }

    public function getTxtTemplate()
    {
        return $this->_txtTemplate;
    }

    public function getHtmlTemplate()
    {
        return $this->_htmlTemplate;
    }

    public function renderText()
    {
        $this->setMasterTemplate("mails/{$this->_mailTplViewMasterTemplate}.txt.tpl");
        return $this->render($this->getTxtTemplate());
    }

    public function renderHtml()
    {
        if ($this->getHtmlTemplate()) {
            $this->setMasterTemplate("mails/{$this->_mailTplViewMasterTemplate}.html.tpl");
            return $this->render($this->getHtmlTemplate());
        }
        return null;
    }
}
