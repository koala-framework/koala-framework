<div class="<?=$this->rootElementClass?>">
    <div class="postData">
        <div class="postInfo">
            <?php if ($this->avatar) { ?>
                <div class="avatar">
                    <?php if ($this->avatar instanceof Kwf_Component_Data) { ?>
                        <?= $this->componentLink(
                                $this->user,
                                $this->component($this->avatar)
                        ) ?>
                    <?php } else { ?>
                        <img src="<?=$this->avatar?>" width="68" height="68" alt="" />
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if ($this->user) { ?>
                <div class="user">
                    <?php if (is_string($this->user)) { ?>
                        <?=$this->data->trlKwf('By')?>: <?=$this->user?>
                    <?php } else { ?>
                        <?=$this->data->trlKwf('By')?>: <?= $this->componentLink($this->user) ?>
                        <?=$this->component($this->user->getChildComponent('-general')->getChildComponent('-rating'))?>
                    <?php } ?>
                </div>
            <?php } ?>
            <strong>#<?= $this->postNumber ?></strong>
            <em>
                <?=$this->data->trlKwf('on') ?> <?=$this->date($this->row->create_time)?>
                <?=$this->data->trlKwf('at') ?> <?=$this->time($this->row->create_time)?>
            </em><br />
            <?php if ($this->actions->hasContent()) { ?>
                <?=$this->data->trlKwf('Post')?>:
                <?= $this->component($this->actions) ?>
            <?php } ?>
        </div>
        <div class="text">
            <?=$this->content?>
        </div>
        <?php if (isset($this->signature)) { ?>
            <?=$this->component($this->signature)?>
        <?php } ?>
    </div>
    <div class="kwfUp-clear"></div>
</div>
