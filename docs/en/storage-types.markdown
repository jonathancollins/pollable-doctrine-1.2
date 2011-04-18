## Discrete

Discrete storage is the base behavior for many other storage types, but it can be used on its own. Discrete polls are defined as follows:

    [yml]
    DiscretePoll:
      storage:
        type: discrete
        options:
          values: [a,b,c]
    
The `values` option is required and tells the discrete storage which responses to accept:

    [php]
    $ent->DiscretePoll->respond('c');
    $ent->DiscretePoll->respond('a');
    $ent->DiscretePoll->respond('b');
    $ent->DiscretePoll->respond('a');
    $ent->DiscretePoll->respond('b');
    $ent->DiscretePoll->respond('c');
    $ent->DiscretePoll->respond('c');
    
    $ent->DiscretePoll->respond('d'); //throws Doctrine_Pollable_Exception

    print $ent->DiscretePoll->count(); //prints 7
    print $ent->DiscretePoll->mode(); //prints 'c'
    print $ent->DiscretePoll->median(); //prints 'b'

### Static

#### Yes or No

Collects yes or no responses. Yes or No polls are defined as follows:

    [yml]
    YesNoPoll:
      storage:
        type: yes_no

All methods available for discrete storages are available. The discrete options are defined as `Doctrine_Pollable_Storage_UpDown::YES` and `Doctrine_Pollable_Storage_UpDown::NO`:

    [php]
    print $ent->YesNoPoll->percent(Doctrine_Pollable_Storage_YesNo::YES);

This storage type also provides `yes()`, `no()`, and `approved()` methods:

    [php]
    $ent->YesNoPoll->yes();
    $ent->YesNoPoll->no();
    $ent->YesNoPoll->yes();

    print $ent->YesNoPoll->approved(); //prints true

#### Up or Down

Collects up or down responses with an optional reason. Up or Down polls are defined as follows:

    [yml]
    UpDownPoll:
      storage:
        type: up_down

All methods available for discrete storages are available. The discrete options are defined as `Doctrine_Pollable_Storage_UpDown::UP` and `Doctrine_Pollable_Storage_UpDown::DOWN`:

    [php]
    print $ent->UpDownPoll->percent(Doctrine_Pollable_Storage_UpDown::DOWN);

This storage type also provides `up()`, `down()`, `reputation()`, and `reason()` methods:

    [php]
    $ent->UpDownPoll->down('Troll'); //provide a reason
    $ent->UpDownPoll->up('Insightful');
    $ent->UpDownPoll->down('Troll');
    $ent->UpDownPoll->down('Flame');

    print $ent->UpDownPoll->reputation(); //prints -2

To figure out the prevailing reason behind the votes, use the `reason()` method:

    [php]
    print $ent->UpDownPoll->reason(); //prints the overall reason, 'Troll'
    print $ent->UpDownPoll->reason(Doctrine_Pollable_Storage_UpDown::UP); //prints 'Insightful'
    print $ent->UpDownPoll->reason(Doctrine_Pollable_Storage_UpDown::DOWN); //prints 'Troll'

#### Rating (Integer)

Collects integer ratings. Polls are defined as follows:

    [yml]
    RatingPoll:
      storage:
        type: rating
        options:
          min: 1
          max: 5

#### Rating (Floating point)

Collects floating point ratings. Polls are defined as follows:

    [yml]
    RatingFloatPoll:
      storage:
        type: rating_float
        options:
          min: 1.0
          max: 10.0

## Non-discrete

### Write-in

Collects arbitrary strings. Write-in polls are defined as follows:

    [yml]
    WriteInPoll:
      storage:
        type: write_in

All standard response and data analyzation methods are available:

    [php]
    $ent->WriteInPoll->respond('George Washington');
    $ent->WriteInPoll->respond('Thomas Jefferson');
    $ent->WriteInPoll->respond('James Madison');
    $ent->WriteInPoll->respond('George Washington');
    $ent->WriteInPoll->respond('George Washington');
    $ent->WriteInPoll->respond('Thomas Jefferson');
    $ent->WriteInPoll->respond('James Madison');
    $ent->WriteInPoll->respond('James Madison');
    $ent->WriteInPoll->respond('George Washington');

    print $ent->WriteInPoll->total(); //prints 9
    print $ent->WriteInPoll->count('George Washington'); //prints 4
    print $ent->WriteInPoll->percentage('James Madison'); //prints 3 / 9 * 100
