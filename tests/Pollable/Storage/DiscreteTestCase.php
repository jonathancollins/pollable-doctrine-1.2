<?php

class Doctrine_Pollable_Storage_Discrete_TestCase extends Doctrine_UnitTestCase
{
    public function prepareTables()
    {
        $this->tables[] = 'DiscreteStorageTest';
        $this->tables[] = 'DiscreteStorageTestDiscretePollResponse';
        parent::prepareTables();
    }

    public function prepareData()
    {
        $this->r1 = new DiscreteStorageTest();
        $this->r1->save();

        $this->r1->DiscretePoll->respond('bad');
        $this->r1->DiscretePoll->respond('good');
        $this->r1->DiscretePoll->respond('good');
        $this->r1->DiscretePoll->respond('ok');
        $this->r1->DiscretePoll->respond('bad');
        $this->r1->DiscretePoll->respond('bad');
        $this->r1->DiscretePoll->respond('ok');
        $this->r1->DiscretePoll->respond('ok');
        $this->r1->DiscretePoll->respond('good');
        $this->r1->DiscretePoll->respond('good');
        $this->r1->DiscretePoll->respond('ok');
        $this->r1->DiscretePoll->respond('good');

        $this->r2 = new DiscreteStorageTest();
        $this->r2->save();

        // for testing median with even number of responses
        $this->r3 = new DiscreteStorageTest();
        $this->r3->save();

        $this->r3->DiscretePoll->respond('good');
        $this->r3->DiscretePoll->respond('bad');
        $this->r3->DiscretePoll->respond('ok');
        $this->r3->DiscretePoll->respond('good');
    }

    public function testBasicValidation()
    {
        $storage = new Doctrine_Pollable_Storage_Discrete(array(
            'choices' => array(
                'a',
                'b',
                'c',
            ),
        ));

        $r = new DiscreteStorageValidationTest();

        $this->assertTrue($storage->validate($r, 'a'));
        $this->assertTrue($storage->validate($r, 'b'));
        $this->assertTrue($storage->validate($r, 'c'));

        $this->assertFalse($storage->validate($r, 'd'));
        $this->assertFalse($storage->validate($r, 0));
        $this->assertFalse($storage->validate($r, 1));
        $this->assertFalse($storage->validate($r, 1.0));
        $this->assertFalse($storage->validate($r, true));
        $this->assertFalse($storage->validate($r, false));
        $this->assertFalse($storage->validate($r, array(100, 101)));
        $this->assertFalse($storage->validate($r, null));
        $this->assertFalse($storage->validate($r, new Yes()));
        $this->assertFalse($storage->validate($r, fopen(__FILE__, 'r')));
    }

    public function testStrictOff()
    {
        $storage = new Doctrine_Pollable_Storage_Discrete(array(
            'choices' => array(
                '1',
                'yes',
                false,
                '3.14'
            ),
            'strict' => false,
        ));

        $r = new DiscreteStorageValidationTest();

        $this->assertTrue($storage->validate($r, '1'));         // '1' == '1'
        $this->assertTrue($storage->validate($r, '0001'));      // '0001' == '1'
        $this->assertTrue($storage->validate($r, 0));           // 0 == 'yes'
        $this->assertTrue($storage->validate($r, 0.0));         // 0.0 == 'yes'
        $this->assertTrue($storage->validate($r, 1));           // 1 == '1'
        $this->assertTrue($storage->validate($r, new Yes()));   // new Yes() == 'yes'
        $this->assertTrue($storage->validate($r, false));       // false == false
        $this->assertTrue($storage->validate($r, ''));          // '' == false
        $this->assertTrue($storage->validate($r, 3.14));        // 3.14 == '3.14'
        $this->assertTrue($storage->validate($r, '3.14'));      // '3.14' == '3.14'

        $this->assertFalse($storage->validate($r, 1.1));        // 1.1 != '1'
        $this->assertFalse($storage->validate($r, 'true'));     // 'true' != '1'
        $this->assertFalse($storage->validate($r, 'false'));    // 'false' != false
    }

    public function testResponses()
    {
        $expected = array(
            'responses' => array(
                array(
                    'response' => 'bad',
                    'count' => 3,
                ),
                array(
                    'response' => 'good',
                    'count' => 5,
                ),
                array(
                    'response' => 'ok',
                    'count' => 4,
                ),
            ),
            'total' => 12,
        );

        $this->assertEqual($this->r1->DiscretePoll->responses(), $expected);

        $expected = array(
            'responses' => array(),
            'total' => 0,
        );

        $this->assertEqual($this->r2->DiscretePoll->responses(), $expected);
    }

    public function testCount()
    {
        $this->assertEqual($this->r1->DiscretePoll->count('bad'), 3);
        $this->assertEqual($this->r1->DiscretePoll->count('ok'), 4);
        $this->assertEqual($this->r1->DiscretePoll->count('good'), 5);

        $this->assertEqual($this->r2->DiscretePoll->count('bad'), 0);
        $this->assertEqual($this->r2->DiscretePoll->count('ok'), 0);
        $this->assertEqual($this->r2->DiscretePoll->count('good'), 0);
    }

    public function testPercentage()
    {
        $this->assertEqual($this->r1->DiscretePoll->percentage('bad'), 3 / 12 * 100);
        $this->assertEqual($this->r1->DiscretePoll->percentage('ok'), 4 / 12 * 100);
        $this->assertEqual($this->r1->DiscretePoll->percentage('good'), 5 / 12 * 100);

        $this->assertEqual($this->r2->DiscretePoll->count('bad'), null);
        $this->assertEqual($this->r2->DiscretePoll->count('ok'), null);
        $this->assertEqual($this->r2->DiscretePoll->count('good'), null);
    }

    public function testTotal()
    {
        $this->assertEqual($this->r1->DiscretePoll->total(), 12);

        $this->assertEqual($this->r2->DiscretePoll->total(), 0);
    }

    public function testMedian()
    {
        $this->assertEqual($this->r1->DiscretePoll->median(), 'ok');

        $this->assertEqual($this->r2->DiscretePoll->total(), null);

        $this->assertEqual($this->r2->DiscretePoll->total(), 'ok'); // {bad,ok,good,good} returns left-side value
    }

    public function testMode()
    {
        $this->assertEqual($this->r1->DiscretePoll->mode(), 'good');

        $this->assertEqual($this->r2->DiscretePoll->total(), null);
    }
}

class DiscreteStorageValidationTest extends Doctrine_Record
{

}

class DiscreteStorageTest extends Doctrine_Record
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
                                'bad',
                                'ok',
                                'good',
                            ),
                        ),
                    ),
                ),
            ),
        ));
    }
}

class Yes
{
    public function __toString()
    {
        return 'yes';
    }
}
