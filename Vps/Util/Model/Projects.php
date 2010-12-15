<?php
class Vps_Util_Model_Projects extends Vps_Model_Service
{
    protected $_serverConfig = 'projects';

    public function getApplicationProjectIds()
    {
        $projectIds = array();
        if (Vps_Registry::get('config')->todo->projectIds) {
            $projectIds = Vps_Registry::get('config')->todo->projectIds->toArray();
        }
        $s = new Vps_Model_Select();
        $s->whereEquals('application_id', Vps_Registry::get('config')->application->id);
        foreach ($this->getRows($s) as $project) {
            $projectIds[] = $project->id;
        }
        return $projectIds;
    }
}
