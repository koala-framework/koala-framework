<?php
abstract class Kwf_Component_Abstract_ContentSender_Abstract
{
    /**
     * @var Kwf_Component_Data
     */
    protected $_data;
    public function __construct(Kwf_Component_Data $data)
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
