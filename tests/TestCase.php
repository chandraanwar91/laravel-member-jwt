<?php
namespace Tests;
use Laravel\Lumen\Testing\TestCase  as BaseTest;

class TestCase extends BaseTest
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
