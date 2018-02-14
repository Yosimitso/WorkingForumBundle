[1mdiff --git a/Controller/ThreadController.php b/Controller/ThreadController.php[m
[1mindex 90a5616..678b3d9 100644[m
[1m--- a/Controller/ThreadController.php[m
[1m+++ b/Controller/ThreadController.php[m
[36m@@ -45,6 +45,9 @@[m [mclass ThreadController extends Controller[m
         $subforum = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')->findOneBySlug($subforum_slug);[m
         $thread = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Thread')->findOneBySlug($thread_slug);[m
         $user = $this->getUser();[m
[32m+[m[32m        $threadUtil = $this->get('yosimitso_workingforum_util_thread');[m
[32m+[m[32m        $anonymousUser = (is_null($user)) ? true : false;[m
[32m+[m[32m        $flashbag =  $this->get('session')->getFlashBag();[m
 [m
         $authorizationChecker = $this->get('yosimitso_workingforum_authorization');[m
         if (!$authorizationChecker->hasSubforumAccess($subforum)) { // CHECK IF USER HAS AUTHORIZATION TO VIEW THIS THREAD[m
[36m@@ -58,9 +61,8 @@[m [mclass ThreadController extends Controller[m
             );[m
 [m
         }[m
[31m-            $autolock = $this->get('yosimitso_workingforum_util_thread')->isAutolock($thread); // CHECK IF THREAD IS AUTOMATICALLY LOCKED (TOO OLD?)[m
[32m+[m[32m            $autolock = $threadUtil->isAutolock($thread); // CHECK IF THREAD IS AUTOMATICALLY LOCKED (TOO OLD?)[m
             $listSmiley = $this->get('yosimitso_workingforum_smiley')->getListSmiley(); // Smileys available for markdown[m
[31m-            $paginator = $this->get('knp_paginator');[m
 [m
             $my_post = new Post($user, $thread);[m
             $form = $this->createForm(PostType::class, $my_post); // create form for posting[m
[36m@@ -68,9 +70,9 @@[m [mclass ThreadController extends Controller[m
 [m
             if ($form->isSubmitted()) { // USER SUBMIT HIS POST[m
 [m
[31m-                if ($user->isBanned()) // USER IS BANNED CAN'T POST[m
[32m+[m[32m                if (!$anonymousUser && $user->isBanned()) // USER IS BANNED CAN'T POST[m
                 {[m
[31m-                    $this->get('session')->getFlashBag()->add([m
[32m+[m[32m                    $flashbag->add([m
                         'error',[m
                         $this->get('translator')->trans('message.banned', [], 'YosimitsoWorkingForumBundle')[m
                     )[m
[36m@@ -81,7 +83,7 @@[m [mclass ThreadController extends Controller[m
 [m
                 if ($autolock) // THREAD IS LOCKED CAUSE TOO OLD ACCORDING TO PARAMETERS[m
                 {[m
[31m-                    $this->get('session')->getFlashBag()->add([m
[32m+[m[32m                    $flashbag->add([m
                         'error',[m
                         $this->get('translator')->trans('thread_too_old_locked', [], 'YosimitsoWorkingForumBundle')[m
                     )[m
[36m@@ -92,45 +94,49 @@[m [mclass ThreadController extends Controller[m
 [m
                 if ($form->isValid()) {[m
 [m
[31m-//                    $my_post[m
[31m-//                        ->setContent($my_post->getContent()) // VOIR SI BESOIN ?[m
                     $subforum->newPost($user); // UPDATE SUBFORUM STATISTIC[m
                     $thread->addReply($user); // UPDATE THREAD STATISTIC[m
[31m-                    $user->addNbPost(1);[m
 [m
[32m+[m[32m                    if (!$anonymousUser) {[m
[32m+[m[32m                        $user->addNbPost(1);[m
[32m+[m[32m                        $em->persist($user);[m
[32m+[m[32m                    }[m
 [m
[31m-                    $em->persist($user);[m
                     $em->persist($thread);[m
                     $em->persist($my_post);[m
                     $em->persist($subforum);[m
[31m-//                    exit(dump($form->getData()->getFilesUploaded()));[m
[32m+[m
[32m+[m[32m                    $postQuery = $em[m
[32m+[m[32m                        ->getRepository('Yosimitso\WorkingForumBundle\Entity\Post')[m
[32m+[m[32m                        ->findByThread($thread->getId())[m
[32m+[m[32m                    ;[m
[32m+[m
[32m+[m[32m                    $post_list =  $threadUtil->paginate($postQuery);[m
[32m+[m
                     if (!empty($form->getData()->getFilesUploaded())) {[m
                         $fileUploader = $this->get('yosimitso_workingforum_util_fileuploader');[m
                         $file = $fileUploader->upload($form->getData()->getFilesUploaded(), $my_post);[m
 [m
[31m-                        if (is_null($file)) {[m
[31m-                            exit('error');[m
[32m+[m[32m                        if (!$file) { // FILE UPLOAD FAILED[m
[32m+[m[32m                            $flashbag->add([m
[32m+[m[32m                                'error',[m
[32m+[m[32m                                $fileUploader->getErrorMessage()[m
[32m+[m[32m                            );[m
[32m+[m
[32m+[m[32m                            return $this->redirect([m
[32m+[m[32m                                $this->generateUrl([m
[32m+[m[32m                                    'workingforum_thread',[m
[32m+[m[32m                                    ['subforum_slug' => $subforum_slug, 'thread_slug' => $thread_slug, 'page' => $post_list->getPageCount()][m
[32m+[m[32m                                ));[m
                         }[m
[31m-//                        exit(dump($file));[m
                         $my_post->addFiles($file);[m
                     }[m
 [m
                     $em->flush();[m
 [m
[31m-                    $this->get('session')->getFlashBag()->add([m
[32m+[m[32m                    $flashbag->add([m
                         'success',[m
                         $this->get('translator')->trans('message.posted', [], 'YosimitsoWorkingForumBundle')[m
[31m-                    )[m
[31m-                    ;[m
[31m-                    $post_query = $em[m
[31m-                        ->getRepository('Yosimitso\WorkingForumBundle\Entity\Post')[m
[31m-                        ->findByThread($thread->getId())[m
[31m-                    ;[m
[31m-[m
[31m-                    $post_list = $paginator->paginate([m
[31m-                        $post_query,[m
[31m-                        $request->query->get('page')/*page number*/,[m
[31m-                        $this->container->getParameter('yosimitso_working_forum.post_per_page') /*limit per page*/[m
                     );[m
 [m
                     return $this->redirect($this->generateUrl('workingforum_thread',[m
[36m@@ -151,17 +157,12 @@[m [mclass ThreadController extends Controller[m
             $moveThread = false;[m
         }[m
 [m
[31m-        $post_query = $em[m
[32m+[m[32m        $postQuery = $em[m
             ->getRepository('Yosimitso\WorkingForumBundle\Entity\Post')[m
             ->findByThread($thread->getId())[m
         ;[m
 [m
[31m-[m
[31m-        $post_list = $paginator->paginate([m
[31m-            $post_query,[m
[31m-            $request->query->get('page',1)/*page number*/,[m
[31m-            $this->container->getParameter('yosimitso_working_forum.post_per_page') /*limit per page*/[m
[31m-        );[m
[32m+[m[32m        $post_list = $threadUtil->paginate($postQuery);[m
 [m
         $hasAlreadyVoted = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\PostVote')->getThreadVoteByUser($thread, $user);[m
 [m
[36m@@ -170,6 +171,7 @@[m [mclass ThreadController extends Controller[m
             'thresholdUsefulPost' => $this->container->getParameter('yosimitso_working_forum.vote')['threshold_useful_post'],[m
             'fileUpload' => $this->container->getParameter('yosimitso_working_forum.file_upload')[m
             ];[m
[32m+[m
         return $this->render('YosimitsoWorkingForumBundle:Thread:thread.html.twig',[m
             [[m
                 'subforum'    => $subforum,[m
[36m@@ -202,11 +204,10 @@[m [mclass ThreadController extends Controller[m
         $em = $this->getDoctrine()->getManager();[m
         $subforum = $em->getRepository('Yosimitso\WorkingForumBundle\Entity\Subforum')->findOneBySlug($subforum_slug);[m
         $authorizationChecker = $this->get('yosimitso_workingforum_authorization');[m
[32m+[m[32m        $flashbag =  $this->get('session')->getFlashBag();[m
 [m
           if (!$authorizationChecker->hasSubforumAccess($subforum)) {[m
[31m-              $this->get('session')[m
[31m-                  ->getFlashBag()[m
[31m-                  ->add([m
[32m+[m[32m              $flashbag->add([m
                       'error',[m
                       $this->get('translator')->trans($authorizationChecker->getErrorMessage(), [], 'YosimitsoWorkingForumBundle')[m
                   )[m
[36m@@ -227,7 +228,7 @@[m [mclass ThreadController extends Controller[m
 [m
 [m
         if ($form->isValid()) {[m
[31m-//            $my_post->setContent($my_post->getContent()); // VOIR SI BESOIN ?[m
[32m+[m
             $subforum->newThread($user); // UPDATE STATISTIC[m
 [m
             $user->addNbPost(1);[m
[36m@@ -244,7 +245,7 @@[m [mclass ThreadController extends Controller[m
             $em->persist($my_thread);[m
             $em->flush();[m
 [m
[31m-            $this->get('session')->getFlashBag()->add([m
[32m+[m[32m            $flashbag->add([m
                 'success',[m
                 $this->get('translator')->trans('message.threadCreated', [], 'YosimitsoWorkingForumBundle')[m
             )[m
[1mdiff --git a/Resources/config/services.yml b/Resources/config/services.yml[m
[1mindex 1c54351..bcc98e2 100644[m
[1m--- a/Resources/config/services.yml[m
[1m+++ b/Resources/config/services.yml[m
[36m@@ -30,6 +30,9 @@[m [mservices:[m
         class: Yosimitso\WorkingForumBundle\Util\Thread[m
         arguments:[m
             - "%yosimitso_working_forum.lock_thread_older_than%"[m
[32m+[m[32m            - "@knp_paginator"[m
[32m+[m[32m            - "%yosimitso_working_forum.post_per_page%"[m
[32m+[m[32m            - "@request_stack"[m
 [m
     yosimitso_workingforum_util_fileuploader:[m
         class: Yosimitso\WorkingForumBundle\Util\FileUploader[m
[36m@@ -37,4 +40,5 @@[m [mservices:[m
             - "@assets.packages"[m
             - "@doctrine.orm.entity_manager"[m
             - "%yosimitso_working_forum.file_upload%"[m
[32m+[m[32m            - "@translator"[m
 [m
[1mdiff --git a/Resources/public/css/forum.css b/Resources/public/css/forum.css[m
[1mindex 99901fc..f2139b9 100644[m
[1m--- a/Resources/public/css/forum.css[m
[1m+++ b/Resources/public/css/forum.css[m
[36m@@ -468,7 +468,7 @@[m [mcode {[m
 .wf_post-right blockquote {[m
     padding: 10px 20px;[m
     font-size: 100%;[m
[31m-[m
[32m+[m[32m    background-color: white;[m[41m[m
 }[m
 [m
 #wf_smiley, .wf_header-block {[m
[36m@@ -615,9 +615,25 @@[m [mcode {[m
     width:50px;[m
 }[m
 [m
[32m+[m[32m.wf_enclosed_files .size {[m[41m[m
[32m+[m[32m    color:grey;[m[41m[m
[32m+[m[32m}[m[41m[m
[32m+[m[41m[m
 .wf_add_enclosed_file {[m
    color:black;[m
[32m+[m[32m   text-decoration: none;[m[41m[m
[32m+[m[32m   padding: 10px,;[m[41m[m
[32m+[m[32m   display:block;[m[41m[m
[32m+[m[32m}[m[41m[m
[32m+[m[41m[m
[32m+[m[32m.wf_add_enclosed_file:hover {[m[41m[m
     text-decoration: none;[m
[32m+[m[32m    color: black;[m[41m[m
[32m+[m[41m[m
[32m+[m[32m}[m[41m[m
[32m+[m[41m[m
[32m+[m[32m.wf_enclosed_files ul {[m[41m[m
[32m+[m[32m    list-style: none;[m[41m[m
 }[m
 [m
 .wf_header-block {[m
[36m@@ -631,6 +647,10 @@[m [mcode {[m
     margin-top:0;[m
 }[m
 [m
[32m+[m[32m.wf_file_upload {[m[41m[m
[32m+[m[32m    margin-bottom: 10px;[m[41m[m
[32m+[m[32m}[m[41m[m
[32m+[m[41m[m
 .wf_file_upload .NFI-wrapper {[m
 // the container div[m
 }[m
[1mdiff --git a/Resources/translations/YosimitsoWorkingForumBundle.en.yml b/Resources/translations/YosimitsoWorkingForumBundle.en.yml[m
[1mindex 28d39b0..150651a 100644[m
[1m--- a/Resources/translations/YosimitsoWorkingForumBundle.en.yml[m
[1m+++ b/Resources/translations/YosimitsoWorkingForumBundle.en.yml[m
[36m@@ -61,6 +61,11 @@[m [mforum:[m
     file_upload:[m
         accepted_format: Accepted formats[m
         add_enclosed_file: Add an enclosed file[m
[32m+[m[32m        error:[m[41m[m
[32m+[m[32m            default: An error occured on file upload, please contact an administrator[m[41m[m
[32m+[m[32m            max_size_exceeded: Your files exceed the maximum size allowed, the limit is %max_size% ko[m[41m[m
[32m+[m[32m            invalid_format: "Your file format isn't allowed : %format%"[m[41m[m
[32m+[m[32m            invalid_filename: "Your filename isn't allowed : %filename%"[m[41m[m
 [m
 pagination:[m
     previous: Previous[m
[1mdiff --git a/Resources/views/Post/post.html.twig b/Resources/views/Post/post.html.twig[m
[1mindex 005ad04..c1ec946 100644[m
[1m--- a/Resources/views/Post/post.html.twig[m
[1m+++ b/Resources/views/Post/post.html.twig[m
[36m@@ -84,7 +84,7 @@[m
                                 {% if file.extension in ['jpg', 'jpeg', 'png', 'gif', 'tiff'] and parameters.fileUpload.preview_file  %}[m
                                 <img class="preview" src="{{ asset(file.path) }}" />[m
                                 {% endif %}[m
[31m-                                {{ file.originalName }} - {{ (file.size/1000) | round }} ko[m
[32m+[m[32m                                {{ file.originalName }} <span class="size">- {{ (file.size/1000) | round }} ko</span>[m[41m[m
                             </a>[m
                         </li>[m
                     {% endfor %}[m
[1mdiff --git a/Util/FileUploader.php b/Util/FileUploader.php[m
[1mindex 1102d15..25788fc 100644[m
[1m--- a/Util/FileUploader.php[m
[1m+++ b/Util/FileUploader.php[m
[36m@@ -5,30 +5,48 @@[m [mnamespace Yosimitso\WorkingForumBundle\Util;[m
 use Symfony\Component\HttpFoundation\File\UploadedFile;[m
 use Yosimitso\WorkingForumBundle\Entity\File;[m
 [m
[32m+[m[32m/**[m
[32m+[m[32m * Class FileUploader[m
[32m+[m[32m * @package Yosimitso\WorkingForumBundle\Util[m
[32m+[m[32m * Handle file upload system[m
[32m+[m[32m */[m
 class FileUploader[m
 {[m
     private $path;[m
     private $em;[m
     private $configFileUpload;[m
[32m+[m[32m    private $translator;[m
[32m+[m[32m    private $errorMessage;[m
 [m
[31m-    public function __construct($asset, $em, $configFileUpload)[m
[32m+[m[32m    public function __construct($asset, $em, $configFileUpload, $translator)[m
     {[m
         $this->path = 'wf_uploads/'.date('Y/m/');[m
         $this->em = $em;[m
         $this->configFileUpload = $configFileUpload;[m
     }[m
 [m
[32m+[m[32m    /**[m
[32m+[m[32m     * @param array $filesSubmitted[m
[32m+[m[32m     * @param $post[m
[32m+[m[32m     * @return array|bool[m
[32m+[m[32m     * Upload submited files on server[m
[32m+[m[32m     */[m
     public function upload(array $filesSubmitted, $post)[m
     {[m
[32m+[m[32m        $this->errorMessage = '';[m
         $fileList = [];[m
[31m-[m
         $totalSize = 0;[m
         foreach ($filesSubmitted as $fileSubmitted) {[m
             $totalSize += $fileSubmitted->getSize() / 1000;[m
         }[m
 [m
[31m-        if ($totalSize > $this->configFileUpload['max_size_ko']) {[m
[31m-            exit('too big');[m
[32m+[m[32m        if (($totalSize > $this->configFileUpload['max_size_ko']) || $totalSize > (int) ini_get('upload_max_filesize') || $totalSize > (int) ini_get('post_max_size')) {[m
[32m+[m[32m            $this->errorMessage = $this->translator->trans([m
[32m+[m[32m                'forum.file_upload.error.max_size_exceeded',[m
[32m+[m[32m                ['max_size' => $this->configFileUpload['max_size_ko']],[m
[32m+[m[32m                'YosimitsoWorkingForumBundle'[m
[32m+[m[32m            );[m
[32m+[m[32m            return false;[m
         }[m
 [m
 [m
[36m@@ -36,44 +54,65 @@[m [mclass FileUploader[m
 [m
 [m
             if ($fileSubmitted->getError()) {[m
[31m-                exit('error');[m
[32m+[m
[32m+[m[32m                $this->errorMessage = $this->translator->trans('forum.file_upload.error.default', [], 'YosimitsoWorkingForumBundle');[m
[32m+[m[32m                return false;[m
             }[m
 [m
             if (!in_array($fileSubmitted->getMimeType(), $this->configFileUpload['accepted_format'])) {[m
[31m-                exit('invalid format');[m
[32m+[m[32m                $this->errorMessage = $this->translator->trans([m
[32m+[m[32m                    'forum.file_upload.error.invalid_format',[m
[32m+[m[32m                    ['format' => $fileSubmitted->getMimeType()],[m
[32m+[m[32m                    'YosimitsoWorkingForumBundle');[m
[32m+[m[32m                return false;[m
             }[m
 [m
             $file = new File;[m
             $originalFilename = [];[m
             preg_match('/(.+?)\..+/', $fileSubmitted->getClientOriginalName(), $originalFilename);[m
[31m-            if (!isset($originalFilename[1])) {[m
[31m-                exit('invalid filename');[m
[32m+[m
[32m+[m[32m            if (!isset($originalFilename[1])) { // FILENAME IS INVALID[m
[32m+[m[32m                $this->errorMessage = $this->translator->trans([m
[32m+[m[32m                    'forum.file_upload.error.invalid_filename',[m
[32m+[m[32m                    ['filename' => $originalFilename],[m
[32m+[m[32m                    'YosimitsoWorkingForumBundle'[m
[32m+[m[32m                );[m
[32m+[m[32m                return false;[m
             }[m
 [m
             $filename = htmlentities(substr($originalFilename[1], 0, 10));[m
             $file->setFilename(md5(uniqid()).'-'.$filename.'.'.$fileSubmitted->guessExtension()); // UNIQUE FILENAME[m
[31m-            $file->setOriginalName([m
[31m-                $originalFilename[1].'.'.$fileSubmitted->guessExtension()[m
[31m-            ); // DON'T USE THE EXTENSION PROVIDED BY THE USER[m
[32m+[m[32m            $file->setOriginalName($originalFilename[1].'.'.$fileSubmitted->guessExtension()); // DON'T USE THE EXTENSION PROVIDED BY THE USER[m
             $file->setExtension($fileSubmitted->guessExtension());[m
             $file->setSize($fileSubmitted->getSize());[m
 [m
[31m-            try {[m
[32m+[m[32m            try { // UPLOAD ON SERVER[m
                 $fileUploaded = $fileSubmitted->move($this->path, $file->getFilename());[m
[31m-//            exit(dump($fileUploaded));[m
                 $file->setPath($fileUploaded->getPath().'/'.$fileUploaded->getFilename());[m
 [m
                 $file->setPost($post);[m
                 $this->em->persist($file);[m
                 $fileList[] = $file;[m
             } catch (\Exception $e) {[m
[31m-                exit('bug upload '.$e->getMessage());[m
[31m-[m
[31m-                return null;[m
[32m+[m[32m                $this->errorMessage = $this->translator->trans('forum.file_upload.error.default', [], 'YosimitsoWorkingForumBundle');[m
[32m+[m[32m                return false;[m
             }[m
         }[m
[31m-        $this->em->flush();[m
 [m
[32m+[m[32m        $this->em->flush();[m
         return $fileList;[m
     }[m
[32m+[m
[32m+[m[32m    /**[m
[32m+[m[32m     * @return string[m
[32m+[m[32m     * Get latest error message if upload failed[m
[32m+[m[32m     */[m
[32m+[m[32m    public function getErrorMessage()[m
[32m+[m[32m    {[m
[32m+[m[32m        if (!empty($this->errorMessage)) {[m
[32m+[m[32m            return $this->errorMessage;[m
[32m+[m[32m        } else {[m
[32m+[m[32m            return false;[m
[32m+[m[32m        }[m
[32m+[m[32m    }[m
 }[m
\ No newline at end of file[m
[1mdiff --git a/Util/Thread.php b/Util/Thread.php[m
[1mindex 7d97dfe..c22e4e0 100644[m
[1m--- a/Util/Thread.php[m
[1m+++ b/Util/Thread.php[m
[36m@@ -11,9 +11,15 @@[m [mnamespace Yosimitso\WorkingForumBundle\Util;[m
 class Thread[m
 {[m
     private $lockThreadOlderThan;[m
[32m+[m[32m    private $paginator;[m[41m[m
[32m+[m[32m    private $postPerPage;[m[41m[m
[32m+[m[32m    private $requestStack;[m[41m[m
 [m
[31m-    public function __construct($lockThreadOlderThan) {[m
[32m+[m[32m    public function __construct($lockThreadOlderThan, $paginator, $postPerPage, $requestStack) {[m[41m[m
         $this->lockThreadOlderThan = $lockThreadOlderThan;[m
[32m+[m[32m        $this->paginator = $paginator;[m[41m[m
[32m+[m[32m        $this->postPerPage = $postPerPage;[m[41m[m
[32m+[m[32m        $this->requestStack = $requestStack;[m[41m[m
     }[m
 [m
     public function isAutolock($thread)[m
[36m@@ -30,4 +36,13 @@[m [mclass Thread[m
             return false;[m
         }[m
     }[m
[32m+[m[41m[m
[32m+[m[32m    public function paginate($postQuery)[m[41m[m
[32m+[m[32m    {[m[41m[m
[32m+[m[32m        return $this->paginator->paginate([m[41m[m
[32m+[m[32m            $postQuery,[m[41m[m
[32m+[m[32m            $this->requestStack->getCurrentRequest()->query->get('page',1),[m[41m[m
[32m+[m[32m            $this->postPerPage[m[41m[m
[32m+[m[32m        );[m[41m[m
[32m+[m[32m    }[m[41m[m
 }[m
\ No newline at end of file[m
