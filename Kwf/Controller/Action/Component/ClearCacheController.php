<?php
class Kwf_Controller_Action_Component_ClearCacheController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('add', 'save');
    protected $_buttons = array('save');

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwf.component.clearCache';
    }

    public function preDispatch()
    {
        $this->_model = new Kwf_Model_FnF(array(
            'primaryKey' => 'id',
            'fields' => array('id', 'clear_cache_affected', 'clear_cache_comment'),
            'data' => array(
                array('id' => 1, 'clear_cache_affected' => '', 'clear_cache_comment' => '')
            )
        ));
        parent::preDispatch();
    }

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->setId(1);
        $this->_form->setLabelWidth(30);

        $this->_form->add(new Kwf_Form_Field_Static(trlKwf('Affected component / part (e.g.: References)')));
        $this->_form->add(new Kwf_Form_Field_TextField('clear_cache_affected'))
            ->setWidth(500)
            ->setLabelSeparator('')
            ->setAllowBlank(false);

        $this->_form->add(new Kwf_Form_Field_Static(trlKwf('Why did you have to clear the cache? (steps to reproduce / description)')));
        $this->_form->add(new Kwf_Form_Field_TextArea('clear_cache_comment'))
            ->setWidth(500)
            ->setHeight(250)
            ->setLabelSeparator('')
            ->setAllowBlank(false);
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);

        $mail = new Kwf_Mail();
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        $mail->setReturnPath('noreply@'.preg_replace('#^www\.#', '', Kwf_Config::getValue('server.domain')));
        $mail->setFrom($user->email, $user->__toString());
        foreach (Kwf_Registry::get('config')->developers as $dev) {
            if (isset($dev->sendClearCacheReport) && $dev->sendClearCacheReport) {
                $mail->addTo($dev->email);
            }
        }
        $mail->setSubject('Clear Cache Report. Affected: '.$row->clear_cache_affected);
        $mail->setBodyText(
            "Clear Cache Report\n\n"
            ."Web: ".(Kwf_Registry::get('config')->application->name)." (".Kwf_Registry::get('config')->application->id.")\n"
            ."User: ".(Kwf_Registry::get('userModel')->getAuthedUser()->__toString())."\n"
            ."Time: ".date("d.m.Y, H:i:s")."\n\n"
            ."Affected component / part:\n".$row->clear_cache_affected."\n\n"
            ."Steps to reproduce / description:\n".$row->clear_cache_comment."\n"
        );
        $mail->send();

        $row->clear_cache_affected = '';
        $row->clear_cache_comment = '';
    }

    public function jsonClearCacheAction()
    {
        $type = $this->_getParam('type');
        $cc = Kwf_Util_ClearCache::getInstance();
        if ($type == 'media') {
            $cc->clearCache('media', false/*output*/, true/*refresh*/);
        } else if ($type == 'assets') {
            $cc->clearCache('assets', false/*output*/, true/*refresh*/);
        } else if ($type == 'trl') {
            foreach (glob('cache/model/zend_cache---trl_*') as $f) {
                unlink($f);
            }
            foreach (glob('cache/model/zend_cache---internal-metadatas---trl_*') as $f) {
                unlink($f);
            }
            Kwf_Cache_Simple::clear('trl-');
        }
    }
    public function jsonClearViewCacheAction()
    {
        $select = new Kwf_Model_Select();
        if ($this->_getParam('dbId')) {
            $select->where(new Kwf_Model_Select_Expr_Like('db_id', $this->_getParam('dbId')));
        }
        if ($this->_getParam('id')) {
            $select->where(new Kwf_Model_Select_Expr_Like('component_id', $this->_getParam('id')));
        }
        if ($this->_getParam('expandedId')) {
            $select->where(new Kwf_Model_Select_Expr_Like('expanded_component_id', $this->_getParam('expandedId')));
        }
        if ($this->_getParam('class')) {
            $select->where(new Kwf_Model_Select_Expr_Like('component_class', $this->_getParam('class')));
        }
        if ($this->_getParam('type')) {
            $select->where(new Kwf_Model_Select_Expr_Like('type', $this->_getParam('type')));
        }
        $select->whereEquals('deleted', false);

        $model = Kwf_Component_Cache::getInstance()->getModel();
        $this->view->entries = $model->countRows($select);
        if (!$this->view->entries) {
            throw new Kwf_Exception_Client("No active view cache entries found; nothing to do.");
        }
        if ($this->_getParam('force')) {
            Kwf_Component_Cache::getInstance()->deleteViewCache($select);
        }
    }
}
