<?php

$sql[] = 'ALTER TABLE  `events` ADD  `user_id` INT UNSIGNED NOT NULL DEFAULT  \'0\' AFTER  `id`';

$sql[] = 'ALTER TABLE  `events` ADD  `purse_id` INT UNSIGNED NOT NULL DEFAULT  \'0\'';

$sql[] = 'CREATE TABLE  `users` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ident_hash` VARCHAR( 32 ) NOT NULL ,
`email` VARCHAR( 50 ) NOT NULL ,
`name` VARCHAR( 50 ) NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8';