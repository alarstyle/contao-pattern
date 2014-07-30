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
 * Add callbacks to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('Pattern', 'getVariables');
$GLOBALS['TL_DCA']['tl_module']['config']['onsubmit_callback'][] = array('Pattern', 'saveVariables');
$GLOBALS['TL_DCA']['tl_module']['config']['ondelete_callback'][] = array('Pattern', 'deleteVariables');
$GLOBALS['TL_DCA']['tl_module']['config']['oncopy_callback'][] = array('Pattern', 'copyVariables');


/**
 * Add palette to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['pattern'] = '{title_legend},name,type;{pattern_legend},ptr_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;';


/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['ptr_template'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['ptr_template'],
    'default'                 => '',
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('Pattern', 'getPatternTemplates'),
    'eval'                    => array('includeBlankOption'=>true, 'mandatory'=>true, 'submitOnChange'=>true),
    'sql'                     => "varchar(32) NOT NULL default ''"
);
