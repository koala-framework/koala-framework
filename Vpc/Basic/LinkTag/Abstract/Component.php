<?php
abstract class Vpc_Basic_LinkTag_Abstract_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentIcon' => new Vps_Asset('page_link')
        ));
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $parent = $this->getData()->parent;
        if ($parent->getComponent() instanceof Vpc_Basic_LinkTag_Component) {
            $model = $parent->getComponent()->getModel();
            $row = $model->getRow($parent->dbId);
            $ret[] = array(
                'model' => get_class($model),
                'id' => $row->component_id
            );
        }

        return $ret;
    }

}
