<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JabatanTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_jabatans(): void
    {
        $response = $this->get('/api/jabatans');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
              '*' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
                'user_id'
              ]
            ]
          ]);    }
}
