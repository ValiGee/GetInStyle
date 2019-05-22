<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testShowRegistration()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    public function testRegister()
    {
        $data = [];
        $response = $this->post('/register', $data);
        $response->assertRedirect('/');

        $data = [
            'name' => 'Andrei',
            'email' => 'andrei@zugravu.com',
            'password' => bcrypt('parola'),
        ];
        $response = $this->post('/register', $data);
        $response->assertRedirect('/');
    }

    public function testLoginForm()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function testLogin()
    {
        $data = [];
        $response = $this->call('post', '/login', $data);
        $response->assertRedirect('/');

        $data = [
            'name' => 'name1',
            'email' => 'email1@email.com',
            'password' => bcrypt('parola'),
        ];
        $response = $this->call('post', '/login', $data);
        $response->assertRedirect('/');
    }

    public function testUserMedia()
    {
        $this->get(route('media.photosByUserId', 1))->assertStatus(200);
        $this->get(route('media.photosByUserId', 100))->assertStatus(404);
    }
}
