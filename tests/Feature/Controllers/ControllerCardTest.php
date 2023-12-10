<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;
use App\Models\Exercise;
use App\Models\CardDetail;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ControllerCardTest extends TestCase
{
    private $card_attributes = [
        'id',
        'name',
        'disabled',
        'date_from',
        'date_to'
    ];

    private function getCardData()
    {
        return [
            'name' => Str::random(10),
            'disabled' => rand(0, 1) == 1 ? true : false,
            'date_from' => today()->format('Y-m-d'),
            'date_to' => today()->addDays(7)->format('Y-m-d'),
        ];
    }

    private function assert_stored_card_equals_card_data($stored_card, $card_data){
        $this->assertEquals($stored_card->name, $card_data['name']);
        $this->assertEquals($stored_card->disabled, $card_data['disabled']);
        $this->assertEquals($stored_card->date_from->format('Y-m-d'), $card_data['date_from']);
        $this->assertEquals($stored_card->date_to->format('Y-m-d'), $card_data['date_to']);
    }

    
    public function test_admin_should_get_card_data()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $card = Card::factory()->create(['user_id' => $admin->id, 'creator_user_id' => $admin->id]);

        $response = $this->get('api/v1/cards/'.$card->id, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $card->delete();
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_attributes,
                'card_details' => [
                    '*' => [
                        'attributes',
                        'exercise' => [
                            'attributes'
                        ]
                    ]
                ],
                'user' => ['attributes']
            ]
        ]);
    }

    public function test_admin_should_not_found_not_existing_card()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $not_existing_card_id = 1;
        $check_card_exist = Card::find($not_existing_card_id);
        if($check_card_exist)
            $check_card_exist->delete();

        $response = $this->get('api/v1/cards/'.$not_existing_card_id, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message'
        ]);
        $this->assertEquals($response->json()['message'], 'Not Found.');
    }

    public function test_admin_should_get_cards_data()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $card_1 = Card::factory()->create(['user_id' => $admin->id, 'creator_user_id' => $admin->id]);
        $card_2 = Card::factory()->create(['user_id' => $admin->id, 'creator_user_id' => $admin->id]);

        $response = $this->get('api/v1/cards/', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $card_1->delete();
        $card_2->delete();
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'attributes' => $this->card_attributes,
                    'card_details' => [
                        '*' => [
                            'attributes',
                            'exercise' => [
                                'attributes'
                            ]
                        ]
                    ],
                    'user' => ['attributes']
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function test_admin_should_create_cards()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $card_data = $this->getCardData();
        $card_data['user_id'] = $admin->id;
        $card_data['creator_user_id'] = $admin->id;

        $response = $this->post('api/v1/cards/', $card_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Card::find($response->json()['data']['attributes']['id']);
        
        $stored_data->delete();
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_attributes,
                'card_details' => [
                    '*' => [
                        'attributes',
                        'exercise' => [
                            'attributes'
                        ]
                    ]
                ],
                'user' => ['attributes']
            ]
        ]);
        $this->assert_stored_card_equals_card_data($stored_data, $card_data);
    }

    public function test_admin_should_update_cards()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $card = Card::factory()->create(['user_id' => $admin->id, 'creator_user_id' => $admin->id]);
        $card_data = $this->getCardData();

        $response = $this->patch('api/v1/cards/'.$card->id, $card_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Card::find($response->json()['data']['attributes']['id']);
        
        $stored_data->delete();
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_attributes,
                'card_details' => [
                    '*' => [
                        'attributes',
                        'exercise' => [
                            'attributes'
                        ]
                    ]
                ],
                'user' => ['attributes']
            ]
        ]);
        $this->assert_stored_card_equals_card_data($stored_data, $card_data);
    }

    public function test_admin_should_delete_cards()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $card = Card::factory()->create(['user_id' => $admin->id, 'creator_user_id' => $admin->id]);
        $card_data = $this->getCardData();

        $response = $this->delete('api/v1/cards/'.$card->id, $card_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Card::find($card->id);
        
        if($stored_data)
            $stored_data->delete();
            
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);
    }

    public function test_trainer_should_get_card_data_created_by_him()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $other_trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $card_0 = Card::factory()->create(['user_id' => $trainer->id, 'creator_user_id' => $trainer->id]);
        $card_1 = Card::factory()->create(['user_id' => $trainer->id, 'creator_user_id' => $trainer->id]);
        $other_trainer_card = Card::factory()->create(['user_id' => $other_trainer->id, 'creator_user_id' => $other_trainer->id]);

        $response = $this->get('api/v1/cards', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $card_0->delete();
        $card_1->delete();
        $other_trainer_card->delete();
        $trainer->tokens()->delete();
        $trainer->delete();
        $other_trainer->delete();

        $response->assertStatus(200);
        $json = $response->json();

        $stored_cards = $json['data'];
        $this->assertEquals(count($stored_cards), 2);
        $this->assert_stored_card_equals_card_data($card_0, $stored_cards[0]['attributes']);
        $this->assert_stored_card_equals_card_data($card_1, $stored_cards[1]['attributes']);
        
        $response->assertJsonStructure([
            'data' => [
                [
                    'attributes' => $this->card_attributes,
                    'card_details' => [
                        '*' => [
                            'attributes',
                            'exercise' => [
                                'attributes'
                            ]
                        ]
                    ],
                    'user' => ['attributes']
               ]
            ],
            'links',
            'meta'
        ]);
    }

    public function test_trainer_should_not_get_card_data_not_created_by_him()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $other_trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $other_trainer_card = Card::factory()->create(['user_id' => $other_trainer->id, 'creator_user_id' => $other_trainer->id]);

        $response = $this->get('api/v1/cards', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $other_trainer_card->delete();
        $trainer->tokens()->delete();
        $trainer->delete();
        $other_trainer->delete();

        $response->assertStatus(200);
        $json = $response->json();

        $this->assertEquals(count($json['data']), 0);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta'
        ]);
    }

    public function test_trainer_should_create_cards()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $card_data = $this->getCardData();
        $card_data['user_id'] = $trainer->id;
        $card_data['creator_user_id'] = $trainer->id;

        $response = $this->post('api/v1/cards/', $card_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Card::find($response->json()['data']['attributes']['id']);
        
        $stored_data->delete();
        $trainer->tokens()->delete();
        $trainer->delete();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_attributes,
                'card_details' => [
                    '*' => [
                        'attributes',
                        'exercise' => [
                            'attributes'
                        ]
                    ]
                ],
                'user' => ['attributes']
            ]
        ]);
        $this->assert_stored_card_equals_card_data($stored_data, $card_data);
    }

    public function test_trainer_should_update_card_data_created_by_him()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $card = Card::factory()->create(['user_id' => $trainer->id, 'creator_user_id' => $trainer->id]);

        $card_data = $this->getCardData();
        $response = $this->patch('api/v1/cards/'.$card->id, $card_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
     
        $stored_data = Card::find($response->json()['data']['attributes']['id']);

        $card->delete();
        $trainer->tokens()->delete();
        $trainer->delete();


        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_attributes,
                'card_details' => [
                    '*' => [
                        'attributes',
                        'exercise' => [
                            'attributes'
                        ]
                    ]
                ],
                'user' => ['attributes']
            ]
        ]);
        $this->assert_stored_card_equals_card_data($stored_data, $card_data);
    }

    public function test_trainer_should_delete_cards()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $card = Card::factory()->create(['user_id' => $trainer->id, 'creator_user_id' => $trainer->id]);
        $card_data = $this->getCardData();

        $response = $this->delete('api/v1/cards/'.$card->id, $card_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $stored_data = Card::find($card->id);
        
        if($stored_data)
            $stored_data->delete();
            
        $trainer->tokens()->delete();
        $trainer->delete();

        $response->assertStatus(200);
    }

    public function test_trainer_should_not_update_card_data_not_created_by_him()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $other_trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $card = Card::factory()->create(['user_id' => $trainer->id, 'creator_user_id' => $other_trainer->id]);

        $card_data = $this->getCardData();
        $response = $this->patch('api/v1/cards/'.$card->id, $card_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
       
        $stored_data = Card::find($card->id);

        $card->delete();
        $trainer->tokens()->delete();
        $trainer->delete();
        $other_trainer->delete();
        

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_trainer_should_not_delete_card_data_not_created_by_him()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $other_trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $card = Card::factory()->create(['user_id' => $trainer->id, 'creator_user_id' => $other_trainer->id]);

        $response = $this->delete('api/v1/cards/'.$card->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
       
        $stored_data = Card::find($card->id);

        $card->delete();
        $trainer->tokens()->delete();
        $trainer->delete();
        $other_trainer->delete();
        

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_users_should_get_only_card_data_assignet_to_him()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $card_0 = Card::factory()->create(['user_id' => $user->id, 'creator_user_id' => $trainer->id]);
        $card_1 = Card::factory()->create(['user_id' => $user->id, 'creator_user_id' => $trainer->id]);
        $other_user_card = Card::factory()->create(['user_id' => $trainer->id, 'creator_user_id' => $trainer->id]);

        $response = $this->get('api/v1/cards', ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        $card_0->delete();
        $card_1->delete();
        $user->tokens()->delete();
        $user->delete();
        $trainer->delete();

        $response->assertStatus(200);
        $json = $response->json();

        $stored_cards = $json['data'];
        $this->assertEquals(count($stored_cards), 2);
        $this->assert_stored_card_equals_card_data($card_0, $stored_cards[0]['attributes']);
        $this->assert_stored_card_equals_card_data($card_1, $stored_cards[1]['attributes']);
        
        $response->assertJsonStructure([
            'data' => [
                [
                    'attributes' => $this->card_attributes,
                    'card_details' => [
                        '*' => [
                            'attributes',
                            'exercise' => [
                                'attributes'
                            ]
                        ]
                    ],
                    'user' => ['attributes']
                ]
            ],
            'links',
            'meta'
        ]);
    }

    public function test_user_should_not_update_card_data()
    {
        $user = User::factory()->create(['role' => '0']);
        $trainer = User::factory()->create(['role' => '2']);
        $token = $user->createAuthToken();
        $card = Card::factory()->create(['user_id' => $user->id, 'creator_user_id' => $trainer->id]);

        $response = $this->patch('api/v1/cards/'.$card->id, $this->getCardData(), ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
       
        $stored_data = Card::find($card->id);

        $card->delete();
        $trainer->tokens()->delete();
        $trainer->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_user_should_not_delete_card_data()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $trainer = User::factory()->create(['role' => '2']);
        $card = Card::factory()->create(['user_id' => $user->id, 'creator_user_id' => $trainer->id]);

        $response = $this->delete('api/v1/cards/'.$card->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
       
        $stored_data = Card::find($card->id);

        $card->delete();
        $user->tokens()->delete();
        $user->delete();
        $trainer->delete();
        
        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
        $this->assertNotNull($stored_data);
    }

    public function test_users_should_get_card_detail_and_exercise()
    {
        $admin = User::factory()->create(['role' => '0']);
        $token = $admin->createAuthToken();
        
        $exercise = Exercise::factory()->create(['creator_user_id' => $admin->id]);
        $card = Card::factory()->create(['user_id' => $admin->id, 'creator_user_id' => $admin->id]);
        $card_detail = CardDetail::factory()->create(['card_id' => $card->id, 'exercise_id' => $exercise->id]);

        $response = $this->get('api/v1/cards/'.$card->id, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
        
        $card_detail->delete();
        $card->delete();
        $admin->tokens()->delete();
        $admin->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_attributes,
                'card_details' => [
                    '*' => [
                        'attributes',
                        'exercise' => [
                            'attributes'
                        ]
                    ]
                ],
                'user' => [ 'attributes' ] 
            ]
        ]);
    }
}
