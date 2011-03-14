<?php

/**
 * A specific instance of a poll definition
 *
 * @package     Doctrine
 * @subpackage  Pollable
 * @link        http://www.doctrine-project.org/
 * @since       1.0
 * @author      Jonathan R. Collins <pollable@joncollins.name>
 */
class Doctrine_Pollable_Poll
{
    /**
     * This poll's record
     *
     * @var Doctrine_Record
     */
    protected $_record;

    /**
     * This poll's definition
     *
     * @var Doctrine_Pollable_Poll_Definition
     */
    protected $_definition;

    /**
     * Creates a poll on a specific record
     *
     * @param Doctrine_Record $record
     * @param Doctrine_Pollable_Poll_Definition $definition
     */
    public function __construct(Doctrine_Record $record, Doctrine_Pollable_Poll_Definition $definition)
    {
        $this->_record = $record;
        $this->_definition = $definition;
    }

    /**
     * Checks if the poll is accepting responses
     *
     * Depends on record state, the "is open" field, and the "closes at" field
     *
     * @return boolean
     */
    public function isPollable()
    {
        //return false if record is transient
        $state = $this->_record->state();
        if ($state == Doctrine_Record::STATE_TDIRTY || $state == Doctrine_Record::STATE_TCLEAN)
        {
            return false;
        }

        //otherwise calculate using value of "closes at" field
        $closes_at = $this->_record->get($this->_definition->getClosesAtColumnName());

        if ($closes_at != null)
        {
            if (strtotime($closes_at) <= time())
            {
                return false;
            }
        }

        //or explicitly flagged
        return $this->_record->get($this->_definition->getIsOpenColumnName());
    }

    /**
     * Validates and stores a response to this poll
     *
     * @param Doctrine_Record $record
     * @param mixed $response
     */
    public function respond($response, Doctrine_Pollable_Identity $identity = null)
    {
        if ( ! $this->_definition->getStorage()->validate($this->_record, $response)) {
            $response = var_export($response, true);
            throw new Doctrine_Pollable_Exception("Response \"$response\" did not validate");
        }

        if ($identity == null) {
            $identity = new Doctrine_Pollable_Identity();
        }

        if ($this->isIdentityThrottled($identity)) {
            $identity = serialize($identity);
            throw new Doctrine_Pollable_Exception("Identity \"$identity\" is throttled for poll \"{$this->__toString()}\"");
        }

        $response_record = $this->createResponse();

        $this->_definition->getStorage()->store($response_record, $response);

        $response_record->set($this->_definition->getUserColumnName(), $identity->getUser());

        foreach ($this->_definition->getTraits() as $trait => $column) {
            $response_record->set($trait, $identity->getTrait($trait));
        }

        $relation = $this->_definition->getStorage()->getResponse()->getRelationName();
        $this->_record->{$relation}[] = $response_record;

        $this->_record->save();
    }

    public function isIdentityThrottled(Doctrine_Pollable_Identity $identity)
    {
        return false;
    }

    private function createResponse() {
        return $this->_definition->getStorage()->getResponse()->create();
    }

    /**
     * Opens the poll
     *
     */
    public function open()
    {
        $this->_record->set($this->_definition->getIsOpenColumnName(), true);
    }

    /**
     * Closes the poll
     *
     */
    public function close()
    {
        $this->_record->set($this->_definition->getIsOpenColumnName(), false);
    }

    /**
     * Sets the poll to close at a certain date
     *
     * @param string $date
     */
    public function closeAt($date)
    {
        $this->_record->set($this->_definition->getClosesAtColumnName(), $date);
    }

    /**
     * Proxies any other method calls directly to $_definition, inserting
     * $_record as the first argument
     *
     * @param string $method
     * @param array $args
     */
    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->_definition, $method), array_merge(array($this->_record), $args));
    }
}
