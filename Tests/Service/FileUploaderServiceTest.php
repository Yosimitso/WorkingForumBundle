<?php

namespace Yosimitso\WorkingForumBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Yosimitso\WorkingForumBundle\Service\FileUploaderService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\Translator;
use Yosimitso\WorkingForumBundle\Entity\Post;

/**
 * Class FileUploaderTest
 *
 * @package Yosimitso\WorkingForumBundle\Tests\Util
 */
class FileUploaderServiceTest extends WebTestCase
{
    private $files;
    private $em;
    private $translator;
    private $post;

    public function setUp() : void
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
        $this->translator->method('trans')->willReturn('an error occured');

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
        $fileUploaderService = new FileUploaderService($this->em, $config, $this->translator);

        $test = $fileUploaderService->upload([$file], $this->post);
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
        $fileUploaderService = new FileUploaderService($this->em, $config, $this->translator);

        $this->expectException(\Exception::class); // FILE EXTENSION SHOULD NOT BE ACCEPTED
        $fileUploaderService->upload([$file], $this->post);
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
        $fileUploaderService = new FileUploaderService($this->em, $config, $this->translator);

        $this->expectException(\Exception::class);
        $fileUploaderService->upload([$file, $file], $this->post);
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
        $fileUploaderService = new FileUploaderService($this->em, $config, $this->translator);

        $this->expectException(\Exception::class);
        $test = $fileUploaderService->upload([$file], $this->post);
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