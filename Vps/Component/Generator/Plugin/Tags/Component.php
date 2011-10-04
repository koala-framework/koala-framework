<?php
class Vps_Component_Generator_Plugin_Tags_Component extends Vps_Component_Generator_Plugin_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Tags');
        $ret['assetsAdmin']['dep'][] = 'VpsAutoAssignGrid';
        $ret['childModel'] = 'Vps_Component_Generator_Plugin_Tags_ComponentsToTagsModel';

        $ret['extConfig'] = 'Vps_Component_Generator_Plugin_Tags_ExtConfig';
        return $ret;
    }

    public function getTags(Vps_Component_Data $data)
    {
        if ($data->generator != $this->_generator) {
            throw new Vps_Exception("invalid data, must be from same generator as the generator plugin");
        }
        $m = self::createChildModel(get_class($this));
        $s = $m->select()
            ->whereEquals('component_id', $data->dbId);
        $ret = array();
        foreach ($m->getRows($s) as $row) {
            $ret[] = $row->getParentRow('Tag');
        }
        return $ret;
    }

    public function getComponentsWithSameTags(Vps_Component_Data $data)
    {
        $ret = array();
        $ids = array();
        foreach ($this->getTags($data) as $tag) {
            foreach ($tag->getChildRows('ComponentsToTags') as $row) {
                $d = Vps_Component_Data_Root::getInstance()
                    ->getComponentByDbId($row->component_id);
                if ($d && $d->componentId != $data->componentId && !in_array($d->componentId, $ids)) {
                    $ids[] = $d->componentId;
                    $ret[] = $d;
                }
            }
        }
        return $ret;
    }
}
