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


namespace Pattern;


/**
 * Class ContentPattern
 */
class ContentPattern extends \ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_pattern';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['CTE']['pattern'][0]) . ' ###';
			$objTemplate->title = $this->ptr_template;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
        $objPattern = new \PatternTemplate($this->ptr_template);

        $arrVars = Pattern::loadVariables($this->id, \ContentModel::getTable(), true);

        $objPattern->arrVariables = $arrVars;

        $this->Template->pattern = $objPattern->parse();
	}
}
