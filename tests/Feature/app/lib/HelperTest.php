<?php

namespace Tests\Feature\app\lib;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HelperTest extends TestCase
{
   public function testMapPropsToParams()
   {
       $props = [
           'a' => 'A',
           'b' => 'C'
       ];

       $strict  = map_props_to_params($props, ['a','b','c']);
       $loose  = map_props_to_params($props, ['a','b','c'], false);

       $this->assertArrayNotHasKey('c',$strict, "Key missing in strict mode");
       $this->assertArrayHasKey('c',$loose, "Key present in strict mode");
   }
}
