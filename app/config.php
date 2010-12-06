<?php
/* Добро пожаловать в конфигурационный файл!
 * Здесь нужно указать настройки базы данных,
 * всё остальное можно оставить по умолчанию
 */

function get_config( $name ) {

    /**\
     * Настройки базы данных
    \**/

    /* Если правильно указать db_host, db_user и db_password,
     * но не создавать базу и/или таблицы,
     * то они будут созданы автоматически при первом запуске.
     *
     * Но имейте в виду что у пользователя, имя и пароль
     * которого вы указали должны быть соотвествующие права.
     */
    $config['db_host'] = 'localhost'; // имя сервера (хост) базы данных
    $config['db_user'] = 'root'; // имя пользователя
    $config['db_password'] = '123'; // пароль
    $config['db_db_name'] = 'myfin'; // название базы данных

    /* Можно поменять названия таблиц.
     * На случай если в указанной базе уже есть таблицы
     * с такими названиями или еще что ...
     */
    $config['db_table']['events'] = 'events';
    $config['db_table']['tags'] = 'tags';
    $config['db_table']['ev2tag'] = 'ev2tag';
    $config['db_table']['users'] = 'users';



    /**\
     * Авторизация
    \**/

    /* Http-авторизация. Если не нужна - оставить поле с логином пустым
     */
    $config['auth_login'] = ''; // логин
    $config['auth_password'] = ''; // пароль

    /* Поддержка авторизации по Open Id
     * (Если будет 1 пользователь то это не нужно, если несколько то надо включить)
     * 1 - включенно
     * 0 - выключенно
     */
    $config['use_openid'] = 0;



    /**\
     * Внешний вид
    \**/

    /* Если на странице должно быть отображенно большое количество
     * записей (больше N), то будут отображены только первые N,
     * и внизу будет ссылка "показать все".
     */
    $config['items_on_page'] = 50; // количество записей на странице (то самое N)

    /* Дни которые отображаются в критериях выборки по времени
     * "за последние [тут идут эти дни] дней"
     */
    $config['days4select'] = array(1, 7, 30, 42, 61, 365, 730);



    /**\
     * Прочее
    \**/

    // Ваш часовой пояс
    // Формат: http://www.php.net/manual/en/timezones.php
    $config['timezone'] = 'Europe/Moscow';

    /* Включить / выключить отладочный вывод (и более подробные сообщения об ошибках)
     * 1 - вкл, 0 - выкл
     */
    $config['debug'] = 1;



    /**\
     * Ниже ничего не трогайте :)
    \**/

    return $config[ $name ];
}

function tn( $name ) {
    $tmp = get_config( 'db_table' );
    return $tmp[$name];
}
