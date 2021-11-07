<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Board;
use App\Models\User;
class BoardTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_authenticated_user_can_read_all_the_boards()
    {
        $this->actingAs(User::factory('App\User')->create());
        $board = Board::factory('App\Board')->create();
        $response = $this->get('/boards');
        $response->assertSee($board->title);
    }

    /** @test */
    public function authenticated_users_can_create_a_new_board()
    {
        $this->actingAs(User::factory('App\User')->create());
        $board = Board::factory('App\Board')->make();
        $this->post('/boards',$board->toArray());
        $this->assertEquals(1,Board::all()->count());
    }

    /** @test */
    public function unauthenticated_users_cannot_create_a_new_board()
    {
        $board = Board::factory('App\Board')->make();
        $this->post('/boards',$board->toArray())
             ->assertRedirect('/login');
    }

    /** @test */
    public function a_board_requires_a_title()
    {
        $this->actingAs(User::factory('App\User')->create());
        $board = Board::factory('App\Board')->make(['title' => null]);
        $this->post('/boards',$board->toArray())
                ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_board_requires_a_description()
    {
        $this->actingAs(User::factory('App\User')->create());
        $board = Board::factory('App\Board')->make(['description' => null]);
        $this->post('/boards',$board->toArray())
            ->assertSessionHasErrors('description');
    }
    /** @test */
    public function authorized_user_can_update_the_board()
    {
        $this->actingAs(User::factory('App\User')->create());
        $board = Board::factory('App\Board')->create();
        $board->title = "Updated Title";
        $this->put('/boards/'.$board->id, $board->toArray());
        $this->assertDatabaseHas('boards',['id'=> $board->id , 'title' => 'Updated Title']);

    }
    /** @test */
    public function unauthorized_user_cannot_update_the_board()
    {
        $this->actingAs(User::factory('App\User')->create());
        $board = Board::factory('App\Board')->create();
        $board->title = "Updated Title";
        $response = $this->put('/boards/'.$board->id, $board->toArray());
        $response->assertStatus(302);
    }
}