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
 * Register namespaces
 */
ClassLoader::addNamespaces(array('Pattern'));


/**
 * Register classes
 */
ClassLoader::addClasses(array
(
    // Classes
    'Pattern\Pattern'           => 'system/modules/pattern/classes/Pattern.php',
    'Pattern\PatternTemplate'        => 'system/modules/pattern/classes/PatternTemplate.php',

    // Models
    'Pattern\VariableModel'     => 'system/modules/pattern/models/VariableModel.php',

    // Elements
	'Pattern\ContentPattern'    => 'system/modules/pattern/elements/ContentPattern.php',

    // Modules
	'Pattern\ModulePattern'     => 'system/modules/pattern/modules/ModulePattern.php',
));

/**
 * Register templates
 */
TemplateLoader::addFiles(array
(
    'ce_pattern'      => 'system/modules/pattern/templates',
    'mod_pattern'      => 'system/modules/pattern/templates',
));

