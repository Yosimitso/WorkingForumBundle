Common errors :

*The service "yosimitso_workingforum_base_controller" has a dependency on a non-existent service "templating"*

Sf 3 : add to app/config/config.yml :

Sf 4 : add to config/framework.yaml :

You have to define your templating engine to Symfony, ex for Twig :
````yaml
framework:
    templating:
        engines: ['twig']
````
--------------------------

*The service "yosimitso_workingforum_util_subscription" has a dependency on a non-existent parameter "swiftmailer.sender_address". Did you mean this: "swiftmailer.single_address"?*

Sf 3 : add to app/config/config.yml :

Sf 4 : add to config/swiftmailer.yaml :
````yaml
swiftmailer:
    sender_address: 'youremail@yourdomain.com'
````
--------------------------------
*SQLSTATE[42000]: Syntax error or access violation : 1071 Specified key was too long; max key length is 767 bytes*

This error occurs on old versions of MySQL (< 5.6)

- upgrade MySQL

or :
- dump the SQL queries with php bin/console doctrine:schema:update --dump-sql
- lower keys length of the concerned table
- execute SQL queries