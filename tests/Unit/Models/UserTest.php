<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase; // Use RefreshDatabase for tests involving DB
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_be_admin()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isStudent());
        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('student'));
    }

    /** @test */
    public function user_can_be_student()
    {
        $user = User::factory()->create(['role' => 'student']);
        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isStudent());
        $this->assertFalse($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('student'));
    }

    /** @test */
    public function user_defaults_to_student_if_role_not_specified_on_model_level()
    {
        // Note: The migration defaults to 'student'.
        // User::factory() might override this if it has a default role.
        // Let's check the User model's behavior if a role is not mass-assigned
        // or if factory doesn't set it.
        // However, our UserFactory will likely set a default.
        // This test is more about the model's hasRole behavior with an explicit role.

        $user = User::factory()->create(); // Assuming factory default or no role set by factory initially
        // If factory sets a role, this test needs adjustment or a specific factory state.
        // For now, we rely on the migration default being applied if factory doesn't set it.

        // UserFactory now defaults to 'student'.
        $this->assertTrue($user->isStudent());
        $this->assertTrue($user->hasRole('student'));
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function user_has_role_is_case_sensitive_as_implemented()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('Admin')); // current hasRole is ===
    }
}
