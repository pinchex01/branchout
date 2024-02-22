<?php

namespace Tests\Feature\app\validators;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ValidatorTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testValidIdNumber()
    {
        $valid = [
            'id_number' => '29569823',
            'first_name' => 'Cliff'
        ];

        $rules = [
            'id_number' => 'valid_id_number:first_name',
        ];
        $v = \Validator::make($valid, $rules);

        $this->assertTrue($v->passes());

    }
}
