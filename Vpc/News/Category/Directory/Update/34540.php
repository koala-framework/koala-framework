<?php
class Vpc_News_Category_Directory_Update_34540 extends Vps_Update
{
    public function update()
    {
        $entries = Vps_Registry::get('db')->query('SELECT COUNT(*) FROM vpc_news_to_categories')->fetchColumn();
        if (!$entries) return;

        Vps_Registry::get('db')->query('ALTER TABLE `vpc_news_to_categories` CHANGE `category_id` `category_id` INT( 11 ) NOT NULL DEFAULT \'0\'');
        Vps_Registry::get('db')->query('UPDATE vpc_news_to_categories SET category_id=-category_id');
        $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Pool');
        $s = $m->select()->whereEquals('pool', 'Newskategorien');
        $pool = $m->getRows($s);
        $cats = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_News_Category_Directory_Component', array('ignoreVisible'=>true));
        foreach ($cats as $cat) {
            foreach ($pool as $r) {
                $newRow = Vps_Model_Abstract::getInstance('Vpc_Directories_Category_Directory_CategoriesModel')->createRow();
                $newRow->component_id = $cat->dbId;
                $newRow->pos = $r->pos;
                $newRow->name = $r->value;
                $newRow->visible = $r->visible;
                $newRow->save();
                $sql = "UPDATE vpc_news_to_categories SET category_id=$newRow->id WHERE category_id=-$r->id
                            AND news_id IN (SELECT id FROM vpc_news WHERE component_id='".$cat->parent->dbId."')";
                Vps_Registry::get('db')->query($sql);
            }
        }
        Vps_Registry::get('db')->query("DELETE FROM vps_pools WHERE pool='Newskategorien'");
    }
}
