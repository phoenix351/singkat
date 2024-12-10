<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PegawaiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_api_get_pegawais(): void
    {
        $response = $this->get('/api/pegawais');

        $response->assertStatus(200);
        // dd($response);
        $response->assertJsonIsArray();
    }
}
