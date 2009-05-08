<?php
class Vpc_Advanced_DownloadsTree_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trl('Downloads');
        $ret['componentIcon'] = new Vps_Asset('application_side_tree');

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Advanced/DownloadsTree/AdminPanel.js';
        $ret['assets']['files'][] = 'vps/Vpc/Advanced/DownloadsTree/Component.js';
        $ret['assets']['dep'][] = 'VpsAutoTree';
        $ret['assets']['dep'][] = 'VpsAutoGrid';
        $ret['assets']['dep'][] = 'ExtBorderLayout';
        $ret['assets']['dep'][] = 'ExtDate';

        $ret['plugins'] = array('Vpc_Rotary_Password_Component');
        $ret['flags']['hasResources'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['projectsClass'] = Vpc_Admin::getComponentClass(get_class($this), 'ViewProjectsController');
        $ret['downloadsClass'] = Vpc_Admin::getComponentClass(get_class($this), 'ViewDownloadsController');
        $ret['projectsClass'] = str_replace('Controller', '', $ret['projectsClass']);
        $ret['downloadsClass'] = str_replace('Controller', '', $ret['downloadsClass']);
        return $ret;
    }

    public function hasContent()
    {
        $projects = new Vpc_Advanced_DownloadsTree_Projects();
        $select = $projects->select()
            ->whereEquals('component_id', $this->getData()->componentId);
        $downloads = new Vpc_Advanced_DownloadsTree_Downloads();
        foreach ($projects->getRows($select) as $project) {
            $select = $downloads->select()
                ->whereEquals('visible' , 1)
                ->whereEquals('project_id', $project->id);
            if ($downloads->countRows($select) > 0) return true;
        }
        return false;
    }
}
