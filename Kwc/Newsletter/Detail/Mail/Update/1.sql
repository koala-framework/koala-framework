set mail_component_id=replace(mail_component_id, '-mail', '_mail')
where mail_component_id like '%-mail';
