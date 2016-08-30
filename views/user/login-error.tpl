<div class="kwfUp-content kwfUp-loginError">
    <h1><?=trlKwf('Login Error')?></h1>
    <p>
        <?=$this->errorMessage?>
    </p>
    <?php if ($this->redirect) { ?>
        <p><a href="<?=$this->redirect?>"><?=trlKwf('Continue')?></a></p>
    <?php } ?>
</div>
