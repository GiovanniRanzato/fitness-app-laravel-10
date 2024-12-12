<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\TermsOfService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ControllerTermsOfServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_evryone_should_retrieve_latest_term_of_service(): void
    {
        $terms_of_service = TermsOfService::factory()->create();
        $response = $this->get('api/v1/terms-of-service/latest');
        $terms_of_service->delete();

        $response->assertStatus(200);
    }

    public function test_it_should_fail_retrieve_latest_term_of_service(): void
    {
        $response = $this->get('api/v1/terms-of-service/latest');
        $response->assertStatus(404);
        $response->assertJson([
            'message' => "Not Found.",
        ]);
    }
}
