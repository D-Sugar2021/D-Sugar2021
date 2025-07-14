<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['password' => Hash::make('old-password')]);
    }

    /** @test */
    public function profile_page_is_displayed()
    {
        $response = $this->actingAs($this->user)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
    }

    /** @test */
    public function user_can_update_their_password()
    {
        $response = $this->actingAs($this->user)->put(route('password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('new-awesome-password', $this->user->refresh()->password));
    }

    /** @test */
    public function user_cannot_update_password_with_incorrect_current_password()
    {
        $response = $this->actingAs($this->user)->put(route('password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertFalse(Hash::check('new-awesome-password', $this->user->refresh()->password));
    }

    /** @test */
    public function new_password_must_be_confirmed()
    {
        $response = $this->actingAs($this->user)->put(route('password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'mismatched-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertFalse(Hash::check('new-awesome-password', $this->user->refresh()->password));
    }
}
