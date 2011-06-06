<?php
class Vps_Controller_Action_Component_ClearCacheController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('add', 'save');
    protected $_buttons = array('save');

    public function preDispatch()
    {
        $this->_model = new Vps_Model_FnF(array(
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

        $this->_form->add(new Vps_Form_Field_Static(
            '1. '.trlVps('Fill out the Form').'<br />'
            .'2. '.trlVps('Click the save button above').'<br />'
            .'3. '.trlVps('If no error pops up, the cache has been cleared sucessfully').'<br /><br />'
        ));

        $this->_form->add(new Vps_Form_Field_Static(trlVps('Affected component / part (e.g.: References)')));
        $this->_form->add(new Vps_Form_Field_TextField('clear_cache_affected'))
            ->setWidth(500)
            ->setLabelSeparator('')
            ->setAllowBlank(false);

        $this->_form->add(new Vps_Form_Field_Static(trlVps('Why do you need to clear the cache? (steps to reproduce / description)')));
        $this->_form->add(new Vps_Form_Field_TextArea('clear_cache_comment'))
            ->setWidth(500)
            ->setHeight(250)
            ->setMinLength(30)
            ->setLabelSeparator('')
            ->setAllowBlank(false);
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);

        $cmd = "php bootstrap.php clear-cache";

        $ret = $output = null;
        exec($cmd, $output, $ret);
        if ($ret != 0) throw new Vps_Exception_Client("Cache clearing failed");

        $mail = new Vps_Mail();
        $user = Vps_Registry::get('userModel')->getAuthedUser();
        $mail->setFrom($user->email, $user->__toString());
        $mail->addTo('ufx@vivid-planet.com', 'Franz Unger');
        $mail->addTo('ns@vivid-planet.com', 'Niko Sams');
        $mail->addTo('mh@vivid-planet.com', 'Markus Hauser');
        $mail->setSubject('Clear Cache Report. Affected: '.$row->clear_cache_affected);
        $mail->setBodyText(
            "Clear Cache Report\n\n"
            ."Web: ".(Vps_Registry::get('config')->application->name)." (".Vps_Registry::get('config')->application->id.")\n"
            ."User: ".(Vps_Registry::get('userModel')->getAuthedUser()->__toString())."\n"
            ."Time: ".date("d.m.Y, H:i:s")."\n\n"
            ."Affected component / part:\n".$row->clear_cache_affected."\n\n"
            ."Steps to reproduce / description:\n".$row->clear_cache_comment."\n"
        );
        $mail->send();

        $row->clear_cache_affected = '';
        $row->clear_cache_comment = '';
    }
}
