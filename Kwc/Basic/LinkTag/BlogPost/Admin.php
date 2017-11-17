<?php
class Kwc_Basic_LinkTag_BlogPost_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $data = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId('blog_'.$row->blog_post_id, array('subroot' => $data));
        if (!$data) return '';
        return $data->name;
    }

    public function getCardForms()
    {
        $ret = array();

        foreach (Kwc_Abstract::getComponentClassesByParentClass('Kwc_Blog_Directory_Component') as $class) {
            $name = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($class, 'componentName'));
            $name = str_replace('.', ' ', $name);
            $ret[$class] = array(
                'form' => Kwc_Abstract_Form::createComponentForm($this->_class, 'child'),
                'title' => $name
            );
        }

        return $ret;
    }
}
