<?php
class Kwc_News_Category_Directory_Update_20150309Legacy34540 extends Kwf_Update
{
    public function update()
    {
        $entries = Kwf_Registry::get('db')->query('SELECT COUNT(*) FROM kwc_news_to_categories')->fetchColumn();
        if (!$entries) return;

        Kwf_Registry::get('db')->query('ALTER TABLE `kwc_news_to_categories` CHANGE `category_id` `category_id` INT( 11 ) NOT NULL DEFAULT \'0\'');
        Kwf_Registry::get('db')->query('UPDATE kwc_news_to_categories SET category_id=-category_id');
        $m = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Pool');
        $s = $m->select()->whereEquals('pool', 'Newskategorien');
        $pool = $m->getRows($s);
        $cats = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_News_Category_Directory_Component', array('ignoreVisible'=>true));
        foreach ($cats as $cat) {
            foreach ($pool as $r) {
                $newRow = Kwf_Model_Abstract::getInstance('Kwc_Directories_Category_Directory_CategoriesModel')->createRow();
                $newRow->component_id = $cat->dbId;
                $newRow->pos = $r->pos;
                $newRow->name = $r->value;
                $newRow->visible = $r->visible;
                $newRow->save();
                $sql = "UPDATE kwc_news_to_categories SET category_id=$newRow->id WHERE category_id=-$r->id
                            AND news_id IN (SELECT id FROM kwc_news WHERE component_id='".$cat->parent->dbId."')";
                Kwf_Registry::get('db')->query($sql);
            }
        }
        Kwf_Registry::get('db')->query("DELETE FROM kwf_pools WHERE pool='Newskategorien'");
    }
}
