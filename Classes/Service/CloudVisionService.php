<?php
namespace Dkd\SemanticEye\Service;

class CloudVisionService implements \TYPO3\CMS\Core\SingletonInterface
{
    /*FIXME:
      Actually call google
      For the moment keep configuration here
      Later move configuration to a configuration service and read from extconf or TS
    */
    public function doSomething(string $argument)
    {
        echo "CloudVisionService: " . $argument . "\n";
    }
}