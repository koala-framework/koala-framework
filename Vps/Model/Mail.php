<?php
class Vps_Model_Mail extends Vps_Model_Db_Proxy
{
    protected $_table = 'vps_enquiries';
    protected $_rowClass = 'Vps_Model_Mail_Row';

    protected $_mailerClass = 'Vps_Mail_Template';
    protected $_mailTemplate;
    protected $_mailMasterTemplate;
    protected $_additionalStore = null;
    protected $_spamFields = array('*');


    protected function _init()
    {
        $this->_siblingModels['vars'] = new Vps_Model_Mail_VarsSibling(array('fieldName' => 'serialized_mail_vars'));
        $this->_siblingModels['essentials'] = new Vps_Model_Field(array('fieldName' => 'serialized_mail_essentials'));
        parent::_init();
    }

    public function __construct(array $config = array())
    {
        parent::__construct($config);

        if (!empty($config['tpl'])) {
            $this->_mailTemplate = $config['tpl'];
        } else if (!empty($config['componentClass'])) {
            $this->_mailTemplate = $config['componentClass'];
        }

        if (!empty($config['masterTpl'])) {
            $this->_mailMasterTemplate = $config['masterTpl'];
        }

        if (!empty($config['mailerClass'])) {
            $this->_mailerClass = $config['mailerClass'];
        }

        if (!empty($config['additionalStore'])) {
            if (is_string($config['additionalStore'])) $config['additionalStore'] = new $config['additionalStore']();
            $this->_additionalStore = $config['additionalStore'];
        }

        if (isset($config['spamFields'])) {
            if (!is_array($config['spamFields'])) {
                throw new Vps_Exception("config 'spamFields' for '".get_class($this)."' must be of type 'array', you've given type '".gettype($config['spamFields'])."'");
            }
            $this->_spamFields = $config['spamFields'];
        }
    }

    public function getAdditionalStore()
    {
        return $this->_additionalStore;
    }

    public function createRow(array $data=array())
    {
        $row = parent::createRow($data);

        if (empty($this->_mailTemplate)) {
            throw new Vps_Exception("mail template not set for class '".get_class($this)."' in construct-config");
        }
        if (!is_string($this->_mailTemplate)) {
            throw new Vps_Exception("mail template must be of type 'string' but type '".gettype($this->_mailTemplate)."' has been set");
        }
        $row->setTemplate($this->_mailTemplate);
        $row->setMailerClass($this->_mailerClass);
        $row->setSpamFields($this->_spamFields);

        if ($this->_mailMasterTemplate) {
            $row->setMasterTemplate($this->_mailMasterTemplate);
        }
        return $row;
    }
}
