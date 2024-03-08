# Advaced Usage

## When should I use Reducible?

There maybe many reasons to use Reducible. However what motivated me was the need to allow certain aspects of a class to be modified by other classes. For example, I have a class that makes API calls and I want to allow other classes to modify the options that are passed to the API call.

This package is similar in some ways to the Laravel's `Macroable` macros, but with these key differences:

- It allows multiple closures to be registered for the same method name.
- When a reducer is called, at least one argument must be passed to it.
- A reducer closure should always return a value, which will be passed to the next reducer in the chain.
- If there are no reducers attached to a name, the method will return the first argument passed to it.

This allows for different packages to register their own reducers for the same method name, and for the class to call all of them in a predictable order.

## Registering a Reducer

To register a reducer, use the `reducer` method on the class. The first argument is the method name, and the second argument is a closure that will be called when the method is called. The third argument is optional and is the priority of the reducer. The default priority is 10.

```php
MyClass::reducer('example', function ($value, $arg1, $arg2) {
    // ...
}, 20);
```

### Reducer Priority

The priority of a reducer determines the order in which it will be called. The default priority is 10. A reducer with lower number will be called before a reducer with a higher number.

```php
MyClass::reducer('example', function ($value) {
    return $value . '1';
}, 20);
MyClass::reducer('example', function ($value) {
    return $value . '2';
}, 10);

MyClass::example('value'); // 'value21'
```

This is useful when you want to ensure that a certain reducer is called before or after another.

## Calling a Reducer

When a class uses the `Reducible` trait, any method called that is not declared by the class or its parents will be treated as a reducer. The method name will be the reducer name, and the arguments will be passed to the reducer chain.

```php
MyClass::flushReducers(); // Ensure no reducers are registered

echo MyClass::example('value'); // 'value'
```

If there is no reducer attached to the method name, it will return the value passed as the first argument.

## Removing a Reducer

To remove a reducer, use the `removeReducer` method on the class. The first argument is the method name, and the second argument is the closure to remove.

```php
MyClass::removeReducer('example', $closure);
```

Alternatively, the `reducer` method returns a `Closure` that can be used to remove the reducer.

```php
$unsubscribe = MyClass::reducer('example', function ($value) {
    return $value . '1';
});

$unsubscribe();
```

## Removing All Reducers

To remove all reducers for a method name, use the `clearReducer` method on the class.

```php
MyClass::clearReducer('example');
```

If you want to remove all reducers for all methods, use the `flushReducers` method on the class.

```php
MyClass::flushReducers();
```

## Reducer Context

When a reducer is called, the context of the class is passed to it. This allows the reducer to access the class's properties and methods.

```php
MyClass::reducer('example', function ($value) {
    return $value . $this->property; // $this will be in `MyClass` context
});
```
