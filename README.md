WorkingForumBundle
==================
** STILL IN DEVELOPMENT **
A forum bundle for Symfony 2, easy to use and fully fonctionnal
This bundle work with your user bundle with no extra configuration (which can extended FOSUserBundle)


Functionnalities
------------------
- Forum with subforum
- Moderator role
- Post form using markdown with instant preview
- Topic locking
- Topic marked as 'resolved'
- Automatic breadcrumb


Setup
------------------
This bundle use KnpPaginatorBundle for pagination, KnpMarkdown for backend markdown rendering
Add to your composer.json, section 'require'
````json
"require" : {
        [...]
        "yosimitso/demobundle" : "dev-master"
    },
    "repositories" : [{
        "type" : "vcs",
        "url" : "https://github.com/Yosimitso/DemoBundle.git"
    }],
```


Register the bundles in your AppKernel
Add to your app/config.yml

````yml
yosimitso_forum:
    topic_per_page: 10
    post_per_page: 5
    date_format: 'd/m/Y H:i:s'
```    
Add to your translation file


Demo
-------------
Coming soon

Todo
-----------
- Removing post by a moderator
