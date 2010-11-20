<?php
/* Скрипт обновления базы данных с версии 1.0.0 (и старше) до 1.0.27 (и старше)
 */

require_once 'app/init.php';

$sql[] = 'ALTER TABLE  `events` ADD  `user_id` INT UNSIGNED NOT NULL DEFAULT  \'0\' AFTER  `id`';

$sql[] = 'ALTER TABLE  `events` ADD  `purse_id` INT UNSIGNED NOT NULL DEFAULT  \'0\'';

$sql[] = 'ALTER TABLE  `ev2tag` ADD  `user_id` INT UNSIGNED NOT NULL DEFAULT  \'0\'';

$sql[] = 'CREATE TABLE  `users` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ident_hash` VARCHAR( 32 ) NOT NULL ,
`email` VARCHAR( 50 ) NOT NULL ,
`name` VARCHAR( 50 ) NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8';
?>
<html><head>
        <title>Обновления базы данных с версии 1.0.0 (и старше) до 1.0.27 (и старше)</title>
    </head>
    <body>
        <h1>Обновления базы данных с версии 1.0.0 (и старше) до 1.0.27 (и старше)</h1>
<?php
$error = 0;
foreach ($sql as $query) {
    if (!Db::justQuery($query)) {
        $error = 1;
        break;
    }
}

if (!$error)
    echo "Обновление прошло успешно";
else
    echo "Ошибка!<br>" . Db::lastError();

?>
    </body>
</html>
