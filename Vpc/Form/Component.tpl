<div class="<?=$this->cssClass?>">
<?php
if ($this->showSuccess) {
    echo $this->component($this->success);
} else {
    if ($this->errors) {
        echo $this->placeholder['error'].':';
        echo '<ul class="error">';
        foreach ($this->errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul>';
    }
    ?>
    <form action="<?= $this->action ?>" method="<?=$this->method?>"<? if($this->isUpload) { ?> enctype="multipart/form-data"<? } ?>>
        <?php $this->formField($this->form) ?>
        <button type="submit" name="<?= $this->formName ?>" value="submit"><?= $this->placeholder['submitButton'] ?></button>
    </form>
    <?php
}
?>
</div>