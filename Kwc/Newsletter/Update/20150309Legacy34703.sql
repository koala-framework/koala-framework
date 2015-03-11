UPDATE kwc_mail_redirect SET value=CONCAT(SUBSTRING(value, 1, LENGTH(value)-12), '_unsubscribe') WHERE type='showcomponent' AND value LIKE '%-unsubscribe';
UPDATE kwc_mail_redirect SET value=CONCAT(SUBSTRING(value, 1, LENGTH(value)-15), '_editSubscriber') WHERE type='showcomponent' AND value LIKE '%-editSubscriber';
