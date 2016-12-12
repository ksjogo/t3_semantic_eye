<?php
namespace Dkd\SemanticEye\Service;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\File;

use Google\Cloud\Vision\VisionClient;

class CloudVisionService extends ImageService
{
    protected $endpoint = 'https://vision.googleapis.com/v1/images:annotate';
    protected $maxresults;

    public function __construct()
    {
        parent::__construct();
        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . GeneralUtility::getFileAbsFileName('EXT:semantic_eye/Resources/Private/Credentials/credentials.json'));
    }

    /**
     * @inheritDoc
     */
    public function extractConcepts(File $file) {

        $vision = new VisionClient([
        ]);

        // Annotate an image, detecting faces.
        $image = $vision->image(
            $file->getContents(),
            ['LABEL_DETECTION']
        );

        $result = $vision->annotate($image);

        return array_map(function($annotation) {
            return [
                "label" => $annotation['description'],
                "score" => $annotation['score']
            ];
        }, $result->info()['labelAnnotations']);
    }
}