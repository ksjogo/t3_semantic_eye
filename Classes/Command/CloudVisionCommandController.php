<?php
namespace Dkd\SemanticEye\Command;

use \Dkd\SemanticEye\Service\CloudVision;

class CloudVisionCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
{

    /**
     * @var \Dkd\SemanticEye\Service\CloudVisionService
     * @inject
     */
    protected $cloudVision;

    /*FIXME:
      collect test arguments from cli
      pass arguments to the actual service
      better naming for calls
    */
    public function testCommand()
    {
        $this->cloudVision->doSomething("arguments");
    }
}