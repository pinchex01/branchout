<?php

namespace Tests\Feature\app\Models;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetOrCreateUser()
    {
        $user = User::getOrCreate(29569823, [
            'email' => "mitacliff@gmail.com",
            'phone' => '0702997218'
        ]);

        $this->assertTrue(is_object($user));
    }
}
