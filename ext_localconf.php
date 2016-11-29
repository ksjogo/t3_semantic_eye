<?php
if (!defined('TYPO3_MODE'))
	die('Access denied.');

/*
if(isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers']) == false) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'] = array();
}

if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
        \vendor\Semantic_eye\Command\CommandControllerClass::class;
}
*/

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Dkd\SemanticEye\Command\CommandControllerClass::class;
