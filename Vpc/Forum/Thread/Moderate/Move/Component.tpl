<div class="<?=$this->cssClass?>">
    <?php if ($this->moved) { ?>
        <?= $this->component($this->success) ?>
    <?php } else { ?>
        <p><?=trlVps('Move Thread') ?>: <strong><?= $this->threadTitle ?></strong></p>
        <p><?=trlVps('Please choose a group, in which the thread should be moved') ?>:</p>
        <?= $this->partial($this->groupsTemplate, array('data' => $this->data, 'groups' => $this->groups, 'groupsTemplate' => $this->groupsTemplate)) ?>
    <?php } ?>
</div>
