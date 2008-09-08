<?php
class Vps_Model_Mail extends Vps_Model_FnF //Vps_Model_Db
{
//     protected $_tableName = 'Vps_Model_Mail_Table';
    protected $_rowClass = 'Vps_Model_Mail_Row';

    protected $_mailTemplate;
    protected $_mailMasterTemplate;
    protected $_spamFields;
    protected $_saveModel = null;
    protected $_saveMailVarsModel = null;
    protected $_saveMailEssentialsModel = null;
    protected $_additionalMailVarsModel = null;

    // Die zwei Vps_Model_Field's sollten nicht überschrieben werden, da ansonsten
    // der spamschutz nicht mehr funktionieren könnte
    // (muss hardcoded in /Vps/Controller/Action/Spam/SetController.php sein)
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        if (!empty($config['tpl'])) {
            $this->setMailTemplate($config['tpl']);
        } else if (!empty($config['componentClass'])) {
            $this->setMailTemplate($config['componentClass']);
        }

        if (!empty($config['masterTpl'])) {
            $this->_mailMasterTemplate = $config['masterTpl'];
        }

        if (!$this->getMailTemplate()) {
            throw new Vps_Exception("mail template not set for class '".get_class($this)."' in construct-config");
        }

        // Model_Db, welches die Daten beherbergt
        if (isset($config['saveModel']) && is_object($config['saveModel'])) {
            $this->_saveModel = $config['saveModel'];
        } else if (!empty($config['saveModelName'])) {
            $m = $config['saveModelName'];
            $this->_saveModel = new $m();
        } else {
            $this->_saveModel = new Vps_Model_Db(array('table' => new Vps_Model_Mail_Table()));
        }

        // Model_Field für mail vars
        $modelConfig = array(
            'fieldName'   => 'serialized_mail_vars',
            'parentModel' => $this->_saveModel
        );
        if (!empty($config['saveMailVarsModel'])) {
            $this->_saveMailVarsModel = $config['saveMailVarsModel'];
        } else if (!empty($config['saveMailVarsModelName'])) {
            $m = $config['saveMailVarsModelName'];
            $this->_saveMailVarsModel = new $m($modelConfig);
        } else {
            $this->_saveMailVarsModel = new Vps_Model_Field($modelConfig);
        }
        if (!($this->_saveMailVarsModel instanceof Vps_Model_Field)) {
            throw new Vps_Exception("saveMailVarsModel '".get_class($this->_saveMailVarsModel)
                ."' in class '".get_class($this)."' must be an instance of 'Vps_Model_Field'"
            );
        }

        // Model_Field für mail essentials
        $modelConfig = array(
            'fieldName'   => 'serialized_mail_essentials',
            'parentModel' => $this->_saveModel
        );
        if (!empty($config['saveMailEssentialsModel'])) {
            $this->_saveMailEssentialsModel = $config['saveMailEssentialsModel'];
        } else if (!empty($config['saveMailEssentialsModelName'])) {
            $m = $config['saveMailEssentialsModelName'];
            $this->_saveMailEssentialsModel = new $m($modelConfig);
        } else {
            $this->_saveMailEssentialsModel = new Vps_Model_Field($modelConfig);
        }
        if (!($this->_saveMailEssentialsModel instanceof Vps_Model_Field)) {
            throw new Vps_Exception("saveMailEssentialsModel '".get_class($this->_saveMailEssentialsModel)
                ."' in class '".get_class($this)."' must be an instance of 'Vps_Model_Field'"
            );
        }

        if (!empty($config['additionalMailVarsModel'])) {
            $this->_additionalMailVarsModel = $config['additionalMailVarsModel'];
        } else if (!empty($config['additionalMailVarsModelName'])) {
            $m = $config['additionalMailVarsModelName'];
            $this->_additionalMailVarsModel = new $m();
        }

        // spam
        if (!$this->_saveModel && !empty($config['spamFields'])) {
            throw new Vps_Exception("'saveModelName' must be set when using 'spamFields' in class '".get_class($this)."'");
        }

        if (!empty($config['spamFields'])) {
            $this->setSpamFields($config['spamFields']);
        } else {
            $this->setSpamFields(array('*'));
        }
    }

    public function getSaveModel()
    {
        if (!$this->_saveModel) return null;
        return $this->_saveModel;
    }

    public function getSaveMailVarsModel()
    {
        if (!$this->_saveMailVarsModel) return null;
        return $this->_saveMailVarsModel;
    }

    public function getSaveMailEssentialsModel()
    {
        if (!$this->_saveMailEssentialsModel) return null;
        return $this->_saveMailEssentialsModel;
    }

    public function getAdditionalMailVarsModel()
    {
        if (!$this->_additionalMailVarsModel) return null;
        return $this->_additionalMailVarsModel;
    }

    public function setMailTemplate($tpl)
    {
        $this->_mailTemplate = $tpl;
    }

    public function getMailTemplate()
    {
        return $this->_mailTemplate;
    }

    public function getMailMasterTemplate()
    {
        return $this->_mailMasterTemplate;
    }

    public function setSpamFields($spamFields)
    {
        $this->_spamFields = $spamFields;
    }

    public function getSpamFields()
    {
        return $this->_spamFields;
    }

}
