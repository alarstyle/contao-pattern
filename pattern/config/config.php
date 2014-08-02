<?php

/**
 * Pattern for Contao Open Source CMS
 *
 * Copyright (C) 2014 Alexander Stulnikov
 *
 * @package    Pattern
 * @link       https://github.com/alarstyle/contao-pattern
 * @license    http://opensource.org/licenses/MIT
 */


/**
 * Add content element
 */
$GLOBALS['TL_CTE']['includes']['pattern'] = 'ContentPattern';


/**
 * Backend form fields
 */
$GLOBALS['BE_FFL']['patternContent']      = 'Pattern\PatternContent';


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['miscellaneous']['pattern'] = 'ModulePattern';


/**
 * Models
 */
$GLOBALS['TL_MODELS'][\Pattern\VariableModel::getTable()]    = 'Pattern\VariableModel';


/**
 * Backend only
 */
if (TL_MODE == 'BE')
{
    /**
     * Hooks
     */
    $GLOBALS['TL_HOOKS']['initializeSystem'][]  = array('Pattern', 'initializeSystem');
    $GLOBALS['TL_HOOKS']['reviseTable'][]       = array('Pattern', 'reviseTable');
}
