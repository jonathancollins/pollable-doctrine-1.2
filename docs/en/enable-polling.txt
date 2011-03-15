Add a specific polling capability to a given model by using `Doctrine_Template_Pollable` and defining one or more `polls`:

    [yml]
    BlogPost:
      actAs:
        Pollable:
          polls:
            Ratings:
              storage:
                type: discrete
                options:
                  values: [good,average,bad]
                  
      columns:
        title:
          type: string(255)
          notnull: true
        body:
          type: string
          notnull: true


In this example, `Ratings` will be an accessor for the poll and its collection of responses. The `storage` key is required for all polls. The storage `type` must be defined, and some storages require additional `options`. The `discrete` storage is an out-of-the-box storage type and requires the `values` key, which defines the discrete set of accepted responses.

> **NOTE**
> This extension does not provide migrations when adding polling capability to your models

After building the models and deploying the tables, you may use the `Ratings` accessor of any BlogPost record:

    [php]
    $blog_post = Doctrine_Core::getTable('BlogPost')->findById(1);

    $blog_post->Ratings->respond('good');
    $blog_post->Ratings->respond('bad');
    $blog_post->Ratings->respond('average');
    $blog_post->Ratings->respond('bad');
    $blog_post->Ratings->respond('good');
    $blog_post->Ratings->respond('average');
    $blog_post->Ratings->respond('bad');

    $blog_post->Ratings->respond('ok'); // throws Doctrine_Pollable_Exception

Transient (unpersisted) records do not accept responses. It is not necessary to call `$blog_post->save()` after using `respond()`.

Accessing and analyzing the results is easy:

    [php]
    print $blog_post->Ratings->count('good'); // prints 2
    print $blog_post->Ratings->count('average'); //prints 2
    print $blog_post->Ratings->count('bad'); // prints 3

    print $blog_post->Ratings->total(); //prints 7

    print $blog_post->Ratings->percent('average'); // prints 28.57

Discrete storage types provide median and mode methods:

    [php]
    print $blog_post->Ratings->median(); // prints 'average'
    print $blog_post->Ratings->mode(); // prints 'bad'

When reporting the median, discrete storage orders the responses as they were given in the poll definition.

> **NOTE**
> In the example above, a separate `Ratings` poll will be present on every `BlogPost` entity, each accepting the same set of responses defined by the `values` key. For information on supporting record-specific sets of accepted responses, see Storage Types -> Dynamic
