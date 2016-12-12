<?php
namespace Dkd\SemanticEye\Command;
use TYPO3\CMS\Core\Resource\ResourceFactory;

class CommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
{
    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     * @inject
     */
    protected $resourceFactory;
}
