<?php
abstract class Kwf_Component_PluginRoot_PostRenderCutter implements
    Kwf_Component_PluginRoot_Interface_MaskComponent,
    Kwf_Component_PluginRoot_Interface_PostRender
{
    const MASK_CODE_BEGIN = 'Begin';
    const MASK_CODE_END = 'End';

    private function _getMaskCode(Kwf_Component_Data $page, $maskType, $maskCode, $params)
    {
        if ($maskType == self::MASK_TYPE_NOMASK) return '';
        $params = $params ? base64_encode(json_encode($params)) : '';
        $ret = "<!-- postRenderPlugin{$maskCode}".'{'.$page->componentId.'}'." $params";
        if ($maskType == self::MASK_TYPE_HIDE) {
            if ($maskCode == self::MASK_CODE_BEGIN) {
                $ret = "{$ret} hide--><script type=\"text/x-kwf-masked\">";
            } else if ($maskCode == self::MASK_CODE_END) {
                $ret = "</script>$ret -->";
            }
        } else {
            $ret = "$ret -->";
        }
        return $ret;
    }

    /**
     * return mask_type (= implicit params=null)
     * return array('type'=>mask_type, 'params' => params)
     */
    protected function _getMask(Kwf_Component_Data $page)
    {
        return self::MASK_TYPE_NOMASK;
    }

    public function getMask(Kwf_Component_Data $page)
    {
        $maskParams = null;
        $mask = $this->_getMask($page);
        if (is_array($mask)) {
            $maskType = $mask['type'];
            if (isset($mask['params'])) {
                $maskParams = $mask['params'];
            }
        } else {
            $maskType = $mask;
        }
        return array(
            'type' => $maskType,
            'params' => $maskParams
        );
    }

    public function processMask($mask)
    {
        if ($mask == self::MASK_TYPE_HIDE) {
            return false;
        }
    }

    public function getMaskCode(Kwf_Component_Data $page)
    {
        $mask = $this->getMask($page);
        $maskType = $mask['type'];
        $maskParams = $mask['params'];
        return array(
            'begin' => $this->_getMaskCode($page, $mask['type'], self::MASK_CODE_BEGIN, $mask['params']),
            'end' => $this->_getMaskCode($page, $mask['type'], self::MASK_CODE_END, $mask['params']),
        );
    }

    protected function _getMaskedContentParts($output, $maskType = null, $params = null)
    {
        $matches = array();
        if (is_null($params)) {
            $params = '[^ ]*';
        } else if ($params) {
            $params = base64_encode(json_encode($params));
        } else {
            $params = '';
        }
        if ($maskType == self::MASK_TYPE_SHOW) {
            $endTag = '-->';
            $startTag = '<!--';
        } else if ($maskType == self::MASK_TYPE_HIDE) {
            $endTag = 'hide--><script type="text/x-kwf-masked">';
            $endTag .= '|>'; //previous code, might be in view cache
            $startTag = '</script><!--';
            $startTag .= '|<'; //previous code, might be in view cache
        } else {
            $endTag = '-->|hide--><script type="text/x-kwf-masked">';
            $endTag .= '|>'; //previous code, might be in view cache
            $startTag = '<!--|</script><!--';
            $startTag .= '|<'; //previous code, might be in view cache
        }
        $pattern = "#(<!-- postRenderPluginBegin{([^ ]*)} ($params) ($endTag))(.*?)(($startTag) postRenderPluginEnd{\\2} $params -->)#s";
        preg_match_all($pattern, $output, $matches);
        $ret = array();
        foreach (array_keys($matches[0]) as $key) {
            $ret[] = array(
                'maskBegin' => $matches[1][$key],
                'maskEnd' => $matches[6][$key],
                'params' => $matches[3][$key] ? json_decode(base64_decode($matches[3][$key]), true) : null,
                'maskType' => $matches[4][$key] == '-->' ? self::MASK_TYPE_SHOW : self::MASK_TYPE_HIDE,
                'output' => $matches[0][$key],
                'maskedContent' => $matches[5][$key],
            );
        }
        return $ret;
    }

    protected function _removeMaskedComponentLinks($output, $params = null)
    {
        foreach ($this->_getMaskedContentParts($output, self::MASK_TYPE_SHOW, $params) as $part) {
            $output = str_replace($part['output'], '', $output);
        }
        return $output;
    }

    protected function _removeMasksFromComponentLinks($output, $params = null)
    {
        foreach ($this->_getMaskedContentParts($output, self::MASK_TYPE_HIDE, $params) as $part) {
            $output = str_replace($part['maskBegin'], '', $output);
            $output = str_replace($part['maskEnd'], '', $output);
        }
        return $output;
    }
}
