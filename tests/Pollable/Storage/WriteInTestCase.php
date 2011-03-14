<?php

class Doctrine_Pollable_Storage_WriteIn_TestCase extends Doctrine_UnitTestCase
{
    private $r;

    public function prepareTables()
    {
        $this->tables[] = 'WriteInStorageTest';
        $this->tables[] = 'WriteInStorageTestWriteInPollResponse';
        parent::prepareTables();
    }

    public function prepareData()
    {
        $this->r = new WriteInStorageTest();
        $this->r->save();

        $this->r->WriteInPoll->respond('George Washington');
        $this->r->WriteInPoll->respond('George Washington');
        $this->r->WriteInPoll->respond('George Washington');
        $this->r->WriteInPoll->respond('George Washington');
        $this->r->WriteInPoll->respond('Thomas Jefferson');
        $this->r->WriteInPoll->respond('Thomas Jefferson');
        $this->r->WriteInPoll->respond('James Madison');
        $this->r->WriteInPoll->respond('James Madison');
        $this->r->WriteInPoll->respond('James Madison');
    }

    public function testStringOnly()
    {
        $storage = new Doctrine_Pollable_Storage_WriteIn();

        $r = new WriteInStorageValidationTest();

        $this->assertTrue($storage->validate($r, ''));
        $this->assertTrue($storage->validate($r, 'string'));
        $this->assertFalse($storage->validate($r, 0));
        $this->assertFalse($storage->validate($r, 16));
        $this->assertFalse($storage->validate($r, 0.0));
        $this->assertFalse($storage->validate($r, 1.0));
        $this->assertFalse($storage->validate($r, true));
        $this->assertFalse($storage->validate($r, false));
        $this->assertFalse($storage->validate($r, null));
        $this->assertFalse($storage->validate($r, new DateTime()));
    }

    public function testMaxLength()
    {
        $length = 15;

        $storage = new Doctrine_Pollable_Storage_WriteIn(array(
            'length' => $length,
        ));

        $r = new WriteInStorageValidationTest();

        $this->assertTrue($storage->validate($r, str_repeat('a', $length)));
        $this->assertFalse($storage->validate($r, str_repeat('a', $length + 1)));
    }

    public function testValidator() {
        $length = 15;

        $storage = new Doctrine_Pollable_Storage_WriteIn(array(
            'validators' => array(
                'country' => true,
            ),
        ));

        $r = new WriteInStorageValidationTest();

        $this->assertTrue($storage->validate($r, 'ad'));
        $this->assertFalse($storage->validate($r, 'xx'));
    }

    public function testValidatorWithArgs() {
        $length = 15;

        $storage = new Doctrine_Pollable_Storage_WriteIn(array(
            'validators' => array(
                'minlength' => $length
            ),
        ));

        $r = new WriteInStorageValidationTest();

        $this->assertFalse($storage->validate($r, str_repeat('a', $length - 1)));
        $this->assertTrue($storage->validate($r, str_repeat('a', $length)));
    }

    public function testResponses() {
        $expected = array(
            'responses' => array(
                array(
                    'response' => 'George Washington',
                    'count' => 4,
                ),
                array(
                    'response' => 'James Madison',
                    'count' => 3,
                ),
                array(
                    'response' => 'Thomas Jefferson',
                    'count' => 2,
                ),
            ),
            'total' => 9,
        );

        $this->assertEqual($this->r->WriteInPoll->responses(), $expected);
    }

    public function testCount() {
        $this->assertEqual($this->r->WriteInPoll->count('George Washington'), 4);
        $this->assertEqual($this->r->WriteInPoll->count('Thomas Jefferson'), 2);
        $this->assertEqual($this->r->WriteInPoll->count('James Madison'), 3);
    }

    public function testPercentage() {
        $this->assertEqual($this->r->WriteInPoll->percentage('George Washington'), 4 / 9 * 100);
        $this->assertEqual($this->r->WriteInPoll->percentage('Thomas Jefferson'), 2 / 9 * 100);
        $this->assertEqual($this->r->WriteInPoll->percentage('James Madison'), 3 / 9 * 100);
    }

    public function testTotal() {
        $this->assertEqual($this->r->WriteInPoll->total(), 9);
    }

    public function testMode()
    {
        $this->assertEqual($this->r->WriteInPoll->mode(), 'George Washington');
    }
}

class WriteInStorageValidationTest extends Doctrine_Record {

}

class WriteInStorageTest extends Doctrine_Record
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
