<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function faker() : \Faker\Generator
    {
        return \Faker\Factory::create();
    }
}
