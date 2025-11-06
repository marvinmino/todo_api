<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Helpers\TestHelper;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, TestHelper;
}
