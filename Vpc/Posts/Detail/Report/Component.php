<?php
class Vpc_Posts_Detail_Report_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['reportMail'] = 'content@vivid-planet.com';
        $ret['reportMailName'] = '';
        $ret['generators']['child']['component']['success'] = 'Vpc_Posts_Detail_Report_Success_Component';
        $ret['flags']['noIndex'] = true;
        $ret['plugins'] = array('Vps_Component_Plugin_Login_Component');
        return $ret;
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $post = $this->getData()->parent->parent;
        $row->url = 'http://' . $_SERVER['HTTP_HOST'] . $post->getPage()->url;
        $row->content = $post->row->content;
        $row->htmlContent = Vpc_Posts_Detail_Component::replaceCodes($post->row->content);
    }
}
