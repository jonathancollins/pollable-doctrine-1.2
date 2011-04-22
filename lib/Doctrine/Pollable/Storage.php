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

    /**
     * Return a structured array with information on all responses
     *
     * @param Doctrine_Record $record
     * @return array
     */
    abstract public function responses(Doctrine_Record $record);

    /**
     * Returns the number of poll responses with the given response
     *
     * @param Doctrine_Record $record
     * @param mixed $response
     * @return integer
     */
    abstract public function count(Doctrine_Record $record, $response);

    /**
     * Returns the percentage poll responses with the given response
     *
     * @param Doctrine_Record $record
     * @param mixed $response
     * @return float
     */
    abstract public function percentage(Doctrine_Record $record, $response);

    /**
     * Returns the total number of poll responses
     *
     * @param Doctrine_Record $record
     * @return integer
     */
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
     * @return array Array of Doctrine_Record_Generator_Pollable instances
     */
    public function getGenerators()
    {
        return $this->_generators;
    }

    /**
     * Gets the generator for the base response
     *
     * @return Doctrine_Record_Generator_Pollable
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Sets up this storage during Doctrine model setup time
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
    
    /**
     * Initializes this storage to a Doctrine_Table instance
     *
     * @param Doctrine_Table $table the Doctrine table to initialize to
     * @param string poll name to initialize
     */
    public function initialize(Doctrine_Table $table, $poll_name) {
        foreach ($this->getGenerators() as $generator) {
            $generator->setPollName($poll_name);
            $generator->initialize($table);
        }
    }
}
