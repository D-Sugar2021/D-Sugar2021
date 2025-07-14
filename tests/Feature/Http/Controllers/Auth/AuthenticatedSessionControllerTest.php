<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->admin()->create([
            'password' => bcrypt('password123')
        ]);
        $this->studentUser = User::factory()->create([
            'role' => 'student',
            'password' => bcrypt('password123')
        ]);
    }

    /** @test */
    public function login_screen_can_be_rendered()
    {
        // Assuming 'auth.login' view exists (created by Breeze or manually)
        // If not, this test needs the view to be created.
        // For now, we'll assume it exists.
        // If 'login' route doesn't exist because auth routes aren't fully set up, this will fail.
        // My routes/auth.php defines it.
        $response = $this->get('/login');
        $response->assertStatus(200); // Or assert view if view exists
    }

    /** @test */
    public function admin_user_is_redirected_to_admin_dashboard_after_login()
    {
        $response = $this->post('/login', [
            'email' => $this->adminUser->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function student_can_login_with_email()
    {
        $response = $this->post(route('login'), [
            'login' => $this->studentUser->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->studentUser);
        $response->assertRedirect(route('student.dashboard'));
    }

    /** @test */
    public function student_can_login_with_username()
    {
        // Ensure the student has a username to test with
        $this->studentUser->update(['username' => 'testuser']);

        $response = $this->post(route('login'), [
            'login' => 'testuser',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->studentUser);
        $response->assertRedirect(route('student.dashboard'));
    }

    /** @test */
    public function user_with_unknown_role_is_redirected_to_default_home_after_login()
    {
        $unknownRoleUser = User::factory()->create(['role' => 'editor', 'password' => bcrypt('password123')]);
        $response = $this->post('/login', [
            'email' => $unknownRoleUser->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($unknownRoleUser);
        // RouteServiceProvider::HOME is '/dashboard'
        // My web.php /dashboard route also redirects based on role.
        // If role is unknown to that route, it shows 'dashboard' view.
        // This tests the AuthenticatedSessionController's fallback.
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    /** @test */
    public function users_can_not_authenticate_with_invalid_password()
    {
        $this->post('/login', [
            'email' => $this->studentUser->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function users_can_logout()
    {
        $this->actingAs($this->studentUser);
        $response = $this->post(route('logout'));
        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
