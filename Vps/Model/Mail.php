<?php
class Vps_Model_Mail extends Vps_Model_FnF
{
    protected $_rowClass = 'Vps_Model_Mail_Row';
    protected $_rowsetClass = 'Vps_Model_Mail_Rowset';

    protected $_mailTemplate;

    public function __construct(array $config = array())
    {
        parent::__construct($config);
        if (!empty($config['tpl'])) {
            $this->setMailTemplate($config['tpl']);
        } else if (!empty($config['componentClass'])) {
            $tpl = Vpc_Admin::getComponentFile($config['componentClass'], 'Component', 'html.tpl');
            $tpl = str_replace('.html.tpl', '', $tpl);
            $this->setMailTemplate($tpl);
        }
        
    }

    public function setMailTemplate($tpl)
    {
        $this->_mailTemplate = $tpl;
    }

    public function getMailTemplate()
    {
        return $this->_mailTemplate;
    }
}
