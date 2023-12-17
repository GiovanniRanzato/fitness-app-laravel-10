<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Consent;
use App\Models\TermsOfService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ControllerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_handle_register_a_new_user() {
        $terms_of_service = TermsOfService::factory()->create();
        $user_data = [
            'name' => 'register_test',
            'email' => 'user@register.test',
            'password' => 'password',
            'accepted_terms_of_service_id' => $terms_of_service->id
        ];
    
        $response = $this->post('api/v1/register', $user_data, ['Accept' => 'application/json']);
        $response_json = $response->json();
        $stored_user = User::find($response_json['data']['attributes']['id']);
        $stored_consent = Consent::where('user_id', '=', $stored_user->id)->first();

        $terms_of_service->delete();
        $stored_consent->delete();
        $stored_user->tokens()->delete();
        $stored_user->delete();

        $this->assertEquals($stored_consent->text, $terms_of_service->text);
        $this->assertEquals($stored_user->name, $user_data['name']);
        $this->assertEquals($stored_user->email, $user_data['email']);
    }
}



