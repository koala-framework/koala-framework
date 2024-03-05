<div class="kwfUp-content kwfUp-loginError">
    <h1><?=trlKwf('Login Error')?></h1>
    <p>
        <?=Kwf_Util_HtmlSpecialChars::filter($this->errorMessage)?>
    </p>
    <?php if ($this->redirect) { ?>
        <p><a href="<?=Kwf_Util_HtmlSpecialChars::filter($this->redirect)?>"><?=trlKwf('Continue')?></a></p>
    <?php } ?>
</div>
