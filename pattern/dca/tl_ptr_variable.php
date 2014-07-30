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
 * Table tl_ptr_variable
 */
$GLOBALS['TL_DCA']['tl_ptr_variable'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		//'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
                'pid' => 'index'
			)
		)
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
        'pid' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
        'ptable' => array
        (
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'type' => array
        (
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'name' => array
        (
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
		'text' => array
		(
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'textarea' => array
        (
            'sql'                     => "text NULL"
        ),
        'html' => array
        (
            'sql'                     => "mediumtext NULL"
        ),
        'file' => array
        (
            'sql'                     => "binary(16) NULL"
        ),
        'folder' => array
        (
            'sql'                     => "binary(16) NULL"
        ),
        'checkbox' => array
        (
            'sql'                     => "char(1) NOT NULL default ''"
        ),
        'date' => array(
            'sql'                     => "int(10) unsigned NULL"
        ),
        'time' => array(
            'sql'                     => "int(10) unsigned NULL"
        ),
        'datetime' => array(
            'sql'                     => "varchar(10) NOT NULL default ''"
        ),
        'color' => array(
            'sql'                     => "varchar(10) NOT NULL default ''"
        )
	)
);
