<?php
class Vpc_Advanced_DownloadsTree_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Downloads');
        $ret['componentIcon'] = new Vps_Asset('application_side_tree');

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Advanced/DownloadsTree/AdminPanel.js';
        $ret['assets']['files'][] = 'vps/Vpc/Advanced/DownloadsTree/Component.js';
        $ret['assets']['dep'][] = 'VpsAutoTree';
        $ret['assets']['dep'][] = 'VpsAutoGrid';
        $ret['assets']['dep'][] = 'ExtBorderLayout';
        $ret['assets']['dep'][] = 'ExtDate';
        $ret['projectsModel'] = 'Vpc_Advanced_DownloadsTree_Projects';
        $ret['downloadsModel'] = 'Vpc_Advanced_DownloadsTree_Downloads';

        $ret['plugins'] = array('Vpc_Rotary_Password_Component');
        $ret['flags']['hasResources'] = true;

        $ret['panelWidth'] = 490;
        $ret['panelHeight'] = 500;

        $ret['extConfig'] = 'Vpc_Advanced_DownloadsTree_ExtConfig';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['options'] = $this->_getOptions();
        return $ret;
    }

    protected function _getOptions()
    {
        $ret['componentId'] = $this->getData()->componentId;
        $ret['projectsUrl'] = Vpc_Admin::getInstance(get_class($this))->getControllerUrl('ViewProjects');
        $ret['downloadsUrl'] = Vpc_Admin::getInstance(get_class($this))->getControllerUrl('ViewDownloads');
        $ret['width'] = $this->_getSetting('panelWidth');
        $ret['height'] = $this->_getSetting('panelHeight');
        if (!Vps_Controller_Front::getInstance() instanceof Vps_Controller_Front_Component)
            throw new Vps_Exception('Front Controller must be instance of Vps_Controller_Front_Component');
        return $ret;
    }

    public function hasContent()
    {
        $projects = Vps_Model_Abstract::getInstance($this->_getSetting('projectsModel'));
        $select = $projects->select()
            ->whereEquals('component_id', $this->getData()->componentId);
        $downloads = Vps_Model_Abstract::getInstance($this->_getSetting('downloadsModel'));
        foreach ($projects->getRows($select) as $project) {
            $select = $downloads->select()
                ->whereEquals('visible' , 1)
                ->whereEquals('project_id', $project->id);
            if ($downloads->countRows($select) > 0) return true;
        }
        return false;
    }
}
