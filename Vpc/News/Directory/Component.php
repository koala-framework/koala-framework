<?php
class Vpc_News_Directory_Component extends Vpc_News_List_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('News.List');
        $ret['componentIcon'] = new Vps_Asset('newspaper');
        $ret['tablename'] = 'Vpc_News_Directory_Model';
        $ret['generators']['detail'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => 'Vpc_News_Detail_Component',
            'nameColumn' => 'title',
            'dbIdShortcut' => 'news_'
        );
        $ret['generators']['newsMenu'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_News_Menu_Component',
            'priority' => 3
        );
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Directory/Panel.js';
        $ret['enableExpireDate'] = false;
        $ret['order'] = 'publish_date DESC';
        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';
        return $ret;
    }
    protected function _getNewsComponent()
    {
        return $this->getData();
    }

    protected function _selectNews()
    {
        $select = $this->getData()->getGenerator('detail')->select($this->getData());
        $select->where('publish_date <= NOW()');
        if ($this->_getSetting('enableExpireDate')) {
            $select->where('expiry_date >= NOW() OR ISNULL(expiry_date)');
        }
        $select->order($this->_getSetting('order'));
        return $select;
    }
    public function modifyNewsData(Vps_Component_Data $new)
    {
        foreach (Vpc_Abstract::getChildComponentClasses(get_class($this)) as $c) {
            if (Vpc_Abstract::hasSetting($c, 'hasModifyNewsData')
                && Vpc_Abstract::getSetting($c, 'hasModifyNewsData')) {
                call_user_func(array($c, 'modifyNewsData'), $new);
            }
        }
    }
}
