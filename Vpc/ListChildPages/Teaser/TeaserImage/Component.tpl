<div class="<?=$this->cssClass?>">
    <?
        $targetComponent = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->data->row->target_page_id);
    ?>
    <h2><?= $this->componentLink($targetComponent); ?></h2>
    <?= $this->ifHasContent($this->image); ?>
        <div class="left prevImg">
            <?= $this->componentLink($targetComponent, $this->component($this->image)); ?>
        </div>
    <?= $this->ifHasContent(); ?>
    <div class="text<?= $this->ifHasContent($this->image); ?> withImage<?= $this->ifHasContent(); ?>">
        <?= $this->component($this->text); ?>
    </div>
    <div class="clear"></div>
    <p class="goTo"><?= $this->componentLink($targetComponent, $this->row->link_text); ?></p>
</div>
