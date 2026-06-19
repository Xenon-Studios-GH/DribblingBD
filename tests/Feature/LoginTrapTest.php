<?php

namespace Tests\Feature;

use App\Models\LoginTrap;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTrapTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_trap_record()
    {
        $trap = LoginTrap::create([
            'ip_address' => '192.168.1.1',
            'attempted_email' => 'attacker@test.com',
            'trigger_reason' => 'excessive_attempts',
        ]);

        $this->assertDatabaseHas('login_traps', [
            'ip_address' => '192.168.1.1',
            'status' => 'active',
        ]);
    }

    public function test_can_release_trap()
    {
        $trap = LoginTrap::create([
            'ip_address' => '192.168.1.1',
            'trigger_reason' => 'excessive_attempts',
        ]);

        $trap->release();

        $this->assertEquals('released', $trap->fresh()->status);
        $this->assertNotNull($trap->fresh()->released_at);
    }

    public function test_active_scope()
    {
        LoginTrap::create(['ip_address' => '1.1.1.1', 'trigger_reason' => 'test', 'status' => 'active']);
        LoginTrap::create(['ip_address' => '2.2.2.2', 'trigger_reason' => 'test', 'status' => 'released']);

        $this->assertCount(1, LoginTrap::active()->get());
    }
}
