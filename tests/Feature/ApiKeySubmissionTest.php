<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiKeySubmissionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testFormSubmission()
    {
        $response = $this->post('/check_key', [
            'title' => 'Tester',
            'api_key' => 'rubish_rubish',
        ]);

        $response->assertRedirect(route('home'))
             ->assertSessionHas('error', 'You entered an incorrect API key. Kindly check and try again.');

    }
}
