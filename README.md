WorkingForumBundle
==================

[![Build Status](https://travis-ci.org/Yosimitso/WorkingForumBundle.svg?branch=master)](https://travis-ci.org/Yosimitso/WorkingForumBundle) [![Latest Stable Version](https://poser.pugx.org/yosimitso/workingforumbundle/v/stable)](https://packagist.org/packages/yosimitso/workingforumbundle) [![Total Downloads](https://poser.pugx.org/yosimitso/workingforumbundle/downloads)](https://packagist.org/packages/yosimitso/workingforumbundle) [![License](https://poser.pugx.org/yosimitso/workingforumbundle/license)](https://packagist.org/packages/yosimitso/workingforumbundle)


ENGLISH
=================  
A forum bundle for Symfony 4/5, easy to use with a lot of features  
This bundle work with your user bundle with no extra configuration (which can extend FOSUserBundle)  
The bundle was made to be customizable and overridable to fit your application  

Demo
-------------
Try it here - https://demoworkingforum.charlymartins.fr

Chat and support
---------------
[Join the Discord](https://discord.gg/EG7C54PgWR)

Features
------------------
- Support multi language (currently provided : english, french)
- Responsive design (mobile, tablet, desktop)
- Post editor using markdown with smiley, quote and instant preview
- Threads status : resolved, closed, pinned, moved from a moderator
- Enclosed files with post (files upload system)
- Vote system for posts
- Moderator role as ROLE_MODERATOR (and default admin roles)
- Reporting system for thread
- Auto-lock system for old thread
- Automatic breadcrumb, messages counters, pagination
- Allow or not the anonymous to read forums
- Database safety : no HTML stored, only markdown
- Search system
- Backend administration
- Antiflood system
- Email notification on new posts


Setup
------------------
See SETUP.md


Configuration
-----------------
Refer to CONFIGURATION.md

Contribute
----------------
About a fix : 
Make a PR !

About a feature :
please open an issue, to talk about it and share the work

Tests
--------------------
Before opening a pull request, run tests :

Go to the bundle's directory and execute 
````
composer install
````

**Unit tests**
````
vendor/phpunit/phpunit/phpunit --testsuite=unit
````

**Functionnal tests**

- create an empty database (all data will be erased when you run tests)
- set its credentials into phpunit.xml
- import the database structure (Tests/Scenario/empty_db.sql)
- run 
````
vendor/phpunit/phpunit/phpunit --testsuite=scenario
````



