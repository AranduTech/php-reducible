<?php

namespace Arandu\Reducible\Tests\Unit;

use Arandu\Reducible\Tests\Samples\MalformedSubject;
use Arandu\Reducible\Tests\Samples\Subject;
use Arandu\Reducible\Tests\TestCase;

class ExceptionTest extends TestCase
{

    public function testReducersCannotBeCalledWithoutArguments()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->expectExceptionMessage('No value provided for reducer.');

        Subject::reducerAlpha();        
    }

    public function testReducersCannotBeCalledWithoutArgumentsInstance()
    {
        $this->expectException(\ArgumentCountError::class);
        $this->expectExceptionMessage('No value provided for reducer.');

        (new Subject)->reducerAlpha();
    }

    public function testNonClosureReducer()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessageMatches('/must be of type Closure/');

        Subject::reducer('reducerAlpha', 'not a closure');

    }

    public function testNonClosureReducerInstance()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessageMatches('/must be of type Closure/');

        (new Subject)->reducer('reducerAlpha', 'not a closure');
    }

    public function testInjectedNonClosureReducer()
    {
        Subject::$reducers = [
            'reducerAlpha' => [
                ['reducer' => 'not a closure', 'priority' => 0],
            ],
        ];

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Reducer is not a closure.');

        Subject::reducerAlpha(3);
    }

    public function testInjectedNonClosureReducerInstance()
    {
        Subject::$reducers = [
            'reducerAlpha' => [
                ['reducer' => 'not a closure', 'priority' => 0],
            ],
        ];

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Reducer is not a closure.');

        (new Subject)->reducerAlpha(3);
    }

}
