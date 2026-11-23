<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific
| PHPUnit test case class. By default, that class is "PHPUnit\Framework\TestCase".
| Of course, you may need to change it using the "pest()" function to bind a
| different class for the different test types in your application.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Browser');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain
| conditions. The "expect()" function gives you access to a set of "expectations"
| methods you can use on that value. You may extend these as needed.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| Pest allows you to define custom functions inside this file, which can
| then be used within your tests. These functions are a great way to
| write reusable test helpers, like a "createUser" helper.
|
*/

function something()
{
    // ..
}