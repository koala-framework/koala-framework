<?php
class Vps_Model_Mail extends Vps_Model_Db
{
    protected $_tableName = 'Vps_Model_Mail_Table';
    protected $_rowClass = 'Vps_Model_Mail_Row';

    protected $_mailTemplate;
    protected $_spamFields;

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
        if (!empty($config['spamFields'])) {
            $this->setSpamFields($config['spamFields']);
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

    public function setSpamFields(array $spamFields)
    {
        $this->_spamFields = $spamFields;
    }

    public function getSpamFields()
    {
        return $this->_spamFields;
    }






    
}
