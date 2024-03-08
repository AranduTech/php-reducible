<?php

namespace Arandu\Reducible\Tests\Samples;

use Arandu\Reducible\Reducible;

class Subject
{
    use Reducible;

    public function foo()
    {
        return 'foo';
    }

    public function bar()
    {
        return 'bar';
    }

    public static function baz()
    {
        return 'baz';
    }
}
