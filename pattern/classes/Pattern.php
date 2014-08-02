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
 * Class Pattern
 */
class Pattern extends \Controller
{

    /**
     * Storing parsed templates data
     */
    protected static $templatesDataCache = array();

    /**
     * Storing variables array for backend editing
     */
    protected static $arrVariables = array();


    /**
     * Current table (content or module)
     */
    protected static $table;


    /**
     * true if current page need to be proceed with pattern methods
     */
    protected static $isEnabled = false;


    /**
     * Get variable from array by field name
     *
     * @param  string     $strField
     *
     * @return array|null
     */
    protected function getVariableByField($strField) {
        foreach (static::$arrVariables as $arrVar)
        {
            if ($arrVar['field'] === $strField)
            {
                return $arrVar;
            }
        }
        return null;
    }


    /**
     * Create field name from variable name
     * @param $strName
     * @return string
     */
    protected function varNameToFieldName($strName)
    {
        return 'ptrvar_' . standardize($strName);
    }


    /**
     * @return bool
     */
    protected function isContent()
    {
        return static::$table === \ContentModel::getTable();
    }


    /**
     * @return bool
     */
    protected function isModule()
    {
        return static::$table === \ModuleModel::getTable();
    }


    /**
     * Load variables from database
     *
     * @param  string  $pid
     * @param  string  $ptable
     * @param  boolean $forOutput If set to true, values will be converted to output format
     *
     * @return array
     */
    public static function loadVariables($pid, $ptable, $forOutput = false)
    {
        global $objPage;

        $arrVars = array();

        $objVars = VariableModel::findBy(array('pid=?', 'ptable=?'), array($pid, $ptable));

        if (empty($objVars))
        {
            return $arrVars;
        }

        while ($objVars->next())
        {
            $arrVars[$objVars->name]['id'] = $objVars->id;
            $arrVars[$objVars->name]['tstamp'] = $objVars->tstamp;
            $arrVars[$objVars->name]['pid'] = $objVars->pid;
            $arrVars[$objVars->name]['ptable'] = $objVars->ptable;
            $arrVars[$objVars->name]['type'] = $objVars->type;

            // If type was changed get value from new type of variable
            $type = static::$arrVariables[$objVars->name]['type'] ? static::$arrVariables[$objVars->name]['type'] : $objVars->type;

            switch($type)
            {
                case 'text':
                    $value = $objVars->text;
                    break;

                case 'textarea':
                    $value = $objVars->textarea;
                    break;

                case 'wysiwyg':
                case 'html':
                    $value = $objVars->html;
                    break;

                case 'checkbox':
                    $value = $objVars->checkbox;
                    break;

                case 'image':
                case 'file':
                    $value = $objVars->file;
                    break;

                case 'folder':
                    $value = $objVars->folder;
                    break;

                case 'date':
                    $value = $objVars->date;
                    break;

                case 'time':
                    $value = $objVars->time;
                    break;

                case 'datetime':
                    $value = $objVars->datetime;
                    break;

                case 'color':
                    $value = $objVars->color;
                    break;

                default:
                    $value = null;
            }

            // Prepare variables for output
            if ($forOutput) {
                switch ($type)
                {
                    case 'textarea':
                        $value = nl2br($value);
                        if ($objPage->outputFormat === 'xhtml') {
                            $value = \String::toXhtml($value);
                        }
                        else {
                            $value = \String::toHtml5($value);
                        }
                        break;

                    case 'image':
                    case 'file':
                    case 'folder':
                        $objModel = \FilesModel::findByUuid($value);
                        $value = $objModel->path;
                        break;
                }
            }

            $arrVars[$objVars->name]['value']  = $value;
        }

        return $arrVars;
    }


    /**
     * Return all pattern templates as array
     *
     * @return array
     */
    public function getPatternTemplates()
    {
        $arrTemplates = \PatternTemplate::getTemplateGroup('ptr_');
        foreach ($arrTemplates as $k=>$strTplName)
        {
            $arrData = $this->getDataFromTemplate($strTplName);
            if (!empty($arrData['label']))
            {
                $arrTemplates[$k] = $arrData['label'];
            }
        }
        return $arrTemplates;
    }


    /**
     * Generate DCA for variables
     * Called on initializeSystem hook
     */
    public function initializeSystem()
    {
        static::$table = \Input::get('table');

        static::$isEnabled = ($this->isContent() || $this->isModule()) && (\Input::get('act') === 'edit' || TL_SCRIPT === 'contao/file.php');

        if (!static::$isEnabled)
        {
            return;
        }

        // Authenticate user to get backend language
        $this->import('BackendUser', 'User');
        $this->User->authenticate();

        $objElement = $this->isContent() ? \ContentModel::findByPk(\Input::get('id')) : \ModuleModel::findByPk(\Input::get('id'));

        if (empty($objElement) || $objElement->type != 'pattern' || empty($objElement->ptr_template))
        {
            return;
        }

        $arrData = $this->getDataFromTemplate($objElement->ptr_template);

        static::$arrVariables = $arrData['variables'];

        if (empty(static::$arrVariables))
        {
            return;
        }

        \Controller::loadDataContainer(static::$table);

        $strFileds = '';

        foreach (static::$arrVariables as $objVar)
        {
            $strFileds .= ',' . $objVar['field'];
            $GLOBALS['TL_DCA'][static::$table]['fields'][$objVar['field']] = array
            (
                'label'                 => $objVar['label'],
                'inputType'             => $objVar['inputType'],
                'eval'                  => $objVar['eval'],
                'explanation'           => $objVar['explanation'],
                'save_callback'         => array(array('Pattern', 'preventFieldSaving')),
                'load_callback'         => array(array('Pattern', 'setVariable')),
            );
        }

        if (!empty($strFileds))
        {
            $strFileds = ';{pattern_data_legend}' . $strFileds;
        }

        $strPalette = ($this->isContent() ? '{type_legend},type;{pattern_legend},ptr_template' : '{title_legend},name,type;{pattern_legend},ptr_template')
            . $strFileds
            . ';{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

        $GLOBALS['TL_DCA'][static::$table]['palettes']['pattern'] = $strPalette;
    }


    /**
     * Parse template file
     * @param $strTemplate
     * @return array
     */
    public function getDataFromTemplate($strTemplate)
    {
        if (!empty(static::$templatesDataCache[$strTemplate]))
        {
            return static::$templatesDataCache[$strTemplate];
        }

        try
        {
            $strPath = \PatternTemplate::getTemplate($strTemplate);
        }
        catch (\Exception $e)
        {
            return null;
        }

        preg_match('/{{%.*?%}}/s', file_get_contents($strPath), $strData);

        $arrData = json_decode(str_replace(array('{{%','%}}'), array('{','}'), $strData[0]), true);

        if (empty($arrData))
        {
            return $arrData;
        }

        if (!empty($arrData['label']) && is_array($arrData['label']))
        {
            if (!empty($arrData['label'][$GLOBALS['TL_LANGUAGE']]))
            {
                $arrData['label'] = $arrData['label'][$GLOBALS['TL_LANGUAGE']];
            }
            elseif (!empty($arrData['label']['en']))
            {
                $arrData['label'] = $arrData['label']['en'];
            }
            else {
                $arrData['label'] = $strTemplate;
            }
        }

        if (empty($arrData['variables']))
        {
            return $arrData;
        }

        $arrVariables = array();

        foreach ($arrData['variables'] as $k=>$arrVar)
        {
            if (is_string($arrVar))
            {
                $arrVar = array("type" => $arrVar);
            }

            switch($arrVar['type'])
            {
                case 'text':
                    $arrVariables[$k]['inputType'] = 'text';
                    break;

                case 'textarea':
                    $arrVariables[$k]['inputType'] = 'textarea';
                    break;

                case 'wysiwyg':
                    $arrVariables[$k]['inputType'] = 'textarea';
                    $arrVariables[$k]['eval'] = array('rte'=>'tinyMCE', 'doNotSaveEmpty'=>true);
                    break;

                case 'html':
                    $arrVariables[$k]['inputType'] = 'textarea';
                    $arrVariables[$k]['eval'] = array('allowHtml'=>true, 'class'=>'monospace', 'rte'=>'ace|html');
                    break;

                case 'checkbox':
                    $arrVariables[$k]['inputType'] = 'checkbox';
                    break;

                case 'image':
                    $arrVariables[$k]['inputType'] = 'fileTree';
                    $arrVariables[$k]['eval'] = array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>\Config::get('validImageTypes'));
                    break;

                case 'file':
                    $arrVariables[$k]['inputType'] = 'fileTree';
                    $arrVariables[$k]['eval'] = array('fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true);
                    if (!empty($arrVar['extensions']))
                    {
                        $arrVariables[$k]['eval']['extensions'] = $arrVar['extensions'];
                    }
                    break;

                case 'folder':
                    $arrVariables[$k]['inputType'] = 'fileTree';
                    $arrVariables[$k]['eval'] = array('fieldType'=>'radio');
                    break;

                case 'date':
                    $arrVariables[$k]['inputType'] = 'text';
                    $arrVariables[$k]['eval'] = array('rgxp'=>'date', 'datepicker'=>true, 'tl_class'=>'wizard');
                    break;

                case 'time':
                    $arrVariables[$k]['inputType'] = 'text';
                    $arrVariables[$k]['eval'] = array('rgxp'=>'time', 'datepicker'=>true, 'tl_class'=>'wizard');
                    break;

                case 'datetime':
                    $arrVariables[$k]['inputType'] = 'text';
                    $arrVariables[$k]['eval'] = array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'wizard');
                    break;

                case 'color':
                    $arrVariables[$k]['inputType'] = 'text';
                    $arrVariables[$k]['eval'] = array('maxlength'=>6, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'wizard');
                    break;

                default:
                    continue 2;
            }

            $arrVariables[$k]['type'] = $arrVar['type'];

            if ($arrVar['mandatory'])
            {
                $arrVariables[$k]['eval']['mandatory'] = true;
            }

            if ($arrVar['class'])
            {
                $arrVariables[$k]['eval']['tl_class'] = empty($arrVariables[$k]['eval']['tl_class']) ? $arrVar['class'] : $arrVariables[$k]['eval']['tl_class'] . ' ' . $arrVar['class'];
            }


            $keys = array_keys(is_array($arrVar['label']) ? $arrVar['label'] : array());

            // Parse variable's label
            // no label
            if (empty($arrVar['label']))
            {
                $arrVar['label'] = array($k, '');
            }
            // label: "Title"
            elseif (!is_array($arrVar['label']))
            {
                $arrVar['label'] = array($arrVar['label'], '');
            }
            //label: ["Title", "Description"]
            elseif (is_int($keys[0]))
            {
                // do nothing
            }
            elseif (!empty($arrVar['label'][$GLOBALS['TL_LANGUAGE']]))
            {
                $arrVar['label'] = $arrVar['label'][$GLOBALS['TL_LANGUAGE']];
            }
            elseif (!empty($arrVar['label']['en']))
            {
                $arrVar['label'] = $arrVar['label']['en'];
            }

            $arrVariables[$k]['label'] = $arrVar['label'];

            $arrVariables[$k]['field'] = $this->varNameToFieldName($k);
        }

        $arrData['variables'] = $arrVariables;

        static::$templatesDataCache[$strTemplate] = $arrData;

        return $arrData;
    }


    /**
     * Prevent saving variables to content or module database tables.
     * Called on save_callback of fields.
     *
     * @param $varValue
     * @param $dc
     *
     * @return null
     */
    public function preventFieldSaving($varValue, $dc)
    {
        return null;
    }


    /**
     * Set variable value.
     * Called on load_callback of field.
     *
     * @param $varValue
     * @param $dc
     *
     * @return null
     */
    public function setVariable($varValue, $dc)
    {
        if ($_POST || empty(static::$arrVariables))
        {
            return null;
        }

        $arrVar = $this->getVariableByField($dc->field);

        return $arrVar['value'];
    }


    /**
     * Load variables' ids and values from database
     * Called on onload_callback of table
     *
     * @param $dc
     */
    public function getVariables($dc)
    {
        if (empty(static::$arrVariables))
        {
            return;
        }

        $arrVars = static::loadVariables(\Input::get('id'), static::$table);

        foreach($arrVars as $strVarName=>$objVariable)
        {
            static::$arrVariables[$strVarName]['id']        = $objVariable['id'];
            static::$arrVariables[$strVarName]['value']     = $objVariable['value'];
            static::$arrVariables[$strVarName]['tstamp']    = $objVariable['tstamp'];
        }
    }


    public function saveVariables($dc)
    {
        if (!static::$isEnabled || $_POST['type'] !== 'pattern' || empty(static::$arrVariables))
        {
            return;
        }

        foreach (static::$arrVariables as $strVarName=>$objVariable)
        {
            // Skip if variable was removed from the template
            /*if(\Input::post($objVariable['field']) === null)
            {
                continue;
            }*/
            $newValue = \Input::post($objVariable['field']);

            switch ($objVariable['type'])
            {
                case 'text':
                    $strDbField = 'text';
                    break;

                case 'textarea':
                    $strDbField = 'textarea';
                    break;

                case 'wysiwyg':
                case 'html':
                    $strDbField = 'html';
                    $newValue = \Input::stripTags($_POST[$objVariable['field']], \Config::get('allowedTags'));
                    break;

                case 'checkbox':
                    $strDbField = 'checkbox';
                    break;

                case 'image':
                case 'file':
                    $strDbField = 'file';
                    $newValue = \String::uuidToBin($newValue);
                    break;

                case 'folder':
                    $strDbField = 'folder';
                    $newValue = \String::uuidToBin($newValue);
                    break;

                case 'date':
                    $strDbField = 'date';
                    if (!empty($newValue))
                    {
                        $objDate = new \Date($newValue, \Config::get('dateFormat'));
                        $newValue = $objDate->tstamp;
                    }
                    break;

                case 'time':
                    $strDbField = 'time';
                    if (!empty($newValue))
                    {
                        $objDate = new \Date($newValue, \Config::get('timeFormat'));
                        $newValue = $objDate->tstamp;
                    }
                    break;

                case 'datetime':
                    $strDbField = 'datetime';
                    if (!empty($newValue))
                    {
                        $objDate = new \Date($newValue, \Config::get('datimFormat'));
                        $newValue = $objDate->tstamp;
                    }
                    break;

                case 'color':
                    $strDbField = 'color';
                    break;

                default:
                    continue 2;
            }

            // Create new variable
            if (empty($objVariable['id']) && strlen($newValue) > 0)
            {
                $var = new VariableModel();
                $var->pid = \Input::get('id');
                $var->ptable = static::$table;
                $var->type = $objVariable['type'];
                $var->name = $strVarName;
                $var->$strDbField = $newValue;
                $var->tstamp = time();
                $var->save();
            }
            // Update variable
            elseif (!empty($objVariable['id']) && strlen($newValue) > 0 && ($objVariable['value'] !== (string)$newValue || $objVariable['tstamp'] === 0))
            {
                VariableModel::updateVariable($objVariable['id'], $objVariable['type'], $strDbField, $newValue);
            }
            // Delete variable
            elseif (!empty($objVariable['id']) && strlen($newValue) === 0)
            {
                VariableModel::deleteVariable($objVariable['id']);
            }
        }
    }


    public function deleteVariables($dc)
    {
        if (!$dc->activeRecord || !$dc->activeRecord->id || $dc->activeRecord->type !== 'pattern')
        {
            return;
        }

        VariableModel::deleteVariablesOf($dc->activeRecord->id, static::$table);
    }


    public function copyVariables($newId, $dc)
    {
        if (!$this->isContent() && !$this->isModule())
        {
            return;
        }

        $objElement = $this->isContent() ? \ContentModel::findByPk(\Input::get('id')) : \ModuleModel::findByPk(\Input::get('id'));

        if (empty($objElement) || $objElement->type != 'pattern' || empty($objElement->ptr_template))
        {
            return;
        }

        \Database::getInstance()->prepare("INSERT INTO " . VariableModel::getTable() . " (pid, ptable, type, name, text, textarea, html, file, folder, checkbox, date, time, datetime, color) SELECT ?, ptable, type, name, text, textarea, html, file, folder, checkbox, date, time, datetime, color FROM " . VariableModel::getTable() . " WHERE pid=? AND ptable=?")
            ->execute(
                $newId,
                \Input::get('id'),
                static::$table
            );
    }


    public function reviseTable($table, $new_records, $parent_table, $child_tables)
    {
        if (!$this->isContent() && !$this->isModule())
        {
            return false;
        }

        $objStmt = \Database::getInstance()->execute("DELETE FROM " . VariableModel::getTable() . " WHERE tstamp=0");

        return $objStmt->affectedRows > 0;

        //var_dump($table);
        //var_dump($new_records);
        //throw new \Exception('stop');
    }

}
