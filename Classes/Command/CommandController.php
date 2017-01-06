<?php
namespace Dkd\SemanticEye\Command;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\FileReference;


class CommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
{
    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     * @inject
     */
    protected $resourceFactory;

    protected $file;

    protected $defaultStorage;

    protected $folder;

    public function getImages() {
        $resourceFactory = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance();
        //get the Storage; return null if no default Storage exists
        $defaultStorage = $resourceFactory->getDefaultStorage();
        //get a Folder object; path relative to Storage root
        $folder = $defaultStorage->getFolder('CloudVisionAPI');
        //retrieve the files
        $file = $defaultStorage->getFilesInFolder($folder);
        //var_dump($file);
        //$metadata = \TYPO3\CMS\Core\Resource\FileReference::getDescription ();
        //var_dump($metadata);

        return $this->file;
    }
}
