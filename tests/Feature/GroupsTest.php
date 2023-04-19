<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GroupsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    public function test_create_group_invalid_api_id()
    {
        $groupName = 'Test Group';

        $response = $this->post('/create_group', [
            'api_id' => 999, //replace with an invalid API id
            'group_name' => $groupName,
        ]);

        $response->assertStatus(500);
        $this->assertDatabaseMissing('groups', [
            'name' => $groupName,
        ]);
        
    }

}
