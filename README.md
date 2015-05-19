WorkingForumBundle
==================
** STILL IN DEVELOPMENT **
A forum bundle for Symfony 2, easy to use and fully fonctionnal
This bundle work with your user bundle with no extra configuration (which can extended FOSUserBundle)

Demo
-------------
Coming soon


Functionnalities
------------------
- Forum with subforum
- Use a moderator role as ROLE_MODERATOR (and default admin roles)
- Post editor using markdown with instant preview
- Enable topic locking
- Support multi language
- Enable marking topic as 'resolved'
- Automatic breadcrumb
- Messages counting (user, forum, suforum) with last replies
- Automatic pagination on topic list and topic


Setup
------------------
This bundle use KnpPaginatorBundle for pagination, KnpMarkdown for backend markdown rendering
Add to your composer.json, section 'require'
````json
"require" : {
        [...]
        "yosimitso/workingforumbundle" : "dev-master",
        "knplabs/knp-paginator-bundle": "2.4.*@dev",
        "knplabs/knp-markdown-bundle": "~1.3"
    },
    "repositories" : [{
        "type" : "vcs",
        "url" : "https://github.com/Yosimitso/WorkingForumBundle.git"
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
You can override the translations files

Your User Entity need these properties with getter and setter :
````php
       /**
     * @var integer
     * @ORM\Column(name="nb_post", type="integer")
     */
	 protected $nbPost;
 /**   
         * @var string
         * @ORM\Column(name="avatar_url", type="string",nullable=true)
         */
   
        protected $avatarUrl;
 /**   
         * @var string
         * @ORM\Column(name="username", type="string",nullable=true)
         */
   
        protected $username;
```

Todo
-----------
- Removing post by a moderator
