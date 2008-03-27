<?php
class Vpc_Posts_Component extends Vpc_Abstract
{
    private $_posts;
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => 'Posts',
            'componentIcon'     => new Vps_Asset('comments'),
            'tablename'         => 'Vpc_Posts_Model',
            'hideInNews'        => true,
            'childComponentClasses' => array(
                'write' => 'Vpc_Posts_Write_Component',
                'post' =>  'Vpc_Posts_Post_Component'
            ),
            'loginDecorator' => 'Vpc_Decorator_CheckLogin_Component'
        ));
        return $ret;
    }

    public function getChildComponents($orderOut = 'asc')
    {
        if (!isset($this->_posts)) {
            $this->_posts = array();
            $where = array(
                'component_id = ?' => $this->getDbId(),
                'visible = 1'
            );
            $order = 'id ASC';
            $classes = $this->_getSetting('childComponentClasses');
            foreach ($this->getTable()->fetchAll($where, $order) as $row) {
                $c = $this->createComponent($classes['post'], $row->id);
                $this->_posts[] = $c;
                $c->setPostNum(count($this->_posts));
            }
        }
        if ($orderOut == 'desc') {
            $posts = $this->_posts;
            if ($posts) {
                krsort($posts);
                return array_values($posts);
            }
        }

        return $this->_posts;
    }

    public function getChildComponentByRow($row)
    {
        $classes = $this->_getSetting('childComponentClasses');
        return $this->createComponent($classes['post'], $row->id);
    }

    public function getTemplateVars()
    {
        if (!(Zend_Registry::get('userModel')->getAllCache())) {
            Zend_Registry::get('userModel')->createAllCache();
        }

        $ret = parent::getTemplateVars();
        $ret['posts'] = array();
        foreach ($this->getChildComponents() as $c) {
            $ret['posts'][] = $c->getTemplateVars();
        }

        $ret['writeUrl'] = $this->getPageFactory()->getChildPageById('write')->getUrl();
        return $ret;
    }

}
