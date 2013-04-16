<?php
class Kwf_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{
    public function init()
    {
        $this->setNoController();
        $this->setViewSuffix('tpl');
        $this->setRender('master');
    }

    public function preDispatch() {
        $module = $this->getRequest()->getParam('module');
        if ($this->isJson()) {
            $this->setView(new Kwf_View_Json());
        } else {
            $this->setView(new Kwf_View_Ext());
        }

        if ((null !== $this->_actionController) && (null === $this->_actionController->view)) {
            $this->_actionController->view = $this->view;
        }
        parent::preDispatch();
    }

    public function postDispatch()
    {
        if (!$this->_noRender
            && (null !== $this->_actionController)
            && $this->getRequest()->isDispatched()
            && !$this->getResponse()->isRedirect()) {

            if ($this->isJson()) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $this->getResponse()->setHeader('Content-Type', 'text/html');
                } else if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                    $this->getResponse()->setHeader('Content-Type', 'text/javascript');
                } else {
                    if (!headers_sent()) {
                        header('Content-Type: text/html');
                    }
                    Kwf_Benchmark::output();
                    echo "<pre>";
                    echo htmlspecialchars($this->_jsonFormat(Zend_Json::encode($this->view->getOutput())));
                    echo "</pre>";
                    $this->setNoRender();
                }
            } else {
                $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
            }
        }
        parent::postDispatch();
    }

    public function isJson()
    {
        return substr($this->getRequest()->getActionName(), 0, 4) == 'json';
    }

    private function _jsonFormat($json)
    {
        $tab = "  ";
        $ret = "";
        $indentLevel = 0;
        $inString = false;

        $len = strlen($json);

        for($c = 0; $c < $len; $c++)
        {
            $char = $json[$c];
            switch($char)
            {
                case '{':
                case '[':
                    if(!$inString)
                    {
                        $ret .= $char . "\n" . str_repeat($tab, $indentLevel+1);
                        $indentLevel++;
                    }
                    else
                    {
                        $ret .= $char;
                    }
                    break;
                case '}':
                case ']':
                    if(!$inString)
                    {
                        $indentLevel--;
                        $ret .= "\n" . str_repeat($tab, $indentLevel) . $char;
                    }
                    else
                    {
                        $ret .= $char;
                    }
                    break;
                case ',':
                    if(!$inString)
                    {
                        $ret .= ",\n" . str_repeat($tab, $indentLevel);
                    }
                    else
                    {
                        $ret .= $char;
                    }
                    break;
                case ':':
                    if(!$inString)
                    {
                        $ret .= ": ";
                    }
                    else
                    {
                        $ret .= $char;
                    }
                    break;
                case '"':
                    if($c > 0 && $json[$c-1] != '\\')
                    {
                        $inString = !$inString;
                    }
                default:
                    $ret .= $char;
                    break;
            }
        }

        return $ret;
    }
}
