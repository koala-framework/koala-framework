<?php
class Kwc_Favourites_Events extends Kwf_Component_Abstract_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentRemove'
        );
        $ret[] = array(
            'class' => $this->_class,
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentAdd'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onComponentRecursiveRemoveOrAdd'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveAdded',
            'callback' => 'onComponentRecursiveRemoveOrAdd'
        );

        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'favouritesModel'),
            'event' => 'Kwf_Events_Event_Row_Deleted',
            'callback' => 'onFavouriteRemove'
        );
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'favouritesModel'),
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onFavouriteInserted'
        );
        return $ret;
    }

    public function onComponentRemove(Kwf_Component_Event_Component_Removed $event)
    {
        $componentId = $event->component->componentId;
        $model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::
                        getSetting($this->_class, 'favouritesModel'));
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Equal('component_id', $componentId));
        $options = array('columns' => array('user_id'));
        $userIds = $model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select, $options);
        if ($userIds) {
            foreach ($userIds as $userId) {
                $componentIds = Kwf_Cache_Simple::fetch('favCIds'.$userId['user_id'], $success);
                if ($success) {
                    Kwf_Cache_Simple::delete('favCIds'.$userId['user_id']);
                    $key = array_search($componentId, $componentIds);
                    unset($componentIds[$key]);
                    Kwf_Cache_Simple::add('favCIds'.$userId['user_id'], $componentIds);
                    $log = Kwf_Events_Log::getInstance();
                    if ($log) {
                        $log->log("favourites cache clear $componentId", Zend_Log::INFO);
                    }
                }
            }
        }
    }

    public function onComponentRecursiveRemoveOrAdd(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $componentId = $event->component->componentId;
        $model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::
                        getSetting($this->_class, 'favouritesModel'));
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Like('component_id', $componentId.'%'));
        $options = array('columns' => array('user_id'));
        $userIds = $model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select, $options);
        if ($userIds) {
            foreach ($userIds as $userId) {
                Kwf_Cache_Simple::delete('favCIds'.$userId['user_id']);
            }
        }
    }

    public function onComponentAdd(Kwf_Component_Event_Component_Added $event)
    {
        $componentId = $event->component->componentId;
        $model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::
                        getSetting($this->_class, 'favouritesModel'));
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Equal('component_id', $componentId));
        $options = array('columns' => array('user_id'));
        $userIds = $model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select, $options);

        if ($userIds) {
            foreach ($userIds as $userId) {
                $componentIds = Kwf_Cache_Simple::fetch('favCIds'.$userId['user_id'], $success);
                if ($success) {
                    Kwf_Cache_Simple::delete('favCIds'.$userId['user_id']);
                    $componentIds[] = $componentId;
                    Kwf_Cache_Simple::add('favCIds'.$userId['user_id'], $componentIds);
                    $log = Kwf_Events_Log::getInstance();
                    if ($log) {
                        $log->log("favourites cache clear $componentId", Zend_Log::INFO);
                    }
                }
            }
        }
    }

    public function onFavouriteInserted(Kwf_Events_Event_Row_Abstract $event)
    {
        $favourite = $event->row;
        $cacheIdUser = 'favCIds'.$favourite->user_id;
        $cacheUser = Kwf_Cache_Simple::fetch($cacheIdUser, $success);
        if ($success) {
            Kwf_Cache_Simple::delete($cacheIdUser);
            $cacheUser[] = $favourite->component_id;
            Kwf_Cache_Simple::add($cacheIdUser, $cacheUser);
        }
    }

    public function onFavouriteRemove(Kwf_Events_Event_Row_Abstract $event)
    {
        $favourite = $event->row;
        $cacheIdUser = 'favCIds'.$favourite->user_id;
        $cacheUser = Kwf_Cache_Simple::fetch($cacheIdUser, $success);
        if ($success) {
            Kwf_Cache_Simple::delete($cacheIdUser);
            $key = array_search($favourite->component_id, $cacheUser);
            unset($cacheUser[$key]);
            Kwf_Cache_Simple::add($cacheIdUser, $cacheUser);
        }
    }
}