<?php

namespace Tests\Feature\Install;

use App\Support\Installer\RequirementChecker;
use Mockery;
use Tests\TestCase;

class InstallerPrecheckTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_precheck_displays_pass_and_fail_statuses(): void
    {
        $checker = Mockery::mock(RequirementChecker::class);
        $checker->shouldReceive('getChecks')->once()->andReturn([
            [
                'key' => 'php',
                'label' => 'PHP',
                'status' => 'pass',
                'message' => 'All good',
                'required' => true,
            ],
            [
                'key' => 'ext_missing',
                'label' => 'Extension: missing',
                'status' => 'fail',
                'message' => 'Install extension',
                'required' => true,
            ],
        ]);
        $checker->shouldReceive('allRequiredPass')->once()->andReturn(false);

        $this->app->instance(RequirementChecker::class, $checker);

        $response = $this->get('/install');

        $response->assertOk();
        $response->assertSee('✅', false);
        $response->assertSee('❌', false);
        $response->assertSeeText('Extension: missing');
    }
}
