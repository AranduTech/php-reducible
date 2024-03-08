<?php

namespace Arandu\Reducible\Tests\Unit;

use Arandu\Reducible\Tests\Samples\Subject;
use Arandu\Reducible\Tests\TestCase;

class ReducibleTest extends TestCase
{
    public function testRegisterReducer()
    {
        $reducer = function ($value) {
            return $value + 1;
        };

        $reducer = Subject::reducer('reducerAlpha', $reducer);

        $this->assertIsCallable([Subject::class, 'reducerAlpha']);
        $this->assertEquals(5, Subject::reducerAlpha(4));
        $this->assertTrue(Subject::hasReducer('reducerAlpha'));

        $otherReducer = function ($value) {
            return $value + 2;
        };

        $otherReducer = Subject::reducer('reducerAlpha', $otherReducer);

        $this->assertEquals(count(Subject::getReducer('reducerAlpha')), 2);
    }

    public function testReducerPrecendence()
    {
        $reducer1 = function ($value) {
            return $value . " 1";
        };

        $reducer2 = function ($value) {
            return $value . " 2";
        };

        $reducer3 = function ($value) {
            return $value . " 3";
        };

        Subject::reducer('reducerBeta', $reducer1, 10);
        Subject::reducer('reducerBeta', $reducer2, 5);

        // print_r(Subject::getReducer('reducerBeta'));

        $this->assertEquals('foo 2 1', Subject::reducerBeta('foo'));

        Subject::reducer('reducerBeta', $reducer3, 7);

        $this->assertEquals('foo 2 3 1', Subject::reducerBeta('foo'));
    }

    public function testUnsubscribeReducer()
    {
        $reducer = function ($value) {
            return $value + 1;
        };

        Subject::reducer('reducerAlpha', $reducer);

        $this->assertIsCallable([Subject::class, 'reducerAlpha']);
        $this->assertEquals(5, Subject::reducerAlpha(4));

        Subject::removeReducer('reducerAlpha', $reducer);

        $this->assertEquals(0, count(Subject::getReducer('reducerAlpha')));
        $this->assertEquals(4, Subject::reducerAlpha(4));

        $unsubscribe = Subject::reducer('reducerAlpha', $reducer);

        $this->assertEquals(1, count(Subject::getReducer('reducerAlpha')));

        $unsubscribe();

        $this->assertEquals(0, count(Subject::getReducer('reducerAlpha')));

        Subject::reducer('reducerAlpha', $reducer);

        $this->assertEquals(1, count(Subject::getReducer('reducerAlpha')));

        Subject::clearReducer('reducerAlpha');

        $this->assertEquals(0, count(Subject::getReducer('reducerAlpha')));

    }

    public function testSubjectMethods()
    {
        $subject = new Subject();
        $this->assertEquals('foo', $subject->foo());
        $this->assertEquals('bar', $subject->bar());
        $this->assertEquals('baz', Subject::baz());
    }

    public function testReducersAcceptMultipleArguments()
    {
        $reducer = function ($value, $arg1, $arg2) {
            return $value + $arg1 + $arg2;
        };

        Subject::reducer('reducerAlpha', $reducer);

        $this->assertEquals(9, Subject::reducerAlpha(4, 3, 2));
        
        $reducer2 = function ($value, $arg1 = null) {
            if ($arg1 === null) {
                return $value;
            }
            return $value + $arg1;
        };

        Subject::reducer('reducerBeta', $reducer2);

        $this->assertEquals(5, Subject::reducerBeta(4, 1, 5, 7, 23));
        $this->assertEquals(4, Subject::reducerBeta(4));

        $reducer3 = function ($value, $arg1, $arg2 = null) {
            if ($arg2 === null) {
                return $value;
            }
            return $value + $arg1 + $arg2;
        };

        Subject::reducer('reducerGamma', $reducer3);

        $this->assertEquals(9, Subject::reducerGamma(4, 3, 2));
        $this->assertEquals(4, Subject::reducerGamma(4, 3));

    }

    public function testDocumentedArrayMergingWorks()
    {
        // Register a 'transformCallApiOptions' reducer
        Subject::reducer('transformCallApiOptions', function ($options) {
            return [
                ...$options,
                'headers' => [
                    ...($options['headers'] ?? []),
                    'Authorization' => 'Bearer 1234',
                ],
            ];
        });

        // Multiple reducers can be added
        Subject::reducer('transformCallApiOptions', function ($options) {
            return [
                ...$options,
                'headers' => [
                    ...($options['headers'] ?? []),
                    'X-Api-Key' => '1234',
                ],
            ];
        });

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];

        $options = Subject::transformCallApiOptions($options);

        $this->assertEquals([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer 1234',
                'X-Api-Key' => '1234',
            ]
        ], $options);
    }

    public function testReducersCanBeCalledFromInstance()
    {
        $reducer = function ($value) {
            return $value + 1;
        };

        Subject::reducer('reducerAlpha', $reducer);

        $subject = new Subject();

        $this->assertEquals(5, $subject->reducerAlpha(4));
    }
   
}
