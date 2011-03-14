<?php

/**
 * Pollable storage that collects anything but stores nothing
 *
 * Useful in tests
 * 
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Storage_Null extends Doctrine_Pollable_Storage
{
    /**
     * Always validates
     *
     * @param Doctrine_Record $response
     * @param mixed $response
     * @return boolean true
     */
    protected function _validate(Doctrine_Record $record, $response)
    {
        return true;
    }

    /**
     * Does nothing
     *
     * @param Doctrine_Record $record
     * @param mixed $response
     */
    protected function _store(Doctrine_Record $record, $response)
    {

    }

    /**
     * Returns an empty response structure
     *
     * @param Doctrine_Record $record
     * @return array
     */
    public function responses(Doctrine_Record $record)
    {
        return array(
            'responses' => array(),
            'total' => 0,
        );
    }

    public function count(Doctrine_Record $record, $response)
    {
        return 0;
    }

    public function percentage(Doctrine_Record $record, $response)
    {
        return null;
    }

    public function total(Doctrine_Record $record)
    {
        return 0;
    }
    /**
     * Sets up no generators
     *
     * @param $identity array identity definition
     *
     */
    public function setUp($identity)
    {

    }
}
