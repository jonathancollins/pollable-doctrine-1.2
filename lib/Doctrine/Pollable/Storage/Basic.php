<?php

/**
 * Provides a single response column to the response generator
 * 
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
abstract class Doctrine_Pollable_Storage_Basic extends Doctrine_Pollable_Storage
{
    /**
     * Column added to $_response by default
     *
     * Basic storage types can override this value, or set individual parts in
     * their constructors
     *
     * @var array
     */
    protected $_column = array(
        'name'    => 'response',
        'type'    => 'string',
        'length'  => 255,
        'options' => array(
            'notnull' => true,
        ),
    );

    /**
     * A basic implementation that should work for most basic single-column storages
     *
     * @param Doctrine_Record $record
     * @param mixed $response
     */
    protected function _store(Doctrine_Record $record, $response)
    {
        $record->response = $response;
    }

    public function responses(Doctrine_Record $record)
    {
        $result = array(
            'responses' => array(),
            'total' => 0,
        );

        $responses = $this->getCountsQuery($record)
            ->orderBy($this->_column['name'])
            ->fetchArray();

        foreach ($responses as $k => $response) {
            $result['responses'][] = array(
                'response' => $response[$this->_column['name']],
                'count' => $response['count'],
            );

            $result['total'] += $response['count'];
        }

        return $result;
    }

    public function count(Doctrine_Record $record, $response)
    {
        return $this->getQuery($record)->andWhere($this->_column['name'].' = ?', $response)->count();
    }

    public function total(Doctrine_Record $record)
    {
        return $this->getQuery($record)->count();
    }

    public function percentage(Doctrine_Record $record, $response)
    {
        $total = $this->total($record);

        if ($total == 0) {
            return null;
        }

        return $this->count($record, $response) / $total * 100;
    }

    public function mode(Doctrine_Record $record)
    {
        $response = $this->getCountsQuery($record)
            ->orderBy('count DESC')
            ->limit(1)
            ->fetchArray();

        if (isset($response[0])) {
          return $response[0][$this->_column['name']];
        }
        else {
          return null;
        }
    }

    protected function getQuery(Doctrine_Record $record)
    {
        $q = $this->_response->getTable()->createQuery();

        foreach ((array) $this->_response->getOption('table')->getIdentifier() as $column) {
            $q->andWhere("$column = ?", $record[$column]);
        }

        return $q;
    }

    protected function getCountsQuery(Doctrine_Record $record)
    {
        return $this->getQuery($record)
            ->select("{$this->_column['name']}, COUNT({$this->_column['name']}) as count")
            ->groupBy($this->_column['name']);
    }

    /**
     * Sets up $_response and adds it to this storage
     *
     */
    public function setUp($identity)
    {
        parent::setUp($identity);

        $this->_response->addColumn(
            $this->_column['name'],
            $this->_column['type'],
            $this->_column['length'],
            $this->_column['options']
        );
    }
    
    public function initialize(Doctrine_Table $table, $poll_name) {
        parent::initialize($table, $poll_name);
        
        $this->_response->getTable()->addTemplate('Timestampable', new Doctrine_Template_Timestampable(array(
            'created' => array(
                'name' => 'responded_at',
            ),
            'updated' => array(
                'disabled' => true,
            ),
        )));
    }
}
