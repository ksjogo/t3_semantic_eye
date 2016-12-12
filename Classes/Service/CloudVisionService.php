<?php
namespace Dkd\SemanticEye\Service;

class CloudVisionService implements \TYPO3\CMS\Core\SingletonInterface
{

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;

/*
 * Defines CloudVision service
 */
class CloudVisionService implements \TYPO3\CMS\Core\SingletonInterface
{
    //base url of cloud vision api
    protected $endpoint = 'https://vision.googleapis.com/v1/images:annotate?key=';

    //stores api key
    protected $key = 'AIzaSyB4gpnr3CU-qA4L7fuqvoFrCXQLyhAWySo';

    //path to the image
    protected $path = '/Users/dkd-dornburg/Documents/';

    //http client to fetch the feed data with
    protected $client;

    //complete cloud vision base url
    protected $url;

    //cloud vision api feature
    protected $feature;

    //name of the image
    protected $filename ='eiffelturm.jpeg';

    //stores max results number for LABEL_DETECTION
    protected $maxresults;

    //constructor for cloud vision api object
    public function __construct()
    {
        $this->url = $this->endpoint . $this->key;

        //create an instance of guzzle client
        $this->client = new Client();

    }

    /*FIXME:
      Actually call google
      For the moment keep configuration here
      Later move configuration to a configuration service and read from extconf or TS
    */
    public function doSomething(string $argument)
    {
        //echo "CloudVisionService: " . $argument . "\n";

        //create a new guzzle client
        //The client constructor accepts an associative array of options
        //$client = new Client(['base_uri' => 'https://vision.googleapis.com/v1']);

        //create a new vision client
        /*
        $vision = new VisionClient([
            'key' => $apiKey,
        ]);
        */
        //$image = $vision->image(file_get_contents($path), ['LANDMARK_DETECTION']);
        //$result = $vision->annotate($image);

        //create http request
        //vision api -> POST https://vision.googleapis.com/v1/images:annotate
        //guzzle request -> Making a Request -> You can send requests with Guzzle using a GuzzleHttp\ClientInterface object.

        //add a file
        ///Users/dkd-dornburg/Documents/demo-image.bmp
        //$this->filename =  yield $filename => base64_encode(file_get_contents(demo-image.bmp));

        $this->url = $this->endpoint . $this->key;

        //prepare JSON
        $data = [
            'requests' => [
                [
                    'image' => [
                        'content' => $this->path . $this->filename,
                        ],
                    'features' => [
                        [
                            'type' => 'LABEL_DETECTION',
                            'maxResults'=> $this->maxresults,
                        ],
                    ],
                ],
            ],
        ];

        //make request
        $response = $this->guzzle_client->post($this->url,[
            RequestOptions::JSON => $data,
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
        ]);

        //request body
        $body = $response->getBody(true);
        //var_dump($response->json());
        $result = json_decode($body, true);
        print_r($result);

        //get status code
        $status = $response->getStatusCode();
        if($response->getStatusCode()==200) {
            print_r($status);
            //var_dump($status);
        }
        else {
            echo "Error: API cant be reached";
        }
    }
}