<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Move Thread') ?></h1>

    <?php if ($this->moved) { ?>
        <?= $this->component($this->success) ?>
    <?php } else { ?>
        <p><?=trlVps('Move Thread') ?>:</p><h3><?= $this->threadTitle ?></h3>
        <p><?=trlVps('Please choose a group, in which the thread should be moved') ?>:</p>
        <?= $this->partial($this->groupsTemplate, array('data' => $this->data, 'groups' => $this->groups, 'groupsTemplate' => $this->groupsTemplate)) ?>
    <?php } ?>
</div>
