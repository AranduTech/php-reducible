<?php

namespace Arandu\Reducible;

trait Reducible
{

    static $reducers = [];

    static function reducer(string $key, \Closure $reducer, $priority = 10)
    {
        if (!isset(static::$reducers[$key])) {
            static::$reducers[$key] = [];
        }

        // less or equal priority
        $before = array_values(array_filter(static::$reducers[$key], function ($item) use ($priority) {
            return $item['priority'] <= $priority;
        }));

        // greater priority
        $after = array_values(array_filter(static::$reducers[$key], function ($item) use ($priority) {
            return $item['priority'] > $priority;
        }));

        static::$reducers[$key] = [
            ...$before,
            ['reducer' => $reducer, 'priority' => $priority],
            ...$after,
        ];

        return function () use ($key, $reducer) {
            static::removeReducer($key, $reducer);
        };
    }

    static function removeReducer(string $key, \Closure $reducer)
    {
        if (isset(static::$reducers[$key])) {
            $reducers = array_values(array_filter(static::$reducers[$key], function ($item) use ($reducer) {
                return $item['reducer'] !== $reducer;
            }));

            static::$reducers[$key] = $reducers;
        }
    }

    static function getReducer(string $key)
    {
        return static::$reducers[$key] ?? [];
    }

    static function hasReducer(string $key)
    {
        return isset(static::$reducers[$key]);
    }

    static function clearReducer(string $key)
    {

        static::$reducers[$key] = [];
    }

    static function flushReducers()
    {
        static::$reducers = [];
    }

    static function __callStatic($name, $arguments)
    {
        if (empty($arguments)) {
            throw new \ArgumentCountError('No value provided for reducer.');
        }

        $value = array_shift($arguments);

        return array_reduce(static::getReducer($name), function ($carry, $item) use ($arguments) {
            $reducer = $item['reducer'];

            if (!($reducer instanceof \Closure)) {
                throw new \TypeError('Reducer is not a closure.');
            }            

            $reducer = $reducer->bindTo(null, static::class);

            $expected = (new \ReflectionFunction($reducer))->getNumberOfParameters();

            $parameters = $arguments;

            if ($expected > count($parameters)) {
                $parameters = array_pad($parameters, $expected, null);
            }

            $parameters = array_slice($parameters, 0, $expected);

            return $reducer($carry, ...$parameters);
        }, $value);
    }

    function __call($method, $parameters)
    {
        if (empty($parameters)) {
            throw new \ArgumentCountError('No value provided for reducer.');
        }

        $value = array_shift($parameters);

        return array_reduce(static::getReducer($method), function ($carry, $item) use ($parameters) {
            $reducer = $item['reducer'];

            if (!($reducer instanceof \Closure)) {
                throw new \TypeError('Reducer is not a closure.');
            }

            $reducer = $reducer->bindTo($this, get_class($this));

            $expected = (new \ReflectionFunction($reducer))->getNumberOfParameters();

            $parameters = $parameters;

            if ($expected > count($parameters)) {
                $parameters = array_pad($parameters, $expected, null);
            }

            $parameters = array_slice($parameters, 0, $expected);

            return $reducer($carry, ...$parameters);
        }, $value);
    }
}