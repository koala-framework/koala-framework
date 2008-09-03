<?php
class Vpc_User_Detail_GeneralCommunity_Component extends Vpc_User_Detail_General_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['avatar'] = 'Vpc_User_Detail_GeneralCommunity_Avatar_Component';
        $ret['generators']['child']['component']['rating'] = 'Vpc_User_Detail_GeneralCommunity_Rating_Component';
        $ret['numberOfLastPosts'] = 9;
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['activitiesCount'] = 0;
        $ret['lastPosts'] = array();
        $ret['showLastPosts'] = $this->_getSetting('numberOfLastPosts') > 0;

        $table = new Vpc_Posts_Directory_Model();
        $select = $table->select()
            ->where('visible = ?', 1)
            ->where('user_id = ?', $this->getData()->parent->row->id)
            ->order('create_time', 'DESC');
        $ret['postsCount'] = count($select->query()->fetchAll());
        $select->limit($this->_getSetting('numberOfLastPosts'));
        foreach ($select->query()->fetchAll() as $row) {
            $id = $row['component_id'] . '-' . $row['id'];
            $post = Vps_Component_Data_Root::getInstance()->getComponentById($id);
            if ($post) {
                $dateHelper = new Vps_View_Helper_Date();
                $post->linktext =
                    $dateHelper->date($post->row->create_time) . ': ' . 
                    $post->getParentPage()->getParentPage()->name . ': ' .
                    $post->getPage()->name;
                $ret['lastPosts'][] = $post;
            }
        }
        return $ret;
    }
}
