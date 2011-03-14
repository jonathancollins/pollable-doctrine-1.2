<?php

/**
 * Validates and stores responses to a poll, and analyzes them
 *
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
abstract class Doctrine_Pollable_Storage
{
    /**
     * Array of options given to this storage
     *
     * @var array
     */
    protected $_options;

    /**
     * Set of Doctrine_Record_Generator_Pollable instances that make up this storage
     *
     * @var array
     */
    private $_generators = array();

    /**
     * Generator representing the base responses for the storage
     *
     * @var Doctrine_Record_Generator_Pollable_Response
     */
    protected $_response = null;

    /**
     * Sets options
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_options = $options;
    }

    /**
     * Allows access to the _validate() method
     *
     * @param Doctrine_Record $record
     * @param mixed $response
     */
    public function validate(Doctrine_Record $record, $response)
    {
        return $this->_validate($record, $response);
    }

    abstract protected function _validate(Doctrine_Record $record, $response);
    /**
     * Allows access to the _store() method
     *
     * @param Doctrine_Record $response_record
     * @param mixed $response
     */
    public function store(Doctrine_Record $response_record, $response)
    {
        return $this->_store($response_record, $response);
    }

    abstract protected function _store(Doctrine_Record $response_record, $response);

    abstract public function responses(Doctrine_Record $record);

    abstract public function count(Doctrine_Record $record, $response);

    abstract public function percentage(Doctrine_Record $record, $response);

    abstract public function total(Doctrine_Record $record);

    /**
     * Adds a Doctrine_Record_Generator_Pollable to be initialized
     *
     * @param Doctrine_Record_Generator_Pollable $generator
     */
    protected function addGenerator(Doctrine_Record_Generator_Pollable $generator)
    {
        $this->_generators[] = $generator;
    }

    /**
     * Get the list of generators for this storage
     *
     * @return array
     */
    public function getGenerators()
    {
        return $this->_generators;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Sets up a
     * 
     * @param $identity array identity definition
     */
    public function setUp($identity)
    {
        $this->_response = new Doctrine_Record_Generator_Pollable_Response();
        $this->addGenerator($this->_response);

        $this->_response->addColumn(
            $identity['user']['name'],
            $identity['user']['type'],
            $identity['user']['length'],
            $identity['user']['options']
        );

        foreach ($identity['traits'] as $trait => $column) {
            $this->_response->addColumn($trait, $column['type'], $column['length'], $column['options']);
        }
    }
    
    public function initialize(Doctrine_Table $table, $poll_name) {
        foreach ($this->getGenerators() as $generator) {
            $generator->setPollName($poll_name);
            $generator->initialize($table);
        }
    }
}
