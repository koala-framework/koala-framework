<?php
class Vps_Controller_Action_User_MailController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_tableName = 'Vps_Dao_UserMails';

    protected function _initFields()
    {
        if (Zend_Registry::get('userModel')->getAuthedUserRole() == 'admin') {
            $this->_form->add(new Vps_Form_Field_TextField('template', trlVps('Template')))
                ->setWidth(300);
            $this->_form->add(new Vps_Form_Field_TextField('variable', trlVps('Variable')))
                ->setWidth(300);
        } else {
            $this->_form->add(new Vps_Form_Field_ShowField('template', trlVps('Template')));
            $this->_form->add(new Vps_Form_Field_ShowField('variable', trlVps('Variable')));
        }
        $this->_form->add(new Vps_Form_Field_TextField('name', trlVps('Name')))
            ->setWidth(300);
        $this->_form->add(new Vps_Form_Field_TextArea('text', trlVps('Text')))
            ->setWidth(400)
            ->setHeight(150);
        $this->_form->add(new Vps_Form_Field_HtmlEditor('html', trlVps('Html')))
            ->setEnableAlignments(false)
            ->setEnableColors(false)
            ->setEnableFont(false)
            ->setEnableFontSize(false)
            ->setWidth(400)
            ->setHeight(200);
    }

    // deprecated - wird nicht mehr verwendet
    private function _getMailTemplatesRecursive($scanPath = 'application/views')
    {
        $values = array();
        foreach (new DirectoryIterator($scanPath) as $file) {
            if ($file->isFile()) {
                $name = '';
                if (preg_match('#^application/views/(.+)$#', $scanPath, $matches)) {
                    $name = $matches[1].'/';
                }
                $name .= $file->getFilename();
                $name = preg_replace('#\\.(txt|html)\\.tpl$#', '', $name);
                if (!isset($values[$name])) {
                    $values[$name] = $name;
                }
            } else if ($file->isDir() && !$file->isDot() && $file->getFilename() != '.svn') {
                $values = array_merge(
                    $values,
                    $this->_getMailTemplatesRecursive($scanPath.'/'.$file->getFilename())
                );
            }
        }
        return $values;
    }
}
