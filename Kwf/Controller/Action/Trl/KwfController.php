<?php
class Kwf_Controller_Action_Trl_KwfController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = "Kwf_Trl_Model_Kwf";
    protected $_buttons = array();
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 30;
    protected $_editDialog = array('controllerUrl'=>'/kwf/trl/kwf-edit',
                                   'width'=>600,
                                   'height'=>550);

    protected function _initColumns()
    {
        $lang = $this->_getLanguage();

        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width'=>180,
            'queryFields' => array($lang, $lang.'_plural')
        );
        $this->_filters['empty'] = array(
            'type'=>'Button',
            'text' => trl('only empty'),
            'icon' => new Kwf_Asset('textfield'),
            'cls' => 'x-btn-text-icon',
            'skipWhere' => true
        );


        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('id', 'Id', 50));
        $this->_columns->add(new Kwf_Grid_Column('context', trlKwf('Context'), 100));
        $this->_columns->add(new Kwf_Grid_Column($lang, $lang.' '.trlKwf('Singular'), 350));
        $this->_columns->add(new Kwf_Grid_Column($lang.'_plural', $lang.' '.trlKwf('Plural'), 150));

        $langs = self::getLanguages();
        if ($langs) {
            foreach ($langs as $lang) {
                if ($lang != $this->_getLanguage()) {
                    $this->_columns->add(new Kwf_Grid_Column($lang, $lang.' '.trlKwf('Singular'), 350));
                    $this->_columns->add(new Kwf_Grid_Column($lang.'_plural', $lang.' '.trlKwf('Plural'), 150));
                }
            }
        }

        parent::_initColumns();
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_getParam('query_empty')) {
            $or = array();
            $langs = self::getLanguages();
            foreach ($langs as $lang) {
                $or[] = new Kwf_Model_Select_Expr_Equal($lang, '');
            }
            $ret->where(new Kwf_Model_Select_Expr_Or($or));

        }
        return $ret;
    }

    protected function _getLanguage()
    {
        return 'en';
    }

    static public function getLanguages()
    {
        $config = Zend_Registry::get('config');
        $langs = array();
        if ($config->webCodeLanguage) $langs[] = $config->webCodeLanguage;

        $possibleUserLanguages = array();
        if ($config->languages) {
            foreach ($config->languages as $lang=>$name) {
                $possibleUserLanguages[] = $lang;
            }
        }
        $userModel = Kwf_Registry::get('userModel');
        if (isset($userModel->getAuthedUser()->language) &&
            $userModel->getAuthedUser()->language &&
            in_array($userModel->getAuthedUser()->language, $possibleUserLanguages))
        {
            $langs[] = $userModel->getAuthedUser()->language;
        }

        if (Kwf_Component_Data_Root::getComponentClass()) {
            $lngClasses = array();
            foreach(Kwc_Abstract::getComponentClasses() as $c) {
                if (Kwc_Abstract::hasSetting($c, 'baseProperties') &&
                    in_array('language', Kwc_Abstract::getSetting($c, 'baseProperties'))
                ) {
                    $lngClasses[] = $c;
                }
            }
            $lngs = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($lngClasses, array('ignoreVisible'=>true));
            foreach ($lngs as $c) {
                if (Kwf_Registry::get('acl')->getComponentAcl()->isAllowed($userModel->getAuthedUser(), $c)) {
                    $langs[] = $c->getLanguage();
                }
            }
        }
        return array_unique($langs);
    }

    public function indexAction ()
    {
        $config = array(
            'controllerUrl' => $this->getRequest()->getBaseUrl().$this->getRequest()->getPathInfo(),
            'language' => 'en'
        );
        $this->view->ext('Kwf.Trl.Grid', $config);
    }
}
