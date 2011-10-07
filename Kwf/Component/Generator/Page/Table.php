<?php
class Vps_Component_Generator_Page_Table extends Vps_Component_Generator_PseudoPage_Table
{
    protected $_idSeparator = '_';
    protected $_inherits = true;
    protected $_maxNameLength;
    protected $_eventsClass = 'Vps_Component_Generator_Page_Events_Table';

    protected function _init()
    {
        parent::_init();

        if (isset($this->_maxNameLength)) {
            $this->_settings['maxNameLength'] = $this->_maxNameLength;
        }
        if (!isset($this->_settings['maxNameLength'])) $this->_settings['maxNameLength'] = 100;
    }

    protected function _formatConfig($parentData, $row)
    {
        $data = parent::_formatConfig($parentData, $row);
        $data['isPage'] = true;
        if (isset($data['name']) && mb_strlen($data['name']) > $this->_settings['maxNameLength']) {
            $data['name'] = mb_substr($data['name'], 0, $this->_settings['maxNameLength']-3).'...';
        }
        return $data;
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['page'] = true;
        return $ret;
    }

}
