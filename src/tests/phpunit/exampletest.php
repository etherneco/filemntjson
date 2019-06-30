<<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExistTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    protected $baseUrl = 'https://localhost/';
    
    public function testBasicExample()
    {
        $this->visit('/')
             ->see('Testeintrag');
    }
}
