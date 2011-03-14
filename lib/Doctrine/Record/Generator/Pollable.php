<?php

/**
 * Record generator used by a pollable storage
 *
 * Names the component according to a given poll name and the class name of the
 * implementation. Provides helper method for storages to add columns.
 *
 * @package     Doctrine
 * @subpackage  Record
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
abstract class Doctrine_Record_Generator_Pollable extends Doctrine_Record_Generator
{
    /**
     * The name of the poll to which this generator belongs
     *
     * @var string
     */
    protected $_poll_name = null;

    /**
     * The derived or set name of this generator
     *
     * @var string
     */
    protected $_name = null;

    /**
     * List of columns to be added to this generator
     *
     * Each entry is of the form:
     *
     * array(
     *     'name'   =>  'column_name',
     *     'type'   =>  'columntype',
     *     'length' =>  <length>
     *     'options' => array(...),
     * )
     *
     * @var unknown_type
     */
    protected $_columns = array();

    /**
     * Derives $_name from the class name, if necessary
     *
     */
    public function __construct()
    {
        if ($this->_name == null) {
            $name = array();
            preg_match('/^(Doctrine_Record_Generator_Pollable_)?(.*)$/', get_class($this), $name);
            $this->_name = $name[2];
        }
    }

    /**
     * Sets the poll name
     *
     * @param string $poll_name
     */
    public function setPollName($poll_name)
    {
        $this->_poll_name = $poll_name;
    }

    /**
     * Returns the poll name
     *
     * @return string
     */
    public function getPollName()
    {
        return $this->_poll_name;
    }

    /**
     * Explicitly sets the generator name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Returns the generator name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Derives a relation name from the poll name and the generator name
     *
     * @return string
     */
    public function getRelationName()
    {
        if ($this->_poll_name == null) {
            //throw exception? wasn't added via Doctrine_Pollable_Storage::addGenerator()
        }

        return $this->_poll_name . $this->_name;
    }

    /**
     * Returns the derived class name for this generator
     *
     * @return string
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * Adds a column to the list of columns to be added to the generator
     *
     * @param string $name
     * @param string $type
     * @param mixed $length
     * @param array $options
     */
    public function addColumn($name, $type, $length = null, $options = array())
    {
        $this->_columns[] = array(
            'name'    => $name,
            'type'    => $type,
            'length'  => $length,
            'options' => $options
        );
    }

    public function setTableDefinition()
    {
        foreach ($this->_columns as $column) {
            $this->hasColumn($column['name'], $column['type'], $column['length'], $column['options']);
        }
    }

    public function initOptions()
    {
        $this->setOption('className', '%CLASS%' . $this->getRelationName());
    }

    public function initialize(Doctrine_Table $table) {
        parent::initialize($table);
    }

    /**
     * Works identically to the Doctrine_Record_Generator version, except the
     * columns are not marked as primary
     *
     * @param Doctrine_Table $table     the table object that owns the plugin
     * @return array                    an array of foreign key definitions
     */
    public function buildForeignKeys(Doctrine_Table $table)
    {
        $fk = array();

        foreach ((array) $table->getIdentifier() as $column) {
            $def = $table->getDefinitionOf($column);

            unset($def['autoincrement']);
            unset($def['sequence']);
            unset($def['primary']);

            $col = $column;

            //$def['primary'] = true;
            $fk[$col] = $def;
        }
        return $fk;
    }

    /**
     * Creates local relation "Record", and creates foreign relation names
     * according to parent storage's poll name and this generator's name
     *
     */
    public function buildRelation()
    {
        $this->buildForeignRelation($this->getRelationName());
        $this->buildLocalRelation('Record');
    }

    public function create() {
        $class_name = $this->getOption('className');
        return new $class_name();
    }
}
