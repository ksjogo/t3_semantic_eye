<?php
namespace Dkd\SemanticEye\Service;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use GuzzleHttp\Client;
use TYPO3\CMS\Core\Resource\File;

abstract class ImageService implements \TYPO3\CMS\Core\SingletonInterface
{
    //potential Guzzle REST Client
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Extracts concepts for the given file using some remote API
     *
     * @param \TYPO3\CMS\Core\Resource\File $file FileObject to be annotated
     * @return array Sorted array of arrays containing 'label' and 'score' values
     */
    //abstract public function extractConcepts(File $file);

    //abstract public function extractlandmark(File $file);

    abstract public function extractface(File $file);

    //abstract public function extracttext(File $file);

    //abstract public function extractlogo(File $file);

    //abstract public function extractSafe(File $file);

}