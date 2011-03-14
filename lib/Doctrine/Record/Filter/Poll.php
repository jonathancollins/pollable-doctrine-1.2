<?php

/**
 * Handles the accessor for a poll
 *
 * @package     Doctrine
 * @subpackage  Record
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Record_Filter_Poll extends Doctrine_Record_Filter
{
    /**
     * This poll definition that this filter is handling
     *
     * @var Doctrine_Pollable_Poll_Definition
     */
    private $_definition;

    /**
     * Cache of Doctrine_Pollable_Poll objects for $_definition
     *
     * @var array
     */
    private $_polls = array();

    /**
     * @param Doctrine_Pollable_Poll_Definition $definition
     */
    public function __construct(Doctrine_Pollable_Poll_Definition $definition)
    {
        $this->_definition = $definition;
    }
/*
    public function init()
    {

    }
*/
    /**
     * Always throws Doctrine_Record_UnknownPropertyException
     *
     */
    public function filterSet(Doctrine_Record $record, $poll_name, $value)
    {
        throw new Doctrine_Record_UnknownPropertyException();
    }

    /**
     * Caches a Doctrine_Pollable_Poll for $record, if necessary, then returns it
     *
     * @param mixed $poll_name
     */
    public function filterGet(Doctrine_Record $record, $poll_name)
    {
        if ($poll_name != $this->_definition->getName()) {
            throw new Doctrine_Record_UnknownPropertyException();
        }

        $oid = $record->getOid();

        if (!isset($this->_polls[$oid])) {
            $this->_polls[$oid] = new Doctrine_Pollable_Poll($record, $this->_definition);
        }

        return $this->_polls[$oid];
    }
}
