<?php

namespace Tests\Feature\Routes;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;

class RouteCardTest extends TestCase
{
    public function test_not_auth_should_not_get_cards_data()
    {
        $response = $this->json('GET', 'api/v1/cards', ['Accept' => 'application/json']);
        $response->assertStatus(401);
    }

    public function test_not_auth_should_not_create_cards()
    {
        $response = $this->post('api/v1/cards/', [], ['Accept' => 'application/json']);
        $response->assertStatus(401);
    }

    public function test_not_auth_should_not_update_cards()
    {
        $user = User::factory()->create();
        $card = Card::factory()->create(['user_id' => $user->id, 'creator_user_id' => $user->id]);
    
        $response = $this->patch('api/v1/cards/'.$card->id, [], ['Accept' => 'application/json',]);

        $card->delete();
        $user->delete();

        $response->assertStatus(401);
    }

    public function test_not_auth_should_not_delete_cards()
    {
        $user = User::factory()->create();
        $card = Card::factory()->create(['user_id' => $user->id, 'creator_user_id' => $user->id]);
    
        $response = $this->delete('api/v1/cards/'. $card->id, [], ['Accept' => 'application/json']);
        $stored_data = Card::find($card->id);
        $user->delete();
        if($stored_data) $stored_data->delete();
        
        $this->assertNotNull($stored_data);
        $response->assertStatus(401);
    }

}



