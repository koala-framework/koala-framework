<?php
class Vps_Dao_Rowset_TreeCache extends Vps_Db_Table_Rowset
{
    public function toMenuData($currentIds = array())
    {
        $return = array();
        foreach ($this as $i => $row) {
            $class = '';
            if ($i == 0) $class .= ' first';
            if ($i == sizeof($this)-1) $class .= ' last';
            $isCurrent = in_array($row->component_id, $currentIds);
            if ($isCurrent) $class .= ' current';
            $data = $row->toArray();
            $data['componentId']  = $row->component_id;
            $data['text']         = $row->name;
            $data['current']      = $isCurrent;
            $data['class']        = trim($class);
            if (!$this->getTable()->showInvisible()) {
                $data['href'] = $row->url;
                $data['rel'] = $row->rel;
            } else {
                $data['href'] = $row->url_preview;
                $data['rel'] = $row->rel_preview;
            }
            $return[] = $data;
        }
        return $return;
    }
}
