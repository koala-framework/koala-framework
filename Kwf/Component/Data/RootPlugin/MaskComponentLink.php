<?php
class Kwf_Component_Data_RootPlugin_MaskComponentLink
    implements Kwf_Component_Data_RootPlugin_Interface_MaskComponentLink
{
    const MASK_TYPE_NOMASK = 'noMask';
    const MASK_TYPE_HIDE = 'hide';
    const MASK_TYPE_SHOW = 'show';
    const MASK_CODE_BEGIN = 'Begin';
    const MASK_CODE_END = 'End';

    private function _getMaskCode($maskType, $maskCode, $params)
    {
        if ($maskType == self::MASK_TYPE_NOMASK) return '';
        $params = base64_encode(json_encode($params));
        $ret = '';
        if ($maskCode == self::MASK_CODE_BEGIN || $maskType == self::MASK_TYPE_SHOW) $ret .= '<!--';
        $ret .= " postRenderPlugin$maskCode $params ";
        if ($maskCode == self::MASK_CODE_END || $maskType == self::MASK_TYPE_SHOW) $ret .= '-->';
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
}
