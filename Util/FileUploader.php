<?php

namespace Yosimitso\WorkingForumBundle\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Yosimitso\WorkingForumBundle\Entity\File;

/**
 * Class FileUploader
 * @package Yosimitso\WorkingForumBundle\Util
 * Handle file upload system
 */
class FileUploader
{
    private $path;
    private $em;
    private $configFileUpload;
    private $translator;
    private $errorMessage;

    public function __construct($em, $configFileUpload, $translator)
    {
        $this->path = 'wf_uploads/'.date('Y/m/');
        $this->em = $em;
        $this->configFileUpload = $configFileUpload;
        $this->translator = $translator;
    }

    /**
     * @param array $filesSubmitted
     * @param $post
     * @return array|bool
     * Upload submitted files on server
     */
    public function upload(array $filesSubmitted, $post)
    {
        $this->errorMessage = '';
        $fileList = [];
        $totalSize = 0;

        foreach ($filesSubmitted as $fileSubmitted) {
            $totalSize += $fileSubmitted->getSize() / 1000;
        }
        $maxSize = $this->getMaxSize();
        if ($totalSize > $maxSize) {
            $this->errorMessage = $this->translator->trans(
                'forum.file_upload.error.max_size_exceeded',
                ['%max_size%' => $maxSize],
                'YosimitsoWorkingForumBundle'
            );
            return false;
        }
        

        foreach ($filesSubmitted as $fileSubmitted) {
            if ($fileSubmitted->getError()) {

                $this->errorMessage = $this->translator->trans(
                    'forum.file_upload.error.default',
                    [],
                    'YosimitsoWorkingForumBundle'
                );
                return false;
            }

            if (!in_array($fileSubmitted->getMimeType(), $this->configFileUpload['accepted_format'])) {
                $this->errorMessage = $this->translator->trans(
                    'forum.file_upload.error.invalid_format',
                    ['%format%' => $fileSubmitted->getMimeType()],
                    'YosimitsoWorkingForumBundle'
                );
                return false;
            }

            $file = new File;
            $originalFilename = [];
            preg_match('/^([A-z0-9_-]+?)\.[A-z]+/', $fileSubmitted->getClientOriginalName(), $originalFilename);
            if (!isset($originalFilename[1])) { // FILENAME IS INVALID
                $this->errorMessage = $this->translator->trans(
                    'forum.file_upload.error.invalid_filename',
                    ['%filename%' => $originalFilename],
                    'YosimitsoWorkingForumBundle'
                );
                return false;
            }

            $filename = htmlentities(substr($originalFilename[1], 0, 10));
            $file->setFilename(md5(uniqid()).'-'.$filename.'.'.$fileSubmitted->guessExtension()); // UNIQUE FILENAME
            $file->setOriginalName($originalFilename[1].'.'.$fileSubmitted->guessExtension()); // DON'T USE THE EXTENSION PROVIDED BY THE USER
            $file->setExtension($fileSubmitted->guessExtension());
            $file->setSize($fileSubmitted->getSize());

            try { // UPLOAD ON SERVER
                $fileUploaded = $fileSubmitted->move($this->path, $file->getFilename());
                $file->setPath($fileUploaded->getPath().'/'.$fileUploaded->getFilename());
                $file->setPost($post);
                $this->em->persist($file);
                $fileList[] = $file;
            } catch (\Exception $e) {
                $this->errorMessage = $this->translator->trans(
                    'forum.file_upload.error.default',
                    [],
                    'YosimitsoWorkingForumBundle'
                );
                return false;
            }
        }

        $this->em->flush();

        return $fileList;
    }

    /**
     * @return string
     * Get latest error message if upload failed
     */
    public function getErrorMessage()
    {
        if (!empty($this->errorMessage)) {
            return $this->errorMessage;
        } else {
            return false;
        }
    }

    /**
     * @return float
     * Determine the max size allowed, the "max size file upload" parameter in application config can't be superior to PHP config
     */
    public function getMaxSize()
    {
        $uploadMaxFilesize = $this->extractSize(ini_get('upload_max_filesize'));
        $uploadPostMaxsize = $this->extractSize(ini_get('post_max_size'));

        return (($this->configFileUpload['max_size_ko'] > intval($uploadMaxFilesize))
            || ($this->configFileUpload['max_size_ko'] > intval( $uploadPostMaxsize)))
            ? min([intval($uploadMaxFilesize), intval($uploadPostMaxsize)]) // THE APPLICATION MAX SIZE EXCEEDS PHP INI CONFIGURATION
            : $this->configFileUpload['max_size_ko']; // THE APPLICATION MAX SIZE VALUE IS OK
    }

    private function extractSize($value)
    {
        preg_match('/([0-9]+)([A-Z]?)/', $value, $sizeRegex);
        if (isset($sizeRegex[2])) {
            switch ($sizeRegex[2]) {
                case 'K':
                    $size = intval($sizeRegex[1])*100;
                    break;
                case 'M':
                    $size = intval($sizeRegex[1])*1000;
                    break;
                case 'G':
                    $size = intval($sizeRegex[1])*10000;
                    break;
                default: 
                    $size =  ini_get('upload_max_filesize');
            }
        } else {
            $size =  ini_get('upload_max_filesize');
        }
        return $size;
    }

}