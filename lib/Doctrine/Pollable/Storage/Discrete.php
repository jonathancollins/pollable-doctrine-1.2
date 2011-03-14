<?php

/**
 * Pollable storage that collects the given discrete set of responses
 * 
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Storage_Discrete extends Doctrine_Pollable_Storage_Basic
{

    protected $_choices = array();

    /**
     * Sets default options for discrete storage and defines the default response
     * column
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        parent::__construct(Doctrine_Lib::arrayDeepMerge(
            array(
                'choices' => array(),
                'strict' => true,
            ),
            $options
        ));

        // give contiguous numeric keys
        $choices = array_values($this->_options['choices']);

        // convert to string and map to sort order
        foreach ($choices as $key => $choice) {
            $choice = (string) $choice;

            if (isset($this->_choices[$choice])) {
                throw new Doctrine_Pollable_Exception("Duplicate discrete choice: $choice");
            }

            $this->_choices[$choice] = $key;
        }

        $this->_column['type'] = 'enum';
        $this->_column['length'] = null;
        $this->_column['options'] = array('values' => array_keys($this->_choices));
    }

    /**
     * Validates a discrete response
     *
     * @param Doctrine_Record $record
     * @param mixed $response
     */
    protected function _validate(Doctrine_Record $record, $response)
    {
        return in_array($response, $this->_options['choices'], $this->_options['strict']);
    }

    /**
     * Finds the median response when responses are ordered as they appear in
     * the poll definition
     *
     * @param Doctrine_Record $record
     * @return string
     */
    public function median(Doctrine_Record $record)
    {
        if (count($this->_choices) == 2) {
            return $this->mode();
        }

        // get response list w/ aggregate counts
        $responses = $this->getCountsQuery($record)->fetchArray();

        // sort in enum order
        usort($responses, array($this, 'sortArray'));

        $total = array_sum(array_map(array('Doctrine_Pollable_Storage_Discrete', 'getAggregateCount'), $responses));

        if ($total == 0) {
            return null;
        }

        // this many responses come before the median
        $half = intval($total / 2);

        $runningTotal = 0;
        foreach ($responses as $response) {
            $runningTotal += $response['count'];
            // if running total surpasses median position, return current $response
            if ($half < $runningTotal) {
                return $response[$this->_column['name']];
            }
        }
    }

    protected static function getAggregateCount($response)
    {
        return $response['count'];
    }

    public function sortArray($left, $right)
    {
        return $this->_choices[$right[$this->_column['name']]] - $this->_choices[$left[$this->_column['name']]];
    }
}
