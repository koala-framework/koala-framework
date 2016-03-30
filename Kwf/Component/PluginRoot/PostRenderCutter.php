<?php
class Kwf_Component_PluginRoot_PostRenderCutter implements
    Kwf_Component_PluginRoot_Interface_MaskComponent,
    Kwf_Component_PluginRoot_Interface_PostRender
{
    const MASK_TYPE_NOMASK = 'noMask';
    const MASK_TYPE_HIDE = 'hide';
    const MASK_TYPE_SHOW = 'show';
    const MASK_CODE_BEGIN = 'Begin';
    const MASK_CODE_END = 'End';

    private function _getMaskCode($maskType, $maskCode, $params)
    {
        if ($maskType == self::MASK_TYPE_NOMASK) return '';
        $params = $params ? base64_encode(json_encode($params)) : '';
        $ret = '';
        $ret .= $maskCode == self::MASK_CODE_BEGIN || $maskType == self::MASK_TYPE_SHOW ? '<!--' : '<';
        $ret .= " postRenderPlugin$maskCode $params ";
        $ret .= $maskCode == self::MASK_CODE_END || $maskType == self::MASK_TYPE_SHOW ? '-->' : '>';
        return $ret;
    }

    protected function _getMaskType(Kwf_Component_Data $page)
    {
        return self::MASK_TYPE_NOMASK;
    }

    protected function _getMaskParams(Kwf_Component_Data $page)
    {
        return null;
    }

    public final function getMaskCode(Kwf_Component_Data $page)
    {
        $maskType = $this->_getMaskType($page);
        $maskParams = $this->_getMaskParams($page);
        return array(
            'begin' => $this->_getMaskCode($maskType, self::MASK_CODE_BEGIN, $maskParams),
            'end' => $this->_getMaskCode($maskType, self::MASK_CODE_END, $maskParams),
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
            $endTag = '>';
            $startTag = '<';
        } else {
            $endTag = '-->|>';
            $startTag = '<!--|<';
        }
        $pattern = "#(<!-- postRenderPluginBegin ($params) ($endTag))(.*?)(($startTag) postRenderPluginEnd $params -->)#s";
        preg_match_all($pattern, $output, $matches);
        $ret = array();
        foreach (array_keys($matches[0]) as $key) {
            $ret[] = array(
                'maskBegin' => $matches[1][$key],
                'maskEnd' => $matches[5][$key],
                'params' => $matches[2][$key] ? json_decode(base64_decode($matches[2][$key]), true) : null,
                'maskType' => $matches[3][$key] == '-->' ? self::MASK_TYPE_SHOW : self::MASK_TYPE_HIDE,
                'output' => $matches[0][$key],
                'maskedContent' => $matches[4][$key],
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

    public function processOutput($output)
    {
        return $output;
    }
}
