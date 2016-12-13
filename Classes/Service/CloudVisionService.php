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

        //Constructing the Request
        //$image = $vision->image(file_get_contents($path), ['LANDMARK_DETECTION']);
        //$result = $vision->annotate($image);

        // Annotate an image, execute image content analysis.
        $image = $vision->image(
            $file->getContents(),
            ['LABEL_DETECTION']
        );

        print("\nType of image detection: LABEL_DETECTION\n");

        $result = $vision->annotate($image);


        return array_map(function($annotation) {
            return [
                "label" => $annotation['description'],
                "score" => $annotation['score']
            ];
        }, $result->info()['labelAnnotations']);
    }

    /**
     * @inheritDoc
     */
    public function extractlandmark(File $file) {

        $vision = new VisionClient([
        ]);

        //Constructing the Request
        //$image = $vision->image(file_get_contents($path), ['LANDMARK_DETECTION']);
        //$result = $vision->annotate($image);

        // Annotate an image, detecting landmarks.
        $image = $vision->image(
            $file->getContents(),
            ['LANDMARK_DETECTION']
        );

        $result = $vision->annotate($image);


        if (!isset($result->info()['landmarkAnnotations'])) {
            return;
        }


        foreach ($result->info()['landmarkAnnotations'] as $annotation) {
            print("\nType of image detection: LANDMARK_DETECTION\n");
            print("mid: $annotation[mid]\n");
            print("description: $annotation[description]\n");
            print("score: $annotation[score]\n\n");
            if (isset($annotation['boundingPoly'])) {
                print("bounding polygon for the detected image annotation:\n");
                foreach ($annotation['boundingPoly']['vertices'] as $vertex) {
                    $x = isset($vertex['x']) ? $vertex['x'] : '';
                    $y = isset($vertex['y']) ? $vertex['y'] : '';
                    print("x:$x\ty:$y\n");
                }
            }
            if (isset($annotation['locations'])) {
                foreach ($annotation['locations'] as $location) {
                    if (isset($location['latLng'])) {
                        $found = $location['latLng'];
                        print("\nlocation: \nlatitude:$found[latitude]" .
                            "\nlongitude:$found[longitude]\n\n");
                    }
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function extractface(File $file) {

        $vision = new VisionClient([
        ]);

        //Constructing the Request
        //$image = $vision->image(file_get_contents($path), ['LANDMARK_DETECTION']);
        //$result = $vision->annotate($image);

        // Annotate an image, detecting faces.
        $image = $vision->image(
            $file->getContents(),
            ['FACE_DETECTION']
        );

        print("\nType of image detection: FACE_DETECTION\n\n");

        $result = $vision->annotate($image);

        if (!isset($result->info()['faceAnnotations'])) {
            return;
        }

        foreach ($result->info()['faceAnnotations'] as $annotation) {
            if (isset($annotation['boundingPoly'])) {
                print("bounding polygon for the detected image annotation:\n");
                foreach ($annotation['boundingPoly']['vertices'] as $vertex) {
                    $x = isset($vertex['x']) ? $vertex['x'] : '';
                    $y = isset($vertex['y']) ? $vertex['y'] : '';
                    print("x:$x\ty:$y\n");
                }
            }
            print("\n");
            if (isset($annotation['landmarks'])) {
                print("LANDMARKS:\n");
                foreach ($annotation['landmarks'] as $landmark) {
                    $pos = $landmark['position'];
                    print("$landmark[type]:\nx:$pos[x]\ty:$pos[y]\tz:$pos[z]\n\n");
                }
            }

            $features = [
                'rollAngle',
                'panAngle',
                'tiltAngle',
                'detectionConfidence',
                'landmarkingConfidence',
                'joyLikelihood',
                'sorrowLikelihood',
                'angerLikelihood',
                'surpriseLikelihood',
                'underExposedLikelihood',
                'blurredLikelihood',
                'headwearLikelihood'
            ];

            print("FIELDS:\n");

            foreach ($features as $feature) {
                if (isset($annotation[$feature])) {
                    switch ($feature) {
                        case 'rollAngle':
                            print("rollAngle:\t$annotation[$feature]\n");
                            break;
                        case 'panAngle':
                            print("panAngle:\t$annotation[$feature]\n");
                            break;
                        case 'tiltAngle':
                            print("tiltAngle:\t$annotation[$feature]\n");
                            break;
                        case 'detectionConfidence':
                            print("detectionConfidence:\t$annotation[$feature]\n");
                            break;
                        case 'landmarkingConfidence':
                            print("landmarkingConfidence:\t$annotation[$feature]\n");
                            break;
                        case 'joyLikelihood':
                            print("Joy:\t$annotation[$feature]\n");
                            break;
                        case 'sorrowLikelihood':
                            print("sorrow:\t$annotation[$feature]\n");
                            break;
                        case 'angerLikelihood':
                            print("anger:\t$annotation[$feature]\n");
                            break;
                        case 'supriseLikelihood':
                            print("suprise:\t$annotation[$feature]\n");
                            break;
                        case 'underExposedLikelihood':
                            print("underExposed:\t$annotation[$feature]\n");
                            break;
                        case 'blurredLikelihood':
                            print("blurred:\t$annotation[$feature]\n");
                            break;
                        case 'headwearLikelihood':
                            print("headwear:\t$annotation[$feature]\n");
                            break;
                        default:
                            //print("$feature:\t$annotation[$feature]\n");
                    }
                }
            }
        }
    }
}