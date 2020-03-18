# ci4_menu
Gestion des menus avec Codeigniter 4

# Installation du module

<pre>
    composer require spreadaurora/ci4_menu
    or 
    /opt/plesk/php/7.xx/bin/php /usr/lib/plesk-9.0/composer.phar require spreadaurora/ci4_menu
    
</pre>
<pre>
    php spark migrate -all
    or
    /opt/plesk/php/7.xx/bin/php spark migrate -all

    php spark db:seed \\Spreadaurora\\ci4_menu\\Database\\Seeds\\MenuSeeder
    or
    /opt/plesk/php/7.xx/bin/php spark db:seed \\Spreadaurora\\ci4_menu\\Database\\Seeds\\MenuSeeder


    php spark ci4_menu:publish
    or
    /opt/plesk/php/7.xx/bin/php spark ci4_menu:publish
    </pre>