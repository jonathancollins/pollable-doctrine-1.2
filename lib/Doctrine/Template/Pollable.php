<?php

/**
 * Maintains poll definitions, adds poll-related columns to the table, and
 * registers Doctrine_Record_Filter_Poll objects to act as poll accessors
 *
 * @package     Doctrine
 * @subpackage  Template
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Template_Pollable extends Doctrine_Template
{
    /**
     * Set of Doctrine_Pollable_Poll_Definition objects
     *
     * @var array
     */
    protected $_definitions = array();

    /**
     * Default options for the Pollable template
     *
     * @var array
     */
    protected $_options = array(
        'polls' => array(),
        'options' => array(),
    );

    /**
     * Sets up options and Doctrine_Pollable_Poll_Definition objects
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);

        foreach ($this->_options['polls'] as $poll_name => $poll_options) {
            $poll_options = Doctrine_Lib::arrayDeepMerge($this->_options['options'], $poll_options);
            $this->_definitions[$poll_name] = new Doctrine_Pollable_Poll_Definition($poll_name, $poll_options);
        }
    }

    /**
     * Returns a Doctrine_Pollable_Poll_Definition for the given poll name
     *
     * @param string $poll_name
     * @return Doctrine_Pollable_Poll_Definition
     */
    public function getDefinition($poll_name)
    {
        if (isset($this->_definitions[$poll_name])) {
            return $this->_definitions[$poll_name];
        }

        return null;
    }

    /**
     * Sets up each poll definition and initializes it to $_table
     *
     */
    public function setUp()
    {
        foreach ($this->_definitions as $definition) {
            $definition->setUp();
            $definition->initialize($this->_table);
        }
    }

    /**
     * Adds "is open" and "closes at" columns for each poll, and registers a
     * filter for each poll
     *
     */
    public function setTableDefinition()
    {
        foreach ($this->_definitions as $definition) {
            $this->hasColumn(
                $definition->getIsOpenColumnName(),
                'boolean', null,
                array(
                    'notnull' => true,
                    'default' => true,
                )
            );

            $this->hasColumn(
                $definition->getClosesAtColumnName(),
                'timestamp', null,
                array (
                    'notnull' => false,
                    'default' => null,
                )
            );

            $this->_table->unshiftFilter(new Doctrine_Record_Filter_Poll($definition));
        }
    }
}
