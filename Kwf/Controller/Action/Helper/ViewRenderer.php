<?php
class Kwf_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{
    public function init()
    {
        $this->setNoController();
        $this->setViewSuffix('tpl');
        $this->setRender('master');
    }

    public function preDispatch()
    {
        $prefix = substr($this->getRequest()->getParam('action'), 0, 4);
        if ($prefix == 'json' || $this->_actionController instanceof Zend_Rest_Controller) {
            $this->getRequest()->setParam('jsonOutput', true);
        }

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
                $this->getResponse()->setHeader('Content-Type', 'application/json');
            } else {
                $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8');
            }
        }
        parent::postDispatch();
    }

    public function isJson()
    {
        return (bool)$this->getRequest()->getParam('jsonOutput');
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
