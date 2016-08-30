<?php
class Kwc_Posts_Detail_Report_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['reportMail'] = 'content@vivid-planet.com';
        $ret['reportMailName'] = '';
        $ret['generators']['child']['component']['success'] = 'Kwc_Posts_Detail_Report_Success_Component';
        $ret['flags']['noIndex'] = true;
        $ret['plugins'] = array('Kwf_Component_Plugin_Login_Component');
        return $ret;
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $post = $this->getData()->parent->parent;
        $row->url = 'http://' . $_SERVER['HTTP_HOST'] . $post->getPage()->url;
        $row->content = $post->row->content;
        $row->htmlContent = Kwc_Posts_Detail_Component::replaceCodes($post->row->content);
    }
}
