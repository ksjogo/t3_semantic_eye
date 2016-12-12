<?php
namespace Dkd\SemanticEye\Command;

use \Dkd\SemanticEye\Service\CloudVision;
use TYPO3\CMS\Core\Resource\ResourceFactory;

class CloudVisionCommandController extends CommandController
{
    /**
     * @var \Dkd\SemanticEye\Service\CloudVisionService
     * @inject
     */
    protected $cloudVision;

    /**
     * Get concepts for image
     *
     * Uses Google CloudVisionService to get concepts.
     *
     * @param string $filename filename of the image - EXT: is honoured.
     * @return void
     */
    public function conceptsCommand(string $filename = NULL)
    {
        //FIXME: should be @inject, why is that not working?
        if (!$this->resourceFactory)
            $this->resourceFactory = $this->objectManager->get(ResourceFactory::class);

        if(!$filename)
            $filename = 'EXT:semantic_eye/Resources/Private/TestImages/1.png';

        $file = $this->resourceFactory->retrieveFileOrFolderObject($filename);
        $result = $this->cloudVision->extractConcepts($file);
        print_r($result);
    }
}