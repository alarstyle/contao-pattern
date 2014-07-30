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
 * Class PatternTemplate
 *
 * Methods are not searching templates in theme folders in this implementation.
 */
class PatternTemplate extends \Contao\FrontendTemplate {

    /**
     * Find a particular template file and return its path
     *
     * @param string $strTemplate The name of the template
     * @param string $strFormat   The file extension
     *
     * @return string The path to the template file
     *
     * @throws \Exception If $strFormat is unknown
     */
    public static function getTemplate($strTemplate, $strFormat='html5')
    {
        $arrAllowed = trimsplit(',', \Config::get('templateFiles'));
        array_push($arrAllowed, 'html5'); // see #3398

        if (!in_array($strFormat, $arrAllowed))
        {
            throw new \Exception("Invalid output format $strFormat");
        }

        $strTemplate = basename($strTemplate);

        //return \TemplateLoader::getPath($strTemplate, $strFormat);

        return \TemplateLoader::getPath($strTemplate, $strFormat);
    }


    /**
     * Return all template files of a particular group as array
     *
     * @param string $strPrefix The template name prefix (e.g. "ce_")
     *
     * @return array An array of template names
     */
    public static function getTemplateGroup($strPrefix)
    {
        $arrTemplates = array();

        // Get the default templates
        foreach (\TemplateLoader::getPrefixedFiles($strPrefix) as $strTemplate)
        {
            $arrTemplates[$strTemplate][] = 'root';
        }

        $arrCustomized = glob(TL_ROOT . '/templates/' . $strPrefix . '*');

        // Add the customized templates
        if (is_array($arrCustomized))
        {
            foreach ($arrCustomized as $strFile)
            {
                $strTemplate = basename($strFile, strrchr($strFile, '.'));
                $arrTemplates[$strTemplate][] = $GLOBALS['TL_LANG']['MSC']['global'];
            }
        }

        // Show the template sources (see #6875)
        foreach ($arrTemplates as $k=>$v)
        {
            $arrTemplates[$k] = $k;
        }

        // Sort the template names
        ksort($arrTemplates);

        return $arrTemplates;
    }


    /**
     * Return pattern variable value
     *
     * @param  string $varName
     *
     * @return string
     */
    public function patternVar($varName)
    {
        if (empty($this->arrData['arrVariables'][$varName]['value']))
        {
            return '';
        }

        $objVar = $this->arrData['arrVariables'][$varName];

        // Convert date values
        switch ($objVar['type'])
        {
            case 'date':
                $objVar['value'] = \Date::parse(\Config::get('dateFormat'), $objVar['value']);
                break;

            case 'time':
                $objVar['value'] = \Date::parse(\Config::get('timeFormat'), $objVar['value']);
                break;

            case 'datim':
                $objVar['value'] = \Date::parse(\Config::get('datimFormat'), $objVar['value']);
                break;
        }

        return $objVar['value'];
    }


    /**
     * Parse the template file and return it as string
     *
     * @return string The template markup
     */
    public function parse()
    {
        $strBuffer = parent::parse();

        $strBuffer = preg_replace('/{{%.*?%}}/s', '', $strBuffer);

        foreach($this->arrData['arrVariables'] as $varName=>$objVar)
        {
            if (empty($objVar['value']))
            {
                continue;
            }

            // Convert date values
            switch ($objVar['type'])
            {
                case 'date':
                    $objVar['value'] = \Date::parse(\Config::get('dateFormat'), $objVar['value']);
                    break;

                case 'time':
                    $objVar['value'] = \Date::parse(\Config::get('timeFormat'), $objVar['value']);
                    break;

                case 'datim':
                    $objVar['value'] = \Date::parse(\Config::get('datimFormat'), $objVar['value']);
                    break;
            }

            $strBuffer = preg_replace('/{%\s*' . $varName . '\s*%}/s', $objVar['value'], $strBuffer);
        }

        $strBuffer = preg_replace('/{%.*?%}/s', '', $strBuffer);

        return $strBuffer;
    }

} 