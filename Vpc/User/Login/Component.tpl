<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Please login')?></h1>
    <p><?=trlVps('You have to login to see the requested page.')?></p>
    <? if ($this->register) { ?>
        <p><?=trlVps("If you don't have an account, you can")?>
        <?=$this->componentLink($this->register, trlVps('register here'))?>.
        </p>
    <? } ?>
    <?=$this->component($this->form)?>
</div>