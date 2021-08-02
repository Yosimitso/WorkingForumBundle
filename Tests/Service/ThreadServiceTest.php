<?php

namespace Yosimitso\WorkingForumBundle\Tests\Service;

use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig\Environment;
use Twig\Template;
use Yosimitso\WorkingForumBundle\Entity\Forum;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\PostReport;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\User;
use Yosimitso\WorkingForumBundle\Form\PostType;
use Yosimitso\WorkingForumBundle\Form\ThreadType;
use Yosimitso\WorkingForumBundle\Security\AuthorizationGuard;
use Yosimitso\WorkingForumBundle\Service\BundleParametersService;
use Yosimitso\WorkingForumBundle\Service\ThreadService;
use Yosimitso\MockDoctrineManager\EntityManagerMock;
use Yosimitso\WorkingForumBundle\Service\FileUploaderService;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ThreadServiceTest extends TestCase
{

    public function getTestedClass($em = null, $user = null, $authorization = null)
    {
        if (is_null($user)) {
            $user = $this->createMock(User::class);
            $user->setUsername = 'toto';
        }
            $tokenStorage = $this->createMock(TokenStorageInterface::class);
        
            $class = new class($user)
            {
                private $user;
                public function __construct($user)
                {
                    $this->user = $user;
                }

                function getUser()
                {
                    return $this->user;
                }
            };

        $tokenStorage->method('getToken')->willReturn($class);

        if (is_null($authorization)) {
            $authorization = $this->createMock(AuthorizationGuard::class);
        }
        
        if (is_null($em)) {
            $em = $this->getMockBuilder(EntityManagerMock::class)
                ->setMethods(['getRepository'])
                ->setMockClassName('EntityManagerInterface')
                ->getMock();
        }

        $bundleParameters = $this->createMock(BundleParametersService::class);
        $router = $this->createMock(Router::class);
        $router->method('generate')->willReturnCallback(function($route, $args) {
            if ($route === 'workingforum_subforum' && $args['forum'] === 'my-forum' && $args['subforum'] === 'my-subforum') {
                return 'my-forum/my-subforum/view';
            }
        });

        $templating = $this->createMock(Environment::class);
        $bundleParameters->allow_moderator_delete_thread = false;
        $testedClass = new ThreadService(
            0,
            $this->createMock(PaginatorInterface::class),
            10,
            $this->createMock(RequestStack::class),
            $em,
            $tokenStorage,
            $this->createMock(FileUploaderService::class),
            $authorization,
            $bundleParameters,
            $this->getFormFactory(),
            $router,
            $templating
        );

        return $testedClass;
    }

    private function getFormFactory()
    {
        $formFactory = $this->createMock(FormFactory::class);
        $formView = $this->createMock(FormView::class);
        $classFormFactory = new class($formView)
        {
            private $formView;
            
            public function __construct($formView)
            {
                $this->formView = $formView;
            }

            function createView()
            {

                return $this->formView;
            }
        };
        $formFactory->method('create')->willReturn($classFormFactory);

        return $formFactory;
    }

    public function testPin()
    {
        $em = new EntityManagerMock;
        
        $testedClass = $this->getTestedClass($em);

        $thread = new Thread;
        $this->assertTrue($testedClass->pin($thread));
        $this->assertTrue($em->getFlushedEntities()[0]->getPin());
    }

    public function testResolved()
    {
        $em = new EntityManagerMock;
        
        $testedClass = $this->getTestedClass($em);

        $thread = new Thread;

        $this->assertTrue($testedClass->resolve($thread));
        $this->assertTrue($em->getFlushedEntity(Thread::class)->getResolved());
    }

    public function testLocked()
    {
        $em = new EntityManagerMock;
        $testedClass = $this->getTestedClass($em);

        $thread = new Thread;

        $this->assertTrue($testedClass->lock($thread));
        $this->assertTrue($em->getFlushedEntity(Thread::class)->getLocked());
    }

    public function testReport()
    {
        $em = new EntityManagerMock;

        $testedClass = $this->getTestedClass($em);

        $post = new Post;
        $this->assertTrue($testedClass->report($post));
        $this->assertTrue($em->getFlushedEntities()[0] instanceof PostReport);
    }


    public function testMove()
    {
        $em = new EntityManagerMock;

        $testedClass = $this->getTestedClass($em);

        $thread = new Thread;
        $thread->setNbReplies(5);

        $currentSubforum = new Subforum;
        $currentSubforum->setName('former');
        $currentSubforum->setNbThread(20);
        $currentSubforum->setNbPost(50);

        $targetSubforum = new Subforum;
        $targetSubforum->setName('new');
        $targetSubforum->setNbThread(20);
        $targetSubforum->setNbPost(50);

        $this->assertTrue($testedClass->move($thread, $currentSubforum, $targetSubforum));
        $this->assertTrue($em->getFlushedEntities()[0] instanceof Thread);
        $this->assertTrue($em->getFlushedEntities()[1] instanceof Subforum);
        $this->assertTrue($em->getFlushedEntities()[2] instanceof Subforum);


        $this->assertEquals('new', $em->getFlushedEntities()[0]->getSubforum()->getName()); // THREAD MOVE TO THE RIGHT SUBFORUM
        $this->assertEquals(19, $em->getFlushedEntities()[1]->getNbThread()); // STATISTICS ARE UPDATED
        $this->assertEquals(21, $em->getFlushedEntities()[2]->getNbThread()); // STATISTICS ARE UPDATED

        $this->assertEquals(45, $em->getFlushedEntities()[1]->getNbPost()); // STATISTICS ARE UPDATED
        $this->assertEquals(55, $em->getFlushedEntities()[2]->getNbPost()); // STATISTICS ARE UPDATED
    }

    public function testDelete()
    {
        $em = $em = new EntityManagerMock;
        $testedClass = $this->getTestedClass($em);

        $thread = new Thread;
        $thread->setNbReplies(20);

        $subforum = new Subforum;
        $subforum->setNbThread(20);
        $subforum->setNbPost(50);

        $this->assertTrue($testedClass->delete($thread, $subforum));

        $this->assertEquals(19, $em->getFlushedEntity(Subforum::class)->getNbThread());
        $this->assertEquals(30, $em->getFlushedEntity(Subforum::class)->getNbPost());
        $this->assertTrue($em->getRemovedEntities()[0] instanceof Thread);
        $this->assertTrue($em->getFlushedEntities()[1] instanceof Thread);
    }

    public function testCreate()
    {
        $em = new EntityManagerMock;

        $user = $this->createMock(User::class);
        $user->setUsername = 'toto';

        $testedClass = $this->getTestedClass($em, $user);

        $form = $this->getMockBuilder(ThreadType::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData'])
            ->getMock();

        $class = new class
        {
            function getPost()
            {
                $secondClass = new class
                {
                    function getFilesUploaded()
                    {
                        return [];
                    }
                };

                return [0 => $secondClass];

            }
        };

        $form->method('getData')->willReturn($class);
        $thread = new Thread;

        $subforum = new Subforum;
        $subforum->setNbThread(20);
        $subforum->setNbPost(50);

        $post = new Post;
        $post->setContent('test');

        $this->assertTrue($testedClass->create($form, $post, $thread, $subforum));

        $user = $em->getFlushedEntity(get_class($user));
        $subforum = $em->getFlushedEntity(Subforum::class);
        $thread = $em->getFlushedEntity(Thread::class);
        $post = $em->getFlushedEntity(Post::class);

        $this->assertEquals(21, $subforum->getNbThread());
        $this->assertEquals(1, $thread->getNbReplies());
        $this->assertEquals('test', $post->getContent());
    }

    public function testCreateWithFiles()
    {
        $em = new EntityManagerMock;

        $user = $this->createMock(User::class);
        $user->setUsername = 'toto';

        $testedClass = $this->getTestedClass($em, $user);

        $form = $this->getMockBuilder(ThreadType::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData'])
            ->getMock();

        $class = new class
        {
            function getPost()
            {
                $secondClass = new class
                {
                    function getFilesUploaded()
                    {
                        $file = new UploadedFile(__DIR__.'/../Mock/file_test.jpg', "file_test.jpg");
                        return [$file];
                    }
                };

                return [0 => $secondClass];

            }
        };

        $form->method('getData')->willReturn($class);
        $thread = new Thread;

        $subforum = new Subforum;
        $subforum->setNbThread(20);
        $subforum->setNbPost(50);

        $post = new Post;
        $post->setContent('test');

        $this->assertTrue($testedClass->create($form, $post, $thread, $subforum));

        $user = $em->getFlushedEntity(get_class($user));
        $subforum = $em->getFlushedEntity(Subforum::class);
        $thread = $em->getFlushedEntity(Thread::class);
        $post = $em->getFlushedEntity(Post::class);

        $this->assertEquals(21, $subforum->getNbThread());
        $this->assertEquals(1, $thread->getNbReplies());

    }

    public function testPost()
    {
        $em = new EntityManagerMock;

        $user = $this->createMock(User::class);
        $user->setUsername = 'toto';

        $testedClass = $this->getTestedClass($em, $user);

        $subforum = new Subforum;
        $subforum->setNbThread(20);
        $subforum->setNbPost(50);

        $thread = new Thread;
        $thread->setNbReplies(20);

        $post = new Post;
        $post->setContent('test');

        $form = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData'])
            ->getMock();

        $class = new class
        {
            function getFilesUploaded()
            {
                return [];
            }
        };

        $form->method('getData')->willReturn($class);

        $this->assertTrue($testedClass->post($subforum, $thread, $post, $user, $form));

        $user = $em->getFlushedEntity(get_class($user));
        $subforum = $em->getFlushedEntity(Subforum::class);
        $thread = $em->getFlushedEntity(Thread::class);
        $post = $em->getFlushedEntity(Post::class);

        $this->assertEquals(20, $subforum->getNbThread());
        $this->assertEquals(51, $subforum->getNbPost());
        $this->assertEquals(21, $thread->getNbReplies());
        $this->assertEquals('test', $post->getContent());

    }

    public function testGetAvailableActionsClassicUser()
    {
        $testedClass = $this->getTestedClass();

        // CLASSIC USER, NOT THREAD'S AUTHOR
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);

        $author = $this->createMock(User::class);
        $author->method('getId')->willReturn(2);
        $thread = new Thread;
        $thread->setAuthor($author);

        $result = $testedClass->getAvailableActions($user, $thread, false, true);

        $this->assertFalse($result['setResolved']);
        $this->assertTrue($result['quote']);
        $this->assertTrue($result['report']);
        $this->assertTrue($result['post']);
        $this->assertTrue($result['subscribe']);
        $this->assertFalse($result['moveThread']);
        $this->assertFalse($result['allowModeratorDeleteThread']);

        // USER IS THE THREAD'S AUTHOR
        $thread->setAuthor($user);

        $result = $testedClass->getAvailableActions($user, $thread, false, true);
        $this->assertTrue($result['setResolved']); // THREAD'S AUTHOR CAN "RESOLVE" HIS THREAD


    }

    public function testGetAvailableActionsAnonymousUser()
    {
        $testedClass = $this->getTestedClass();

        // ANONYMOUS USER
        $user = null;
        $thread = new Thread;

        $result = $testedClass->getAvailableActions($user, $thread, false, true);
        $this->assertFalse($result['setResolved']);
        $this->assertFalse($result['quote']);
        $this->assertFalse($result['report']);
        $this->assertFalse($result['post']);
        $this->assertFalse($result['subscribe']);
        $this->assertFalse($result['moveThread']);
        $this->assertFalse($result['allowModeratorDeleteThread']);
    }

    public function testGetAvailableActionsModerator()
    {
        $authorization = $this->createMock(AuthorizationGuard::class);
        $authorization->method('hasModeratorAuthorization')->willReturn(true);

        $testedClass = $this->getTestedClass(null, null, $authorization);


        // MODERATOR
        $user = $this->createMock(User::class);
        $thread = new Thread;

        $result = $testedClass->getAvailableActions($user, $thread, false, true);
        $this->assertTrue($result['setResolved']);
        $this->assertTrue($result['quote']);
        $this->assertTrue($result['report']);
        $this->assertTrue($result['post']);
        $this->assertTrue($result['subscribe']);
        $this->assertTrue($result['moveThread'] instanceof FormView);
        $this->assertFalse($result['allowModeratorDeleteThread']);
    }

    public function testGetAvailableActionsAdmin()
    {
        $authorization = $this->createMock(AuthorizationGuard::class);
        $authorization->method('hasModeratorAuthorization')->willReturn(true);

        $testedClass = $this->getTestedClass(null, null, $authorization);


        // MODERATOR
        $user = $this->createMock(User::class);
        $thread = new Thread;

        $result = $testedClass->getAvailableActions($user, $thread, false, true);
        $this->assertTrue($result['setResolved']);
        $this->assertTrue($result['quote']);
        $this->assertTrue($result['report']);
        $this->assertTrue($result['post']);
        $this->assertTrue($result['subscribe']);
        $this->assertTrue($result['moveThread'] instanceof FormView);
        $this->assertFalse($result['allowModeratorDeleteThread']);
    }

    public function testRedirectToSubforum()
    {
        $testedClass = $this->getTestedClass();
        $forum = (new Forum())->setSlug('my-forum');
        $subforum = (new Subforum())->setSlug('my-subforum');
        $response = $testedClass->redirectToSubforum($forum, $subforum);

        $this->assertEquals('my-forum/my-subforum/view', $response->getTargetUrl());
    }

}
