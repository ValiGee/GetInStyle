<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $this->get('/media')->assertStatus(200);
    }

    public function testShowMedia()
    {
        $this->get(route('media.show', 1))->assertStatus(200);
        $this->get(route('media.show', 100))->assertStatus(404);
    }

    public function testMediaPreview()
    {
        $data = [];
        $this->json('post', route('media.preview'), $data)->assertStatus(422);
    }

    public function testMediaCreate()
    {
        $this->get(route('media.create'))->assertStatus(200);
    }

    public function testStoreMedia()
    {
        $data = [];
        $this->json('post', route('media.store'), $data)->assertStatus(401);

        $user = \App\User::first();
        $this->actingAs($user)->json('post', route('media.store'), $data)->assertStatus(422);
        
        $media = \App\Media::first();
        $data = [
            'stylized_path' => $media->stylized_path,
            'original_path' => 'ceva',
            'style_id' => 1,
            'tags' => [],
            'description' => null,
        ];

        $this->json('post', route('media.store'), $data)->assertStatus(422);
    }

    public function testMediaLike()
    {
        $this->json('post', route('media.like', 1))->assertStatus(401);

        $user = \App\User::first();
        $this->actingAs($user)->json('post', route('media.like', 100))->assertStatus(404);
        $this->actingAs($user)->json('post', route('media.like', 1))->assertStatus(200)->assertJson(['status' => 'success',
            'message' => '',]);
    }
}
