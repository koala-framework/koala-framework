<?= trlVps('Hello {0}!', $this->fullname); ?><br /><br />

<?= trlVps('A new Post has been written in thread:'); ?> <a href="<?= $this->webUrl.$this->threadUrl; ?>"><?= $this->threadName; ?></a><br />
<?= trlVps('in the forum of:'); ?> <a href="<?= $this->webUrl; ?>"><?= $this->webUrl; ?></a><br /><br />

<?= trlVps('Click the following link to go directly to the mentioned thread:'); ?><br />
<a href="<?= $this->webUrl.$this->threadUrl; ?>"><?= $this->threadName; ?></a><br /><br />

<?= $this->applicationName; ?><br /><br />

--<br />
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
