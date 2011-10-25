<?php
class Kwc_Posts_Latest_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Posts.Last Posts');
        $ret['childModel'] = 'Kwc_Posts_Directory_Model';
        $ret['numberOfPosts'] = 9;
        return $ret;
    }

    protected function _getSelect()
    {
        $select = new Kwf_Model_Select();
        $select
            ->whereEquals('visible', 1)
            ->order('create_time', 'DESC')
            ->limit($this->_getSetting('numberOfPosts'));
        return $select;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['posts'] = array();
        $rows = $this->getChildModel()->fetchAll($this->_getSelect());
        foreach ($rows as $row) {
            $id = $row->component_id . '-' . $row->id;
            $post = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id);
            if ($post) {
                $dateHelper = new Kwf_View_Helper_Date();
                $linktexts = array();
                $page = $post->getPage();
                while ($page) {
                    $linktexts[] = $page->name;
                    $page = $page->getParentPage();
                }
                $post->linktext =
                    $dateHelper->date($post->row->create_time) .
                    ': ' .
                    implode(' &raquo; ', array_reverse($linktexts));
                $ret['posts'][] = $post;
            }
        }
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwc_Posts_Directory_Model');
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model(Kwf_Registry::get('config')->user->model);
        return $ret;
    }
}
