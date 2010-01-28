<div class="<?=$this->cssClass?>">
    <?php
        $targetComponent = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->data->row->target_page_id);
    ?>
    <h1><?= $this->componentLink($targetComponent); ?></h1>
    <div>
        <?= $this->ifHasContent($this->text); ?>
            <?= $this->component($this->text); ?>
        <?= $this->ifHasContent(); ?>

        <?= $this->ifHasContent($this->image); ?>
            <?= $this->component($this->image); ?>
        <?= $this->ifHasContent(); ?>
    </div>
    <?= $this->componentLink($targetComponent, $this->row->link_text); ?>
</div>
