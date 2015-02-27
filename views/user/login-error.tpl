<div class="content loginError">
    <h1><?=trlKwf('Login Error')?></h1>
    <p>
        <?=$this->errorMessage?>
    </p>
    <? if ($this->redirect) { ?>
        <p><a href="<?=$this->redirect?>"><?=trlKwf('Continue')?></a></p>
    <? } ?>
</div>
