<?php

namespace Tests\Feature;

use App\Models\JenisSK;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class APITest extends TestCase
{
  use RefreshDatabase;
  /**
   * A basic feature test example.
   */
  public function test_api_get_jabatans(): void
  {
    $response = $this->get('/api/jabatans/1');

    $response->assertStatus(200);
  
  }
  public function test_api_get_all_records_enis_sk(): void
  {
    $response = $this->get('/api/jenis-sk');

    $response->assertStatus(200);
    
  }
  public function test_api_get_one_record_jenis_sk(): void
  {
    $jenisSK = JenisSK::factory()->create();
    $response = $this->getJson('/api/jenis-sk/{$jenisSK->id}');
    

    // $response->assertStatus(200);
    $response->assertStatus(200)
    ->assertJsonStructure([
        'success',
        'data' => [
            'id',
            'nama', // Include actual fields from your model
          
        ],
    ])
    ->assertJson($jenisSK->toArray());

    
  }
}
