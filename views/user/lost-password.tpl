<div class="kwfUp-content">
    <h1><?=trlKwf('Lost password')?></h1>
    <p><?=trlKwf('Please enter your email address')?></p>
    <?=$this->errorsHtml?>
    <form action="<?= htmlspecialchars($this->action) ?>" method="<?=$this->method?>">
        <?php $this->formField($this->form) ?>
        <button class="submit" type="submit" name="<?= $this->formName ?>" value="submit"><?=trlKwf('Submit')?></button>
    </form>
</div>
