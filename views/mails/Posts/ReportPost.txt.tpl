<?= trlVps('Hello'); ?>!

<?= trlVps('A Post at {0} has been reported with the following comment', $this->webUrl); ?>:

<?= $this->content; ?>

<?= trlVps('Click the link'); ?><?= trlVps('to go directly to the post or read it below'); ?>:
<?= $this->webUrl.$this->postUrl; ?>

<?= nl2br($this->postContent); ?>


<?= $this->applicationName; ?>

--
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
