<?php

class Doctrine_Template_Pollable_TestCase extends Doctrine_UnitTestCase
{
    public function prepareTables()
    {
        $this->tables[] = 'NullPollPollableTest';

        $this->tables[] = 'WriteInPollPollableTest';
        $this->tables[] = 'WriteInPollPollableTestWriteInPollResponse';

        $this->tables[] = 'DiscretePollPollableTest';
        $this->tables[] = 'DiscretePollPollableTestDiscretePollResponse';

        parent::prepareTables();
    }

    public function testGetDefinition()
    {
        $t = new Doctrine_Template_Pollable(array(
            'polls' => array(
                'Poll' => array(
                    'storage' => array(
                        'type' => 'null',
                    ),
                ),
            ),
        ));

        $d1 = $t->getDefinition('Poll');
        $this->assertEqual($d1->getName(), 'Poll');

        $d2 = $t->getDefinition('InvalidPoll');
        $this->assertEqual($d2, null);
    }

    public function testPollAccess()
    {
        $pollable = new NullPollPollableTest();

        try {
            $poll = $pollable->NullPoll;
            $this->assertEqual(get_class($poll), 'Doctrine_Pollable_Poll');
        }
        catch (Doctrine_Record_UnknownPropertyException $e) {
            $this->fail();
        }
    }

    public function testInvalidPollAccess()
    {
        $pollable = new NullPollPollableTest();

        try {
            $poll = $pollable->NullPol; //misspell
            $this->fail();
        }
        catch (Doctrine_Record_UnknownPropertyException $e) {
            $this->pass();
        }
    }

    public function testMultiplePollAccess()
    {
        $pollable = new MultiplePollsPollableTest();

        try {
            $polla = $pollable->NullPoll;
            $pollb = $pollable->WriteInPoll;
            $pollc = $pollable->DiscretePoll;

            $this->assertFalse($polla == $pollb);
            $this->assertFalse($polla == $pollc);
            $this->assertFalse($pollb == $pollc);
        }
        catch (Doctrine_Record_UnknownPropertyException $e) {
            $this->fail();
            $this->fail();
            $this->fail();
        }
    }

    public function testWriteInPollPollableTestHasRelation()
    {
        $test = Doctrine::getTable('WriteInPollPollableTest');
        $this->assertTrue($test->hasRelation('WriteInPollResponse'));

        $relation = $test->getRelation('WriteInPollResponse');

        $this->assertEqual($relation->getType(), Doctrine_Relation::MANY);
        $this->assertEqual($relation->getClass(), 'WriteInPollPollableTestWriteInPollResponse');
    }

    public function testWriteInPollPollableTestWriteInPollResponseHasRelation()
    {
        $test = Doctrine::getTable('WriteInPollPollableTestWriteInPollResponse');
        $this->assertTrue($test->hasRelation('Record'));

        $relation = $test->getRelation('Record');
        $this->assertEqual($relation->getType(), Doctrine_Relation::ONE);
        $this->assertEqual($relation->getClass(), 'WriteInPollPollableTest');
    }

    public function testDiscretePollPollableTestHasRelation()
    {
        $test = Doctrine::getTable('DiscretePollPollableTest');
        $this->assertTrue($test->hasRelation('DiscretePollResponse'));

        $relation = $test->getRelation('DiscretePollResponse');
        $this->assertEqual($relation->getType(), Doctrine_Relation::MANY);
        $this->assertEqual($relation->getClass(), 'DiscretePollPollableTestDiscretePollResponse');
    }

    public function testDiscretePollPollableTestDiscretePollResponseHasRelation()
    {
        $test = Doctrine::getTable('DiscretePollPollableTestDiscretePollResponse');
        $this->assertTrue($test->hasRelation('Record'));

        $relation = $test->getRelation('Record');
        $this->assertEqual($relation->getType(), Doctrine_Relation::ONE);
        $this->assertEqual($relation->getClass(), 'DiscretePollPollableTest');
    }

    public function testMultiplePollsPollableTestHasRelations()
    {
        $test = Doctrine::getTable('MultiplePollsPollableTest');
        $this->assertTrue($test->hasRelation('WriteInPollResponse'));
        $this->assertTrue($test->hasRelation('DiscretePollResponse'));

        $relation1 = $test->getRelation('WriteInPollResponse');
        $this->assertEqual($relation1->getType(), Doctrine_Relation::MANY);
        $this->assertEqual($relation1->getClass(), 'MultiplePollsPollableTestWriteInPollResponse');

        $relation2 = $test->getRelation('DiscretePollResponse');
        $this->assertEqual($relation2->getType(), Doctrine_Relation::MANY);
        $this->assertEqual($relation2->getClass(), 'MultiplePollsPollableTestDiscretePollResponse');
    }

    public function testMultiplePollsPollableTestWriteInPollResponseHasRelation()
    {
        $test = Doctrine::getTable('MultiplePollsPollableTestWriteInPollResponse');
        $this->assertTrue($test->hasRelation('Record'));

        $relation = $test->getRelation('Record');
        $this->assertEqual($relation->getType(), Doctrine_Relation::ONE);
        $this->assertEqual($relation->getClass(), 'MultiplePollsPollableTest');
    }

    public function testMultiplePollsPollableTestDiscretePollResponseHasRelation()
    {
        $test = Doctrine::getTable('MultiplePollsPollableTestDiscretePollResponse');
        $this->assertTrue($test->hasRelation('Record'));

        $relation = $test->getRelation('Record');
        $this->assertEqual($relation->getType(), Doctrine_Relation::ONE);
        $this->assertEqual($relation->getClass(), 'MultiplePollsPollableTest');
    }
}

class NullPollPollableTest extends Doctrine_Record
{
    public function setUp()
    {
        $this->actAs('Pollable', array(
            'polls' => array(
                'NullPoll' => array(
                    'storage' => array(
                        'type' => 'null',
                    ),
                ),
            ),
        ));
    }
}

class WriteInPollPollableTest extends Doctrine_Record
{
    public function setUp()
    {
        $this->actAs('Pollable', array(
            'polls' => array(
                'WriteInPoll' => array(
                    'storage' => array(
                        'type' => 'write_in',
                    ),
                ),
            ),
        ));
    }
}

class DiscretePollPollableTest extends Doctrine_Record
{
    public function setUp()
    {
        $this->actAs('Pollable', array(
            'polls' => array(
                'DiscretePoll' => array(
                    'storage' => array(
                        'type' => 'discrete',
                        'options' => array(
                            'choices' => array(
                                'a',
                                'b',
                                'c',
                            ),
                        ),
                    ),
                ),
            ),
        ));
    }
}

class MultiplePollsPollableTest extends Doctrine_Record
{
    public function setUp()
    {
        $this->actAs('Pollable', array(
            'polls' => array(
                'NullPoll' => array(
                    'storage' => array(
                        'type' => 'null',
                    ),
                ),
                'WriteInPoll' => array(
                    'storage' => array(
                        'type' => 'write_in',
                    ),
                ),
                'DiscretePoll' => array(
                    'storage' => array(
                        'type' => 'discrete',
                        'options' => array(
                            'choices' => array(
                                'a',
                                'b',
                                'c',
                            ),
                        ),
                    ),
                ),
            ),
        ));
    }
}
