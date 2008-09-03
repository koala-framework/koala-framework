<?= trlVps("Hello")?>,

<?=trlVps("a post has been reported for the following reason:") ?>

-------------------
<?= $this->reason ?>

-------------------

<?= $this->url ?>

<?= trlVps("Open this url to go to the post or read it here:") ?>

-------------------
<?= $this->content ?>

-------------------

