<div class="<?=$this->cssClass?>">
<?php
if ($this->showSuccess) {
    $this->component($this->success);
} else {
    if ($this->errors) {
        echo 'Fehler:';
        echo '<ul class="error">';
        foreach ($this->errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul>';
    }
    ?>
    <form action="<?= $this->action ?>" method="POST">
        <?php $this->formField($this->form) ?>
        <button type="submit" name="<?= $this->formName ?>" value="submit"><?= $this->placeholder['submitButton'] ?></button>
    </form>
    <?php
}
?>
</div>