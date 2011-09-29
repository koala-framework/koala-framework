<?php
abstract class Vps_Component_Abstract_ContentSender_Abstract
{
    protected $_data;
    public function __construct(Vps_Component_Data $data)
    {
        $this->_data = $data;
    }

    /**
     * returned rel will be added to rel of data for component that uses this ContentSender
     *
     * used for Lightbox
     */
    public function getLinkRel() { return ''; }

    abstract public function sendContent($includeMaster = true);
    protected function _callProcessInput() { return array(); }
    protected function _callPostProcessInput($process) {}
}
