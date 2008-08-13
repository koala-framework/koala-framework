<?php
class Vpc_User_Detail_Community_Component extends Vpc_User_Detail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Community');
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['activitiesCount'] = 0;
        
        $table = new Vpc_Posts_Directory_Model();
        $select = $table->select()
            ->where('visible = ?', 1)
            ->where('user_id = ?', Zend_Registry::get('userModel')->getAuthedUser()->id)
            ->order('create_time DESC');
        $ret['postsCount'] = count($select->query()->fetchAll());
        $select->limit(5);
        foreach ($select->query()->fetchAll() as $row) {
            $id = $row['component_id'] . '-' . $row['id'];
            $post = Vps_Component_Data_Root::getInstance()->getComponentById($id);
            if ($post) {
                $dateHelper = new Vps_View_Helper_Date();
                $post->linktext =
                    $dateHelper->date($post->row->create_time) . ': ' . 
                    Vpc_Abstract::getSetting($post->parent->componentClass, 'name') . ': ' . 
                    $post->getPage()->name;
                $ret['lastPosts'][] = $post;
            }
        }
        return $ret;
    }
}
