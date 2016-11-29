<?php
/**
 * Created by PhpStorm.
 * User: dkd-dornburg
 * Date: 29.11.16
 * Time: 18:05
 */


namespace \Dkd\SemanticEye\Command;

use \TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

class CommandControllerClass extends CommandController
{

    public function testCommand()
    {
        echo "TEST";
    }

}