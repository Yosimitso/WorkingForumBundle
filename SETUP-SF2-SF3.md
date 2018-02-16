WorkingForumBundle
==================

Setup for Symfony 2/3/4 (without Flex structure)
------------------

This bundle use KnpPaginatorBundle for pagination, KnpMarkdown for markdown rendering and the extra package for Symfony
Add to your composer.json, section 'require'
````json
"require" : {
 [...]
 "yosimitso/workingforumbundle" : "~1.1",
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
    vote:
        threshold_useful_post: 5
    file_upload:
        enable: true
        max_size_ko: 10000
        accepted_format: [image/jpg, image/jpeg, image/png, image/gif, image/tiff, application/pdf]
        preview_file: true    
knp_paginator:
    page_range: 1                      # default page range used in pagination control
    default_options:
        page_name: page                # page query parameter name
        sort_field_name: sort          # sort field query parameter name
        sort_direction_name: direction # sort direction query parameter name
        distinct: true                 # ensure distinct results, useful when ORM queries are using GROUP BY statements
    template:
        pagination: "@YosimitsoWorkingForum/Common/slidePagination.html.twig"     # sliding pagination controls template
        sortable: "@KnpPaginator/Pagination/sortable_link.html.twig" # sort link template
````
If you decide to enable the file upload system, create a directory called "wf_uploads" into your web directory,
please also check if your PHP configuration allow file upload through forms and adjust the directives "upload_max_filesize" and "post_max_size" to your application's config

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

To import the bundle's routing, add to your app/config.yml (you are free to modifiy the prefix) :
````yml
yosimitso_working_forum:
    resource: "@YosimitsoWorkingForumBundle/Resources/config/routing.yml"
    prefix:   /
````    