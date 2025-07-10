<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Exam;
use App\Models\User;
use App\Models\ExamAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->admin()->create();
        $this->studentUser = User::factory()->create(); // Default role is student
    }

    /** @test */
    public function admin_can_view_admin_dashboard()
    {
        // Seed some data for dashboard stats
        Exam::factory()->recycle($this->adminUser)->count(3)->create();
        User::factory()->count(5)->create(); // 5 more students (total 6 with $this->studentUser)
        ExamAttempt::factory()->count(10)->create();

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHasAll([
            'totalExams',
            'totalStudents',
            'totalAttempts',
            'recentAttempts',
        ]);

        // Check if counts are correct
        // Note: totalStudents counts users with 'student' role.
        // $this->studentUser is one, User::factory()->count(5) are five more.
        // The admin user from setUp is not counted.
        $this->assertEquals(3, $response->viewData('totalExams'));
        $this->assertEquals(User::where('role', 'student')->count(), $response->viewData('totalStudents'));
        $this->assertEquals(10, $response->viewData('totalAttempts'));
        $this->assertCount(min(5, 10), $response->viewData('recentAttempts')); // Shows up to 5
    }

    /** @test */
    public function student_cannot_view_admin_dashboard()
    {
        $response = $this->actingAs($this->studentUser)->get(route('admin.dashboard'));
        $response->assertRedirect(route('student.dashboard')); // Or 403 depending on RoleMiddleware
    }

    /** @test */
    public function guest_cannot_view_admin_dashboard()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }
}
