<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStoreComment()
    {
        $data = [];
        $user = \App\User::first();
        $this->json('post', route('comments.store'), $data)->assertStatus(401);
        $this->actingAs($user)->json('post', route('comments.store'), $data)->assertStatus(422);
        $data = [
            'message' => 'Ceva',
            'media_id' => 1,
            'parent_id' => null,
        ];
        $this->actingAs($user)->json('post', route('comments.store'), $data)->assertStatus(200);
        $this->assertDatabaseHas('comments', $data);
    }

    public function testCommentLike()
    {
        $this->json('post', route('comments.like', 1))->assertStatus(401);

        $user = \App\User::first();
        $this->actingAs($user)->json('post', route('comments.like', 100))->assertStatus(404);
        $this->actingAs($user)->json('post', route('comments.like', 1))->assertStatus(200)->assertJson(['status' => 'success',
            'message' => '',]);
    }
}
