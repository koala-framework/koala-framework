<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Move Thread') ?></h1>

    <?php if ($this->moved) { ?>
        <?= $this->component($this->success) ?>
    <?php } else { ?>
        <h3><?= $this->threadTitle ?></h3>
        <p>Bitte klicken Sie auf die Gruppe, in die das Thema verschoben werden soll:</p>

        <?= $this->partial($this->groupsTemplate, array('data' => $this->data, 'groups' => $this->groups, 'groupsTemplate' => $this->groupsTemplate)) ?>
    <?php } ?>
</div>
