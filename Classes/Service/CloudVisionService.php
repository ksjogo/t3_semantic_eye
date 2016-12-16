<?php
namespace Dkd\SemanticEye\Service;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\File;

use Google\Cloud\Vision\VisionClient;

class CloudVisionService extends ImageService
{
    protected $vision = null;

    public function __construct()
    {
        parent::__construct();
        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . GeneralUtility::getFileAbsFileName('EXT:semantic_eye/Resources/Private/Credentials/credentials.json'));
        $this->vision = new VisionClient([
        ]);
    }

    /**
     * @inheritDoc
     */
    public function extractLabel(File $file) {

        // Annotate an image, execute image content analysis.
        $image = $this->vision->image(
            $file->getContents(),
            ['LABEL_DETECTION']
        );

        echo("\nType of image detection: LABEL_DETECTION\n");

        $result = $this->vision->annotate($image);

        return array_map(function($annotation) {
            return [
                'label' => $annotation['description'],
                'score' => $annotation['score']
            ];
        }, $result->info()['labelAnnotations']);
    }

    /**
     * @inheritDoc
     */
    public function extractLandmark(File $file) {

        // Annotate an image, detecting landmarks.
        $image = $this->vision->image(
            $file->getContents(),
            ['LANDMARK_DETECTION']
        );

        $result = $this->vision->annotate($image);

        if (!isset($result->info()['landmarkAnnotations'])) {
            return;
        }

        foreach ($result->info()['landmarkAnnotations'] as $annotation) {
            echo("\nType of image detection: LANDMARK_DETECTION\n");
            echo("mid: $annotation[mid]\n");
            echo("description: $annotation[description]\n");
            echo("score: $annotation[score]\n\n");

            print_r($this->boundingPoly($annotation));

            if (isset($annotation['locations'])) {
                foreach ($annotation['locations'] as $location) {
                    if (isset($location['latLng'])) {
                        $found = $location['latLng'];
                        echo("\nlocation: \nlatitude:$found[latitude]" .
                            "\nlongitude:$found[longitude]\n\n");
                    }
                }
            }
        }

        return array_map(function($annotation) {
            return [
                'description' => $annotation['description'],
                'score' => $annotation['score']
            ];
        }, $result->info()['landmarkAnnotations']);
    }

    /**
     * @inheritDoc
     */
    public function extractFace(File $file) {

        // Annotate an image, detecting faces.
        $image = $this->vision->image(
            $file->getContents(),
            ['FACE_DETECTION']
        );

        echo("\nType of image detection: FACE_DETECTION\n\n");

        $result = $this->vision->annotate($image);

        if (!isset($result->info()['faceAnnotations'])) {
            return;
        }

        foreach ($result->info()['faceAnnotations'] as $annotation) {

            print_r($this->boundingPoly($annotation));

            echo("\n");

            if (isset($annotation['landmarks'])) {
                echo("LANDMARKS:\n");
                foreach ($annotation['landmarks'] as $landmark) {
                    $pos = $landmark['position'];
                    echo("$landmark[type]:\nx:$pos[x]\ty:$pos[y]\tz:$pos[z]\n\n");
                }
            }

            $features = [
                'rollAngle' => 'rollAngle',
                'panAngle' => 'panAngle',
                'tiltAngle' => 'tiltAngle',
                'detectionConfidence' => 'detectionConfidence',
                'landmarkingConfidence' => 'landmarkingConfidence',
                'joyLikelihood' => 'Joy',
                'sorrowLikelihood' => 'Sorrow',
                'angerLikelihood' => 'Anger',
                'surpriseLikelihood' => 'Suprise',
                'underExposedLikelihood' => 'UnderExposed',
                'blurredLikelihood' => 'Blurred',
                'headwearLikelihood' => 'Headwear'
            ];

            echo("FIELDS:\n");

            foreach ($features as $feature => $value) {
                if (isset($annotation[$feature])) {
                    echo("$value:\t$annotation[$feature]\n");
                }
            }
        }

        return array_map(function($annotation) {
            return [
                'Joy' => $annotation['joyLikelihood'],
                'sorrow' => $annotation['sorrowLikelihood'],
                'anger' => $annotation['angerLikelihood'],
                'suprise' => $annotation['surpriseLikelihood'],
                'underExposed' => $annotation['underExposedLikelihood'],
                'blurred' => $annotation['blurredLikelihood'],
                'headwear' => $annotation['headwearLikelihood']
            ];
        }, $result->info()['faceAnnotations']);
    }

    /**
     * @inheritDoc
     */
    public function extractText(File $file) {

        // Annotate an image, detecting text.
        $image = $this->vision->image(
            $file->getContents(),
            ['TEXT_DETECTION']
        );

        echo("\nType of image detection: TEXT_DETECTION\n\n");

        $result = $this->vision->annotate($image);

        if (!isset($result->info()['textAnnotations'])) {
            return;
        }

        foreach ($result->info()['textAnnotations'] as $annotation) {

            print_r($this->boundingPoly($annotation));

            echo("\n");

            if (isset($annotation['locale'])) {
                echo("local: $annotation[locale]\n");
            }

            if (isset($annotation['description'])) {
                echo("description: $annotation[description]\n");
            }
        }

        return array_map(function($annotation) {
            return [
                'locale' => $annotation['locale'],
                'description' => $annotation['description']
            ];
        }, $result->info()['textAnnotations']);
    }

    /**
     * @inheritDoc
     */
    public function extractLogo(File $file)
    {
        // Annotate an image, detecting text.
        $image = $this->vision->image(
            $file->getContents(),
            ['LOGO_DETECTION']
        );

        echo("\nType of image detection: LOGO_DETECTION\n\n");

        $result = $this->vision->annotate($image);

        if (!isset($result->info()['logoAnnotations'])) {
            return;
        }

        foreach ($result->info()['logoAnnotations'] as $annotation) {
            if (isset($annotation['description'])) {
                echo("description: $annotation[description]\n");
            }
            if (isset($annotation['mid'])) {
                echo("mid: $annotation[mid]\n");
            }
            if (isset($annotation['score'])) {
                echo("score: $annotation[score]\n\n");
            }

            $this->boundingPoly($annotation);
        }

        return array_map(function($annotation) {
            return [
                'score' => $annotation['score'],
                'description' => $annotation['description']
            ];
        }, $result->info()['logoAnnotations']);
    }

    /**
     * @inheritDoc
     */
    public function extractSafe(File $file) {

        // Annotate an image, execute image content analysis.
        $image = $this->vision->image(
            $file->getContents(),
            ['SAFE_SEARCH_DETECTION']
        );

        echo("\nType of image detection: SAFE_SEARCH_DETECTION\n");

        $result = $this->vision->annotate($image);

        if (!isset($result->info()['safeSearchAnnotation'])) {
            return;
        }

        foreach ($result->info()['safeSearchAnnotation'] as $annotation) {

            if (isset($annotation['adult'])) {
                echo("adult: $annotation[adult]\n");
            }

            if (isset($annotation['spoof'])) {
                echo("spoof: $annotation[spoof]\n");
            }

            if (isset($annotation['medical'])) {
                echo("medical: $annotation[medical]\n");
            }

            if (isset($annotation['violence'])) {
                echo("violence: $annotation[violence]\n");
            }
         }

            /*
            if(isset ($annotation['adult'])) {
                print ("array exists");
            }

            if(!isset ($annotation['adult'])) {
                print ("array does not exist");
            }
            */

        return array_map(function($annotation) {
            return [
                'adult' => isset($annotation['adult']),
                'spoof' => isset($annotation['spoof']),
                'medical' => isset($annotation['medical']),
                'violence' => isset($annotation['violence'])
            ];
        }, $result->info()['safeSearchAnnotation']);
    }

    public function boundingPoly($annotation) {
        if (isset($annotation['boundingPoly'])) {
            echo("bounding polygon for the detected image annotation:\n");
            foreach ($annotation['boundingPoly']['vertices'] as $vertex) {
                $x[] = isset($vertex['x']) ? $vertex['x'] : '';
                $y[] = isset($vertex['y']) ? $vertex['y'] : '';
                //echo("x:$x\ty:$y\n");
            }
            return array($x,$y);
        }
    }
}