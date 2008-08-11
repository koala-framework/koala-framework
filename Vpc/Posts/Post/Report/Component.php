<?php
class Vpc_Posts_Post_Report_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['reportMail'] = 'content@vivid-planet.com';
        $ret['reportMailName'] = '';
        return $ret;
    }
    
    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $post = $this->getData()->parent;
        $row->url = 'http://' . $_SERVER['HTTP_HOST'] . $post->getPage()->url;
        $row->content = $post->row->content;
        $row->htmlContent = Vpc_Posts_Post_Component::replaceCodes($post->row->content);
    }
}
