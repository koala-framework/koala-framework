<?php
class Vps_Mail_Template_View extends Vps_View_Mail
{
    protected $_mailTplViewMasterTemplate = null;
    protected $_txtTemplate;
    protected $_htmlTemplate;

    public function __construct($template, $masterTemplate = 'Master')
    {
        parent::__construct();

        // das substr mit Vpc_ muss sein weil auf prosalzburg test server sonst nur eine weiße seite kommt
        if (is_object($template) || ((substr($template, 0, 4) == 'Vpc_' || substr($template, 0, 4) == 'Vps_')
            && class_exists($template) && is_instance_of($template, 'Vpc_Abstract'))
        ) {
            if (is_object($template)) {
                if ($template instanceof Vpc_Abstract) {
                    $template = $template->getData();
                }
                if (!$template instanceof Vps_Component_Data) {
                    throw new Vps_Exception("template must be instance of 'Vpc_Abstract' or 'Vps_Component_Data'");
                }
                $template = $template->componentClass;
            }

            $this->_txtTemplate = Vpc_Admin::getComponentFile($template, 'Component', 'txt.tpl');
            if (!$this->_txtTemplate) {
                throw new Vps_Exception("Component class '$template' needs at least a .txt.tpl mail template.");
            }
            $this->_htmlTemplate = Vpc_Admin::getComponentFile($template, 'Component', 'html.tpl');
        } else {
            if (substr($template, 0, 1) == '/') {
                throw new Vps_Exception("Absolute mail template paths are not allowed. You called '$template'.");
            }

            if (false === $this->getScriptPath("$template.txt.tpl")) {
                $template = "mails/$template";
                if (false === $this->getScriptPath("$template.txt.tpl")) {
                    throw new Vps_Exception("There has to exist at least a .txt.tpl mail template for '$template'.");
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
            $host = Vps_Registry::get('config')->server->domain;
        }
        $this->webUrl = 'http://'.$host;
        $this->host = $host;

        $this->applicationName = Vps_Registry::get('config')->application->name;
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
        return '';
    }
}
