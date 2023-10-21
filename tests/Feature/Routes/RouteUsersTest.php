<?php

namespace Tests\Feature\Routes;

use DateTime;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteUsersTest extends TestCase
{
    
    public function test_not_auth_should_not_get_users_data()
    {
        $response = $this->get('api/v1/users', ['Accept' => 'application/json']);
        $response->assertStatus(401);
    }

    public function test_not_auth_should_not_update_user()
    {
        $response = $this->patch('api/v1/users/1', [], ['Accept' => 'application/json']);
        $response->assertStatus(401);
    }

    public function test_not_auth_should_not_delete_user()
    {
        $user = User::factory()->create();
    
        $response = $this->delete('api/v1/users/'. $user->id, [], ['Accept' => 'application/json']);
        $stored_data = User::find($user->id);

        if($stored_data) $stored_data->delete();

        $this->assertNotNull($stored_data);
        $response->assertStatus(401);
    }
}
