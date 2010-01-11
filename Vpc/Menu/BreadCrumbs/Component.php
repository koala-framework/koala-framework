<?php
class Vpc_Menu_BreadCrumbs_Component extends Vpc_Menu_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['separator'] = '»';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['separator'] = $this->_getSetting('separator');
        $ret['links'] = array();
        $page = $this->getData();
        do {
            $ret['links'][] = $page;
        } while ($page = $page->getParentPage());
        $ret['links'] = array_reverse($ret['links']);
        return $ret;
    }

    public static function getStaticCacheVars()
    {
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
            foreach (Vpc_Abstract::getSetting($componentClass, 'generators') as $key => $generator) {
                if (!is_instance_of($generator['class'], 'Vps_Component_Generator_PseudoPage_Table') &&
                    !is_instance_of($generator['class'], 'Vps_Component_Generator_Page')
                ) continue;
                $generator = current(Vps_Component_Generator_Abstract::getInstances(
                    $componentClass, array('generator' => $key))
                );
                $model = $generator->getModel();
                if ($model instanceof Vps_Model_Db) $model = $model->getTable();
                $ret[] = array(
                    'model' => get_class($model)
                );
            }
        }
        $ret[] = array(
            'model' => 'Vps_Component_Model'
        );
        $ret[] = array(
            'model' => 'Vps_Component_PagesModel'
        );
        return $ret;
    }
}
