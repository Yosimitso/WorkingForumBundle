WorkingForumBundle
==================

ENGLISH
=================
A forum bundle for Symfony 2 (>= 2.7) and Symfony 3, easy to use and fully fonctionnal
This bundle work with your user bundle with no extra configuration (which can extend FOSUserBundle)

Demo
-------------
Try it here - http://demo.charlymartins.fr/demoworkingforum/


Features
------------------
- Forum with subforum
- Moderator role as ROLE_MODERATOR (and default admin roles)
- Post editor using markdown with instant preview
- Smiley system
- Enable thread locking
- Thread can be pinned
- Thread can be moved
- Quoting system
- Reporting system for thread
- Support multi language
- Enable marking thread as 'resolved'
- Auto-lock system for old thread
- Automatic breadcrumb
- Messages counting (user, forum, subforum) with last replies
- Automatic pagination on thread list and thread
- Allow or not the anonymous to read forums
- Database safety : no HTML stored, only markdown
- Search system
- Backend administration


Setup
------------------
This bundle use KnpPaginatorBundle for pagination, KnpMarkdown for markdown rendering and the extra package for Symfony
Add to your composer.json, section 'require'
````json
"require" : {
 [...]
 "yosimitso/workingforumbundle" : "~1.0",
 "knplabs/knp-paginator-bundle": "~2.5",
 "knplabs/knp-markdown-bundle": "~1.5",
 "sensio/framework-extra-bundle": "~3.0"
 }
````


Register the bundles in your AppKernel
````php
  new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
  new Yosimitso\WorkingForumBundle\YosimitsoWorkingForumBundle(),
  new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
  new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle()
````
Add to your app/config.yml

````yml
yosimitso_working_forum:
    thread_per_page: 50
    post_per_page: 10
    date_format: 'Y/m/d H:i:s'
    allow_anonymous_read: false
    allow_moderator_delete_thread: false
    theme_color: green
    lock_thread_older_than: 0
knp_paginator:
    page_range: 1                      # default page range used in pagination control
    default_options:
        page_name: page                # page query parameter name
        sort_field_name: sort          # sort field query parameter name
        sort_direction_name: direction # sort direction query parameter name
        distinct: true                 # ensure distinct results, useful when ORM queries are using GROUP BY statements
    template:
        pagination: YosimitsoWorkingForumBundle:Common:slidePagination.html.twig     # sliding pagination controls template
        sortable: KnpPaginatorBundle:Pagination:sortable_link.html.twig # sort link template
````
Add to you app/config.yml into 'orm' key :
````yml
resolve_target_entities:
    Yosimitso\WorkingForumBundle\Entity\User: You\YourUserBundle\Entity\YourUser
````
You can override the translations files

Your User Entity needs to extends : \Yosimitso\WorkingForumBundle\Entity\User
Example :
````php
   class User extends \Yosimitso\WorkingForumBundle\Entity\User
{
    // YOUR ENTITY
}
````
In case your user entity already extends an another bundle (like FOSUserBundle), implement the interface \Yosimitso\WorkingForumBundle\Entity\UserInterface
in your user entity. Then copy/paste the content of \Yosimitso\WorkingForumBundle\Entity\User (attributes, getter, setter) into your user entity



FRANCAIS
==================
Un bundle pour forum pour Symfony 2 (>= 2.7) et Symfony 3, simple a mettre en place et pleinement fonctionnel
Ce bundle utilise votre bundle utilisateur (qui peut hériter de FOSUserBundle)


Demo
-------------
Essayez le ici - http://demo.charlymartins.fr/demoworkingforum/


Fonctionnalités
------------------
- Forum avec sous-forum
- Utilise un role modérateur ROLE_MODERATOR (également les roles admin par défaut)
- L'éditeur de message utilise markdown avec la prévisualisation instantanée
- Prise en charge de smileys
- Système de signalement de messages
- Système de citation
- Les threads peuvent être verrouillés
- Les threads peuvent être epinglés
- Les threads peuvent être déplacés
- Les threads peuvent être marqués comme résolus
- Système d'auto-lock pour les vieux sujets
- Support le multilangage


- Breadcrumb (fil d'Arianne) automatique
- Compteur de messages (utilisateur, forum, sousforum) avec dernières réponses
- Pagination automatique sur la liste des thread, et les messages des threads
- Autoriser ou non les anonymes à lire les forums
- Système de bannissement
- Securité de la base de données : aucun HTML d'enregistré, uniquement du markdown
- Système de recherche



Installation
------------------
Ce bundle utilise KnpPaginatorBundle pour la pagination, KnpMarkdown pour le parsage du markdown et le package d'extra pour Symfony.
Ajoutez à votre composer.json, section 'require'
````json
"require" : {
        [...]
        "yosimitso/workingforumbundle" : "~1.0",
        "knplabs/knp-paginator-bundle": "~2.5",
        "knplabs/knp-markdown-bundle": "~1.5",
        "sensio/framework-extra-bundle": "~3.0"
    }
````

Ajoutez les bundles dans votre AppKernel
````php
  new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
  new Yosimitso\WorkingForumBundle\YosimitsoWorkingForumBundle(),
  new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
  new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle()

````
Ajoutez a votre app/config.yml

````yml
yosimitso_working_forum:
    thread_per_page: 50
    post_per_page: 10
    date_format: 'd/m/Y H:i:s'
    allow_anonymous_read: false
    allow_moderator_delete_thread: false
    theme_color: green
    lock_thread_older_than: 0
	
knp_paginator:
    page_range: 1                      # default page range used in pagination control
    default_options:
        page_name: page                # page query parameter name
        sort_field_name: sort          # sort field query parameter name
        sort_direction_name: direction # sort direction query parameter name
        distinct: true                 # ensure distinct results, useful when ORM queries are using GROUP BY statements
    template:
        pagination: YosimitsoWorkingForumBundle:Common:slidePagination.html.twig     # sliding pagination controls template
        sortable: KnpPaginatorBundle:Pagination:sortable_link.html.twig # sort link template
````   
Ajouter à votre app/config.yml dans la clé 'orm' :
````yml
 resolve_target_entities:
            Yosimitso\WorkingForumBundle\Entity\User: You\YourUserBundle\Entity\YourUser
````

Vous pouvez surcharger les fichiers de traductions

Votre entité utilisateur à besoin d'étendre : \Yosimitso\WorkingForumBundle\Entity\User
Exemple :
````php
   class User extends \Yosimitso\WorkingForumBundle\Entity\User
{
    // VOTRE ENTITE
}

````
Dans le cas où votre entité utilisateur étend déjà un autre de bundle (comme FOSUserBundle), implémenter l'interface \Yosimitso\WorkingForumBundle\Entity\UserInterface
dans votre entité. Ensuite copier / coller le contenu de \Yosimitso\WorkingForumBundle\Entity\User (attributs, getter, setter) dans votre entité.
