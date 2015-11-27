WorkingForumBundle
==================

ENGLISH
=================
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
- Messages counting (user, forum, subforum) with last replies
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
    }
```


Register the bundles in your AppKernel
````php
new Yosimitso\WorkingForumBundle\YosimitsoWorkingForumBundle()
```
Add to your app/config.yml

````yml
yosimitso_working_forum:
    topic_per_page: 10
    post_per_page: 5
    date_format: 'd/m/Y H:i:s'
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
```
Add to you app/config.yml into 'orm' key :
````yml
 resolve_target_entities:
            Yosimitso\WorkingForumBundle\Entity\User: You\YourUserBundle\Entity\YourUser
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
		 /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set nbPost
     *
     * @param integer $nbPost
     *
     * @return User
     */
    public function setNbPost($nbPost)
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    /**
     * Get nbPost
     *
     * @return integer
     */
    public function getNbPost()
    {
        return $this->nbPost;
    }

    /**
     * Set avatarUrl
     *
     * @param string $avatarUrl
     *
     * @return User
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * Get avatarUrl
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

```

Todo
-----------
- Removing post by a moderator
- Allow anonymous users to create thread if set in the forums' configuration
- Forbide anonymous users to read if set in the forums' configuration

FRANCAIS
==================
** EN DEVELOPEMENT **
Un bundle pour forum pour Symfony 2, simple a mettre en place et pleinement fonctionnel
Ce bundle utilise votre bundle utilisateur (qui peut hériter de FOSUserBundle)


Demo
-------------
Bientôt


Fonctionnalités
------------------
- Forum avec sous-forum
- Utilise un role modérateur ROLE_MODERATOR (également les roles admin par défaut)
- L'éditeur de message utilise markdown avec la prévisualisation instantanée
- Les topics peuvent être verrouillés
- Support le multilangage
- Les topics peuvent être marqués comme résolus
- Breadcrumb (fil d'Arianne) automatique
- Compteur de messages (utilisateur, forum, suforum) avec dernières réponses
- Pagination automatique sur la liste des topic, et les messages des topics


Installation
------------------
Ce bundle utilise KnpPaginatorBundle pour la pagination, KnpMarkdown pour le parsage du markdown dans le backend
Ajoutez à votre composer.json, section 'require'
````json
"require" : {
        [...]
        "yosimitso/workingforumbundle" : "dev-master",
        "knplabs/knp-paginator-bundle": "2.4.*@dev",
        "knplabs/knp-markdown-bundle": "~1.3"
    }
```

Ajoutez le bundle dans votre AppKernel
````php
new Yosimitso\WorkingForumBundle\YosimitsoWorkingForumBundle()
```
Ajoutez à votre app/config.yml

````yml
yosimitso_working_forum:
    topic_per_page: 10
    post_per_page: 5
    date_format: 'd/m/Y H:i:s'
	
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
```   
Ajouter à votre app/config.yml dans la clé 'orm' :
````yml
 resolve_target_entities:
            Yosimitso\WorkingForumBundle\Entity\User: You\YourUserBundle\Entity\YourUser
```

Vous pouvez surcharger les fichiers de traductions

Votre entité Utilisateur à besoin de ces propriétés avec getter et setter
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
		 /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set nbPost
     *
     * @param integer $nbPost
     *
     * @return User
     */
    public function setNbPost($nbPost)
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    /**
     * Get nbPost
     *
     * @return integer
     */
    public function getNbPost()
    {
        return $this->nbPost;
    }

    /**
     * Set avatarUrl
     *
     * @param string $avatarUrl
     *
     * @return User
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * Get avatarUrl
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

```

Todo
-----------
- Suppression d'un topic par un modérateur
- Autoriser ou non les utilisateurs anonyme à créer des sujets selon la configuration du forum
- Interdire les utilisateurs anonymes à lire les forums selon la configuration du forum

