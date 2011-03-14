<?php

class Doctrine_Pollable_Poll_TestCase extends Doctrine_UnitTestCase
{
    private $d;

    public function prepareData()
    {
        $this->d = Doctrine_Core::getTable('NullPollPollableTest')->getTemplate('Doctrine_Template_Pollable')->getDefinition('NullPoll');
    }

    public function prepareTables()
    {
        $this->tables[] = 'NullPollPollableTest';
        parent::prepareTables();
    }

    // open()
    // close()

    public function testOpenClose()
    {
        $r = new PollablePollTest();

        $p = new Doctrine_Pollable_Poll($r, $this->d);

        $p->close();
        $this->assertEqual($r->get($this->d->getIsOpenColumnName()), false);

        $p->open();
        $this->assertEqual($r->get($this->d->getIsOpenColumnName()), true);
    }

    // closeAt()

    public function testCloseAt()
    {
        $r = new PollablePollTest();

        $p = new Doctrine_Pollable_Poll($r, $this->d);

        $p->closeAt('2009-12-01 13:56:23');
        $this->assertEqual($r->get($this->d->getClosesAtColumnName()), '2009-12-01 13:56:23');
    }

    // isPollable()

    public function testSaveOpen()
    {
        $r = new NullPollPollableTest();

        $p = new Doctrine_Pollable_Poll($r, $this->d);

        $this->assertFalse($p->isPollable());//, 'isPollable() is false for transient object');
        $r->save();
        $this->assertTrue($p->isPollable());//, 'isPollable() is true after save()');
        $p->open();
        $this->assertTrue($p->isPollable());//, 'isPollable() is true after save(), openPoll()');
    }

    public function testSaveClose()
    {
        $r = new NullPollPollableTest();

        $p = new Doctrine_Pollable_Poll($r, $this->d);

        $r->save();
        $p->close();
        $this->assertFalse($p->isPollable());//, 'isPollable() is false after save(), closePoll()');
    }

    public function testOpenSaveClose()
    {
        $r = new NullPollPollableTest();

        $p = new Doctrine_Pollable_Poll($r, $this->d);

        $p->open();
        $this->assertFalse($p->isPollable());//, 'isPollable() is false for transient object, even after openPoll()');
        $r->save();
        $this->assertTrue($p->isPollable());//, 'isPollable() is true after openPoll(), save()');
        $p->close();
        $this->assertFalse($p->isPollable());//, 'isPollable() is false after openPoll(), save(), closePoll()');
    }

    public function testCloseSaveOpen()
    {
        $r = new NullPollPollableTest();

        $p = new Doctrine_Pollable_Poll($r, $this->d);

        $p->close();
        $this->assertFalse($p->isPollable());//, 'isPollable() is false for transient object, even after closePoll()');
        $r->save();
        $this->assertFalse($p->isPollable());//, 'isPollable() is false after closePoll(), save()');
        $p->open();
        $this->assertTrue($p->isPollable());//, 'isPollable() is true after closePoll(), save(), openPoll()');
    }


    public function testClosesAtSave()
    {
        $r = new NullPollPollableTest();

        $p = new Doctrine_Pollable_Poll($r, $this->d);

        $p->closeAt(date("Y-m-d H:i:s", time() - 100));
        $r->save();
        $this->assertFalse($p->isPollable());//, "isPollable() is false after 'poll_closes_at'");
    }

    public function testClosesAtSaveClose()
    {
        $r = new NullPollPollableTest();

        $p = new Doctrine_Pollable_Poll($r, $this->d);

        $p->closeAt(date("Y-m-d H:i:s", time() + 100));
        $r->save();
        $this->assertTrue($p->isPollable());//, "isPollable() is true before 'poll_closes_at'");
        $p->close();
        $this->assertFalse($p->isPollable());//, "isPollable() is false after closePoll(), even before 'poll_closes_at'");
    }

    // __call()

    public function testProxy()
    {
        $r = new NullPollPollableTest();

        $p = new Doctrine_Pollable_Poll($r, $this->d);

        $this->assertEqual($p->getName(), 'NullPoll'); //proxied into $this->d
    }
}

class PollablePollTest extends Doctrine_Record
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
