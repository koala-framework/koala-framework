<h1><?=trlKwf('Login')?></h1>
<?=$this->errorsHtml?>
<form action="<?= htmlspecialchars($this->action) ?>" method="<?=$this->method?>">
    <?php $this->formField($this->form) ?>
    <button class="submit" type="submit" name="<?= $this->formName ?>" value="submit"><?=trlKwf('Submit')?></button>
</form>
<p>
    <a href="<?=$this->lostPasswordLink?>"><?=trlKwf('Lost password?')?></a>
</p>
