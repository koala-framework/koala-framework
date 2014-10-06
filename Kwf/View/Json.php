<?php
class Kwf_View_Json extends Zend_View_Abstract
{
    private $_outputFormat = 'kwfConnection';

    public function setPlainOutputFormat()
    {
        $this->_outputFormat = '';
    }

    public function kwc($config)
    {
        $this->config = $config;
    }

    public function ext($class, $config = array()) {
        $this->class = $class;
        $this->config = $config;
    }

    public function render($name)
    {
        return $this->_run();
    }

    public function getOutput()
    {
        $this->strictVars(true);
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ('_' != substr($key, 0, 1)) {
                $out[$key] = $value;
            }
        }

        if ($this->_outputFormat == 'kwfConnection' && !isset($out['success'])) {
            $out['success'] = !isset($out['exception']) && !isset($out['error']);
        }
        return $out;
    }

    protected function _run()
    {
        $ret = Zend_Json::encode($this->getOutput());
        if (isset($this->exception) && $this->exception) {
            $ret = $this->_jsonFormat($ret);
        }
        return $ret;
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
