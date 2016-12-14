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

        print("\nType of image detection: LABEL_DETECTION\n");

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
            print("\nType of image detection: LANDMARK_DETECTION\n");
            print("mid: $annotation[mid]\n");
            print("description: $annotation[description]\n");
            print("score: $annotation[score]\n\n");

            $this->boundingPoly($annotation);

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

        print("\nType of image detection: FACE_DETECTION\n\n");

        $result = $this->vision->annotate($image);

        if (!isset($result->info()['faceAnnotations'])) {
            return;
        }

        foreach ($result->info()['faceAnnotations'] as $annotation) {

            $this->boundingPoly($annotation);

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

        return array_map(function($annotation) {
            return [
                'Joy' => $annotation['joyLikelihood'],
                'sorrow' => $annotation['sorrowLikelihood'],
                'anger' => $annotation['angerLikelihood'],
                'suprise' => $annotation['supriseLikelihood'],
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

        print("\nType of image detection: TEXT_DETECTION\n\n");

        $result = $this->vision->annotate($image);

        if (!isset($result->info()['textAnnotations'])) {
            return;
        }

        foreach ($result->info()['textAnnotations'] as $annotation) {

            $this->boundingPoly($annotation);

            print("\n");

            if (isset($annotation['locale'])) {
                print("local: $annotation[locale]\n");
            }

            if (isset($annotation['description'])) {
                print("description: $annotation[description]\n");
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

        print("\nType of image detection: LOGO_DETECTION\n\n");

        $result = $this->vision->annotate($image);

        if (!isset($result->info()['logoAnnotations'])) {
            return;
        }

        foreach ($result->info()['logoAnnotations'] as $annotation) {
            if (isset($annotation['description'])) {
                print("description: $annotation[description]\n");
            }
            if (isset($annotation['mid'])) {
                print("mid: $annotation[mid]\n");
            }
            if (isset($annotation['score'])) {
                print("score: $annotation[score]\n\n");
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

        print("\nType of image detection: SAFE_SEARCH_DETECTION\n");

        $result = $this->vision->annotate($image);

        if (!isset($result->info()['safeSearchAnnotation'])) {
            return;
        }

        foreach ($result->info()['safeSearchAnnotation'] as $annotation) {

            if (isset($annotation['adult'])) {
                print("adult: $annotation[adult]\n");
            }

            if (isset($annotation['spoof'])) {
                print("spoof: $annotation[spoof]\n");
            }

            if (isset($annotation['medical'])) {
                print("medical: $annotation[medical]\n");
            }

            if (isset($annotation['violence'])) {
                print("violence: $annotation[violence]\n");
            }
        }

        /*
        return array_map(function($annotation) {
            return [
                'adult' => $annotation['adult'],
                'violence' => $annotation['violence']
            ];
        }, $result->info()['safeSearchAnnotation']);
        */
    }

    public function boundingPoly($annotation) {
        if (isset($annotation['boundingPoly'])) {
            print("bounding polygon for the detected image annotation:\n");
            foreach ($annotation['boundingPoly']['vertices'] as $vertex) {
                $x = isset($vertex['x']) ? $vertex['x'] : '';
                $y = isset($vertex['y']) ? $vertex['y'] : '';
                print("x:$x\ty:$y\n");
            }
        }
    }
}