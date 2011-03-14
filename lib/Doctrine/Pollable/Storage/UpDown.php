<?php

/**
 * Pollable storage that collects up or down votes
 *
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Storage_UpDown extends Doctrine_Pollable_Storage_Discrete
{
    const UP = 'up';

    const DOWN = 'down';

    /**
     * Sets discrete choices to self::UP and self::DOWN
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $options['choices'] = array(self::UP, self::DOWN);

        parent::__construct(Doctrine_Lib::arrayDeepMerge(
            array(
                'reasons' => array(
    		        'up' => array(null),
    		        'down' => array(null),
		        ),
            ),
            $options
        ));

        parent::__construct($options);

        if (!is_array($this->_options['reasons']['up'])) {
            $this->_options['reasons']['up'] = array($this->_options['reasons']['up']);
        }

        if (!is_array($this->_options['reasons']['down'])) {
            $this->_options['reasons']['down'] = array($this->_options['reasons']['down']);
        }
    }

    public function _validate(Doctrine_Record $record, $response) {
        if (is_array($response)) {
            $response = key($response);
            $reason = current($response);
        } else {
            $reason = null;
        }

        if (parent::_validate($record, $response)) {
            return in_array($reason, $this->_options['reasons'][$response]);
        }

        return false;
    }

    public function up(Doctrine_Record $record, $reason = null)
    {
        $this->store($record, array(self::UP => $reason));
    }

    public function down(Doctrine_Record $record, $reason = null) {
        $this->store($record, array(self::DOWN => $reason));
    }

    public function reputation(Doctrine_Record $record)
    {
        return $this->count($record, self::UP) - $this->count($record, self::DOWN);
    }

    public function reason(Doctrine_Record $record, $response = null) {
        if ($response == null) {
            // TODO: find the overall reason
        } elseif ($response == self::UP || $response == self::DOWN) {
            // TODO: find the most common reason for $response
        } else {
            return null;
        }
    }

    /**
     * Adds the "reason" column
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->_response->addColumn('reason', 'enum', null,
            array_merge($this->_options['reasons']['up'], $this->_options['reasons']['down'])
        );
    }
}
