<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;
use App\Models\Exercise;
use App\Models\CardDetail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelCardDetailTest extends TestCase
{
    public function test_it_should_create_card_relationship()
    {
        $user = User::factory()->create(['role'=>'0']);
        $card = Card::factory()->create(['name'=>'test card', 'user_id'=>$user->id, 'creator_user_id'=>$user->id]);
        $card_detail = CardDetail::factory()->create(['card_id'=>$card->id]);

        $stored_card_detail = CardDetail::find($card->cardDetails[0]->id);

        $this->assertEquals($card_detail->card->id, $card->id);
        $this->assertEquals($stored_card_detail->card->id, $card->id);

        $card_detail->delete();
        $card->delete();
        $user->delete();
    }

    public function test_it_should_create_exercise_relationships()
    {
        $user = User::factory()->create(['role'=>'0']);
        $card = Card::factory()->create(['name'=>'test card', 'user_id'=>$user->id, 'creator_user_id'=>$user->id]);
        $exercise = Exercise::factory()->create(['name'=>'test exercise', 'creator_user_id'=>$user->id]);
        $card_detail = CardDetail::factory()->create(['card_id'=>$card->id, 'exercise_id'=>$exercise->id]);

        $stored_card_detail = CardDetail::find($card->cardDetails[0]->id);

        $this->assertEquals($card_detail->exercise->id, $exercise->id);
        $this->assertEquals($stored_card_detail->exercise->id, $exercise->id);

        $card_detail->delete();
        $card->delete();
        $user->delete();
        $exercise->delete();
    }
}
