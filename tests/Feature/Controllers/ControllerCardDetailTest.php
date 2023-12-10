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

class ControllerCardDetailTest extends TestCase
{
    use RefreshDatabase;
     
    private $card_detail_attributes = [
        'id',
        'quantity',
        'time_duration',
        'time_recovery',
        'weight',
        'notes',
    ];

    private function getCardDetailData()
    {
        return [
            'quantity' => rand(1,100),
            'time_duration' => rand(1,1000),
            'time_recovery' => rand(1,1000),
            'weight' => rand(1,100),
            'notes' => Str::random(120),

        ];
    }

    private function assert_stored_card_detail_equals_exercise_data($stored_card_detail_data, $card_detail_data){
        $this->assertEquals($stored_card_detail_data->quantity, $card_detail_data['quantity']);
        $this->assertEquals($stored_card_detail_data->time_duration, $card_detail_data['time_duration']);
        $this->assertEquals($stored_card_detail_data->time_recovery, $card_detail_data['time_recovery']);
        $this->assertEquals($stored_card_detail_data->weight, $card_detail_data['weight']);
        $this->assertEquals($stored_card_detail_data->notes, $card_detail_data['notes']);
    }

    public function test_utilities() {

        $card_detail_data = $this->getCardDetailData();
        $user = User::factory()->create(['role' => '0']);
        $exercise = Exercise::factory()->create(['creator_user_id' => $user->id]);
        $card = Card::factory()->create(['user_id'=> $user->id, 'creator_user_id' => $user->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $card_detail = CardDetail::factory()->create($card_detail_data);

        $stored_data = CardDetail::find($card_detail->id);

        $user->tokens()->delete();
        $user->delete();
        $card_detail->delete();
        $exercise->delete();
        $card->delete();

        $this->assert_stored_card_detail_equals_exercise_data($stored_data, $card_detail_data);
    }

    public function test_admin_should_create_card_details()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $admin->id]);
        $card = Card::factory()->create(['user_id'=> $admin->id, 'creator_user_id' => $admin->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $response = $this->post('api/v1/card-details/', $card_detail_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($response->json()['data']['attributes']['id']);
 
        $stored_data->delete();
        $admin->tokens()->delete();
        $admin->delete();
        $stored_data->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_detail_attributes,
                'exercise' => [
                    'attributes'
                ]
            ],
        ]);
        $this->assert_stored_card_detail_equals_exercise_data($stored_data, $card_detail_data);
    }

    public function test_admin_should_update_card_details()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $admin->id]);
        $card = Card::factory()->create(['user_id'=> $admin->id, 'creator_user_id' => $admin->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $card_detail = CardDetail::factory()->create($card_detail_data);

        $updated_card_detail_data = $this->getCardDetailData();
        $response = $this->patch('api/v1/card-details/'.$card_detail->id, $updated_card_detail_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($card_detail->id);
 
        $stored_data->delete();
        $admin->tokens()->delete();
        $admin->delete();
        $stored_data->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_detail_attributes,
                'exercise' => [
                    'attributes'
                ]
            ],
        ]);
        $this->assert_stored_card_detail_equals_exercise_data($stored_data, $updated_card_detail_data);
    }

    public function test_admin_should_delete_card_details()
    {
        $admin = User::factory()->create(['role' => '1']);
        $token = $admin->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $admin->id]);
        $card = Card::factory()->create(['user_id'=> $admin->id, 'creator_user_id' => $admin->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $card_detail = CardDetail::factory()->create($card_detail_data);

        $response = $this->delete('api/v1/card-details/'.$card_detail->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($card_detail->id);
        if($stored_data)
            $stored_data->delete();
       
        $admin->tokens()->delete();
        $admin->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(200);
    }

    public function test_trainer_should_create_card_details()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $trainer->id]);
        $card = Card::factory()->create(['user_id'=> $trainer->id, 'creator_user_id' => $trainer->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $response = $this->post('api/v1/card-details/', $card_detail_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($response->json()['data']['attributes']['id']);
 
        $stored_data->delete();
        $trainer->tokens()->delete();
        $trainer->delete();
        $stored_data->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_detail_attributes,
                'exercise' => [
                    'attributes'
                ]
            ],
        ]);
        $this->assert_stored_card_detail_equals_exercise_data($stored_data, $card_detail_data);
    }

    public function test_trainer_should_update_card_details()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $trainer->id]);
        $card = Card::factory()->create(['user_id'=> $trainer->id, 'creator_user_id' => $trainer->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $card_detail = CardDetail::factory()->create($card_detail_data);

        $updated_card_detail_data = $this->getCardDetailData();
        $response = $this->patch('api/v1/card-details/'.$card_detail->id, $updated_card_detail_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($card_detail->id);
 
        $stored_data->delete();
        $trainer->tokens()->delete();
        $trainer->delete();
        $stored_data->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'attributes' => $this->card_detail_attributes,
                'exercise' => [
                    'attributes'
                ]
            ],
        ]);
        $this->assert_stored_card_detail_equals_exercise_data($stored_data, $updated_card_detail_data);
    }

    public function test_trainer_should_delete_card_details()
    {
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $trainer->id]);
        $card = Card::factory()->create(['user_id'=> $trainer->id, 'creator_user_id' => $trainer->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $card_detail = CardDetail::factory()->create($card_detail_data);

        $response = $this->delete('api/v1/card-details/'.$card_detail->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($card_detail->id);
        if($stored_data)
            $stored_data->delete();
       
        $trainer->tokens()->delete();
        $trainer->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(200);
    }

    public function test_trainer_should_not_update_other_trainer_card_details()
    {
        $other_trainer = User::factory()->create(['role' => '2']);
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $other_trainer->id]);
        $card = Card::factory()->create(['user_id'=> $other_trainer->id, 'creator_user_id' => $other_trainer->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $card_detail = CardDetail::factory()->create($card_detail_data);

        $updated_card_detail_data = $this->getCardDetailData();
        $response = $this->patch('api/v1/card-details/'.$card_detail->id, $updated_card_detail_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($card_detail->id);
 
        $stored_data->delete();
        $trainer->tokens()->delete();
        $trainer->delete();
        $stored_data->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
        $this->assert_stored_card_detail_equals_exercise_data($stored_data, $card_detail_data);
    }

    public function test_trainer_should_not_delete_other_trainer_card_details()
    {
        $other_trainer = User::factory()->create(['role' => '2']);
        $trainer = User::factory()->create(['role' => '2']);
        $token = $trainer->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $other_trainer->id]);
        $card = Card::factory()->create(['user_id'=> $other_trainer->id, 'creator_user_id' => $other_trainer->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $card_detail = CardDetail::factory()->create($card_detail_data);

        $response = $this->delete('api/v1/card-details/'.$card_detail->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($card_detail->id);
        if($stored_data)
            $stored_data->delete();
       
        $trainer->tokens()->delete();
        $trainer->delete();
        $stored_data->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_user_should_not_create_card_details()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $user->id]);
        $card = Card::factory()->create(['user_id'=> $user->id, 'creator_user_id' => $user->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $response = $this->post('api/v1/card-details/', $card_detail_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $user->tokens()->delete();
        $user->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function test_user_should_not_update_card_details()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $user->id]);
        $card = Card::factory()->create(['user_id'=> $user->id, 'creator_user_id' => $user->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $card_detail = CardDetail::factory()->create($card_detail_data);

        $updated_card_detail_data = $this->getCardDetailData();
        $response = $this->patch('api/v1/card-details/'.$card_detail->id, $updated_card_detail_data, ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($card_detail->id);
 
        $stored_data->delete();
        $user->tokens()->delete();
        $user->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
        $this->assert_stored_card_detail_equals_exercise_data($stored_data, $card_detail_data);
    }

    public function test_user_should_not_delete_card_details()
    {
        $user = User::factory()->create(['role' => '0']);
        $token = $user->createAuthToken();
        $exercise = Exercise::factory()->create(['creator_user_id' => $user->id]);
        $card = Card::factory()->create(['user_id'=> $user->id, 'creator_user_id' => $user->id]);
        
        $card_detail_data = $this->getCardDetailData();
        $card_detail_data['card_id'] = $card->id;
        $card_detail_data['exercise_id'] = $exercise->id;
        $card_detail = CardDetail::factory()->create($card_detail_data);

        $response = $this->delete('api/v1/card-details/'.$card_detail->id, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

        $stored_data = CardDetail::find($card_detail->id);
        if($stored_data)
            $stored_data->delete();
       
        $user->tokens()->delete();
        $user->delete();
        $stored_data->delete();
        $exercise->delete();
        $card->delete();

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message'
        ]);
    }
}