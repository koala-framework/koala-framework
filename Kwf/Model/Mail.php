<?php
class Kwf_Model_Mail extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwf_enquiries';
    protected $_rowClass = 'Kwf_Model_Mail_Row';

    protected $_mailerClass = 'Kwf_Mail'; // muss instanceof Kwf_Mail sein
    protected $_mailTemplate;
    protected $_mailMasterTemplate;
    protected $_additionalStore = null;
    protected $_spamFields = array('*');
    protected $_attachmentSaveFolder = null;

    protected function _init()
    {
        $this->_siblingModels['vars'] = new Kwf_Model_Mail_VarsSibling(array('fieldName' => 'serialized_mail_vars'));
        $this->_siblingModels['essentials'] = new Kwf_Model_Field(array('fieldName' => 'serialized_mail_essentials'));
        $this->_dependentModels['Attachments'] = new Kwf_Model_FieldRows(array('fieldName'=>'mail_attachments'));
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
            if (!is_instance_of($config['mailerClass'], 'Kwf_Mail')) {
                throw new Kwf_Exception("mailerClass must be instance of Kwf_Mail. '".$config['mailerClass']."' given.");
            }
            $this->_mailerClass = $config['mailerClass'];
        }

        if (!empty($config['additionalStore'])) {
            if (is_string($config['additionalStore'])) $config['additionalStore'] = new $config['additionalStore']();
            $this->_additionalStore = $config['additionalStore'];
        }

        if (isset($config['spamFields'])) {
            if (!is_array($config['spamFields'])) {
                throw new Kwf_Exception("config 'spamFields' for '".get_class($this)."' must be of type 'array', you've given type '".gettype($config['spamFields'])."'");
            }
            $this->_spamFields = $config['spamFields'];
        }
    }

    public function setAttachmentSaveFolder($path)
    {
        $this->_attachmentSaveFolder = $path;
    }

    public function getAttachmentSaveFolder()
    {
        if (!is_null($this->_attachmentSaveFolder)) return $this->_attachmentSaveFolder;

        $ret = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')->getUploadDir();
        if (substr($ret, -1) != '/') $ret .= '/';
        $ret .= 'mailattachments';
        if (!file_exists($ret) || !is_dir($ret)) {
            mkdir($ret);
        }
        $this->_attachmentSaveFolder = $ret;
        return $this->_attachmentSaveFolder;
    }

    public function getAdditionalStore()
    {
        return $this->_additionalStore;
    }

    public function createRow(array $data=array())
    {
        $row = parent::createRow($data);

        if (!is_instance_of($this->_mailerClass, 'Kwf_Mail')) {
            throw new Kwf_Exception("mailerClass must be instance of 'Kwf_Mail'. '".$this->_mailerClass."' given.");
        }

        if (empty($this->_mailTemplate)) {
            throw new Kwf_Exception("mail template not set for class '".get_class($this)."' in construct-config");
        }
        if (!is_string($this->_mailTemplate)) {
            throw new Kwf_Exception("mail template must be of type 'string' but type '".gettype($this->_mailTemplate)."' has been set");
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
