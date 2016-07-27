<div class="<?=$this->rootElementClass?>">
    <div class="postData">
        <div class="postInfo">
            <?php if (!is_string($this->user)) { ?>
                <div class="avatar">
                    <?= $this->componentLink(
                            $this->user,
                            $this->component($this->user->getChildComponent('-general')
                                ->getChildComponent('-avatar')->getChildComponent('-small'))
                    ) ?>
                </div>
                <div class="user">
                    <?=$this->data->trlKwf('By')?>: <?= $this->componentLink($this->user) ?>
                    <?=$this->component($this->user->getChildComponent('-general')->getChildComponent('-rating'))?>
                </div>
            <?php } ?>
            <?php if ($this->row->name) { ?>
                <div class="user"><?= $this->row->name; ?></div>
            <?php } ?>
            <strong><?=$this->placeholder['prePostNumber']?><?= $this->postNumber ?></strong>
            <em>
                <?=$this->data->trlKwf('on') ?> <?=$this->date($this->row->create_time)?>
                <?=$this->data->trlKwf('at') ?> <?=$this->time($this->row->create_time)?>
            </em><br />
            <?php if ($this->actions) { ?>
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
