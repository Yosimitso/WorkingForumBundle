<?php

namespace Yosimitso\WorkingForumBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Yosimitso\WorkingForumBundle\Util\Thread as ThreadUtil;
use Yosimitso\WorkingForumBundle\Entity\Thread as ThreadEntity;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Yosimitso\WorkingForumBundle\Util\FileUploader;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\Translator;
use Yosimitso\WorkingForumBundle\Entity\Post;

/**
 * Class FileUploaderTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Util
 */
class FileUploaderTest extends WebTestCase
{
    private $files;
    private $fileUploader;
    private $em;
    private $translator;
    private $post;

    public function setUp()
    {
        $file = $this->getMockBuilder(UploadedFile::class)
            ->setConstructorArgs(['C:\Users\MyUser\AppData\Local\Temp\php6BA8.tmp', 'picture_10201115386585046_1320686413_n.jpg', 'image/jpeg', true])
            ->getMock();

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['persist', 'flush'])
            ->getMock();

        $em->method('persist')->willReturn(true);
        $em->method('flush')->willReturn(true);
        $this->em = $em;

        $this->translator = $this->getMockBuilder(Translator::class)
                                ->disableOriginalConstructor()
                                ->setMethods(['trans'])
                                ->getMock();
        $this->translator->method('trans')->willReturn('a string');

        $this->post = $this->getMockBuilder(Post::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $this->files = [$file];
    }

    public function testUpload()
    {

        $config = [
            'enable' => true,
            'max_size_ko' => 10000,
            'accepted_format' => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/tiff', 'application/pdf'],
            'preview_file' => true
        ];
        $file = $this->getFileMock('C:\Users\MyUser\AppData\Local\Temp\php6BA8.tmp', 'picture_10201115386585046_1320686413_n.jpg', 'image/jpeg');
        $fileUploader = new FileUploader($this->em, $config, $this->translator);

        $test = $fileUploader->upload([$file], $this->post);
        $this->assertNotFalse($test);
    }

    public function testUploadWithNotAcceptedFormat()
    {
        $config = [
            'enable' => true,
            'max_size_ko' => 10000,
            'accepted_format' => ['image/gif', 'image/tiff', 'application/pdf'],
            'preview_file' => true
        ];
        $file = $this->getFileMock('C:\Users\MyUser\AppData\Local\Temp\php6BA8.tmp', 'picture_10201115386585046_1320686413_n.jpg', 'image/jpeg');
        $fileUploader = new FileUploader($this->em, $config, $this->translator);

        $test = $fileUploader->upload([$file], $this->post);
        $this->assertFalse($test); // FILE EXTENSION SHOULD NOT BE ACCEPTED
    }

    public function testUploadWithTwoFilesTooBig()
    {

        $config = [
            'enable' => true,
            'max_size_ko' => 10000,
            'accepted_format' => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/tiff', 'application/pdf'],
            'preview_file' => true
        ];
        $file = $this->getFileMock('C:\Users\MyUser\AppData\Local\Temp\php6BA8.tmp', 'picture_10201115386585046_1320686413_n.jpg', 'image/jpeg', 6294899);

        $fileUploader = new FileUploader($this->em, $config, $this->translator);
        $test = $fileUploader->upload([$file, $file], $this->post);
        $this->assertFalse($test);
    }

    public function testUploadWithInvalidName()
    {

        $config = [
            'enable' => true,
            'max_size_ko' => 10000,
            'accepted_format' => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/tiff', 'application/pdf'],
            'preview_file' => true
        ];
        $file = $this->getFileMock('C:\Users\MyUser\AppData\Local\Temp\php6BA8.tmp', 'picture/10201115386585046_1320686413_n.jpg', 'image/jpeg'); //INVALID ORIGINAL NAME
        $fileUploader = new FileUploader($this->em, $config, $this->translator);

        $test = $fileUploader->upload([$file], $this->post);
        $this->assertFalse($test);
    }




    private function getFileMock($path, $originalName, $mimeType, $size = 629489)
    {
        $file = $this->getMockBuilder(UploadedFile::class)
            ->setConstructorArgs([$path, $originalName , $mimeType, true])
            ->setMethods(['getMimeType', 'getClientOriginalName', 'getError', 'getSize', 'move'])
            ->getMock();

        $movedFile = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath', 'getFilename'])
            ->getMock();

        $movedFile->method('getPath')->willReturn('wf_upload');
        $movedFile->method('getFilename')->willReturn('toto.jpg');

        $file->method('getMimeType')->willReturn($mimeType);
        $file->method('getClientOriginalName')->willReturn($originalName);
        $file->method('getError')->willReturn(false);
        $file->method('getSize')->willReturn($size);
        $file->method('move')->willReturn($movedFile);

        return $file;
    }
}