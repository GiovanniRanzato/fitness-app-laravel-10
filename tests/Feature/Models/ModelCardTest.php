<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Card;
use App\Models\User;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\CardDetail;


class ModelCardTest extends TestCase
{
    public function test_it_should_create_user_realtionship()
    {
        $user = User::factory()->create(['role'=>'0']);
        $card = Card::factory()->create(['name'=>'test card', 'user_id'=>$user->id, 'creator_user_id'=>$user->id]);
        $user->cards()->saveMany([$card]);
        $stored_card = Card::find($user->cards[0]->id);

        $this->assertEquals($card->user->id, $user->id);
        $this->assertEquals($stored_card->user->id, $user->id);
    }

    public function test_it_should_create_creator_user_relationship()
    {
        $user = User::factory()->create(['role'=>'0']);
        $creator_user = User::factory()->create(['role'=>'2']);
        $card = Card::factory()->create(['name'=>'test card', 'user_id'=>$user->id, 'creator_user_id'=>$creator_user->id]);
        $user->cards()->saveMany([$card]);

        $stored_card = Card::find($user->cards[0]->id);
        
        $this->assertEquals($card->creatorUser->id, $creator_user->id);
        $this->assertEquals($stored_card->creatorUser->id, $creator_user->id);
    }

    
    public function test_it_should_create_category_relationship()
    {
        $user = User::factory()->create(['role'=>'0']);
        $category = Category::factory()->create(['name'=>'test category']);
        $card = Card::factory()->create(['name'=>'test card', 'user_id'=>$user->id, 'creator_user_id'=>$user->id]);
        $card->category()->associate($category);

        $user->cards()->saveMany([$card]);

        $stored_card = Card::find($user->cards[0]->id);

        $this->assertEquals($card->category->id, $category->id);
        $this->assertEquals($stored_card->category->id, $category->id);

        $user->cards()->delete();
        $user->delete();
        $category->delete();
 
    }

    public function test_it_should_create_card_details_relationship() {
        $user = User::factory()->create(['role'=>'0']);
        $card = Card::factory()->create(['name'=>'test card', 'user_id'=>$user->id, 'creator_user_id'=>$user->id]);
        $card_detail = CardDetail::factory()->create(['card_id' => $card->id]);
        
        $card->cardDetails()->saveMany([$card_detail]);

        $stored_card = Card::find($user->cards[0]->id);

        $this->assertEquals($card->cardDetails[0]->id, $card_detail->id);
        $this->assertEquals($stored_card->cardDetails[0]->id, $card_detail->id);

        $card_detail->delete();
        $user->cards()->delete();
        $user->delete();

    }
}
