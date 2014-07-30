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
 * Class VariableModel
 */
class VariableModel extends \Model
{

    /**
     * Table name
     */
    protected static $strTable = 'tl_ptr_variable';


    /**
     * Update variable data
     *
     * @param string $id          Id of variable
     * @param string $type        Type of variable
     * @param string $strDbField  Database field to save new value
     * @param string $value       New value of variable
     */
    public static function updateVariable($id, $type, $strDbField, $value)
    {
        \Database::getInstance()->prepare("UPDATE " . static::$strTable . " SET tstamp=?, type=?, " . $strDbField . "=? WHERE id=?")
            ->execute(time(), $type, $value, $id);
    }


    /**
     * Delete variable by id
     *
     * @param string $id
     */
    public static function deleteVariable($id)
    {
        \Database::getInstance()->prepare("DELETE FROM " . static::$strTable . " WHERE id=?")
            ->execute($id);
    }


    /**
     * Delete all variables of pattern
     *
     * @param string $pid
     * @param string $ptable
     */
    public static function deleteVariablesOf($pid, $ptable)
    {
        \Database::getInstance()->prepare("DELETE FROM " . static::$strTable . " WHERE pid=? AND ptable=?")
            ->execute($pid, $ptable);
    }
}
