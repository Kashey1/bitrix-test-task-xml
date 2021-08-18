# bitrix-test-task-xml
Bitrix test task XML

<b>Команда для запуска в терминале:</b><br>
/opt/php73/bin/php /var/www/www-root/data/www/morizo.volochay.ru/local/php_interface/cli/readXML.php xml=/local/php_interface/cli/eowiki-20210720-pages-articles.xml item=page <br>
Где xml -> путь к файлу XML, а item -> название узла в XML файле, который принимать за элементы инфоблока.

Файл eowiki-20210720-pages-articles.xml не загружал в репозиторий из за его размеров.

<b>Лог пишется в /local/php_interface/log.txt.</b>
