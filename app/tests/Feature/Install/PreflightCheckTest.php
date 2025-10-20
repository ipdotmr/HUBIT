<?php

namespace Tests\Feature\Install;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PreflightCheckTest extends TestCase
{
    public function test_preflight_endpoint_returns_check_results(): void
    {
        $originalConfig = config('install');

        try {
            $response = $this->postJson('/api/install/preflight');

            $response
                ->assertOk()
                ->assertJson(
                    fn (AssertableJson $json) => $json
                        ->whereType('ok', 'boolean')
                        ->has('checks')
                        ->has(
                            'checks',
                            fn (AssertableJson $checkJson) => $checkJson
                                ->each(
                                    fn (AssertableJson $check) => $check
                                        ->whereAllType([
                                            'key' => 'string',
                                            'label' => 'string',
                                            'status' => 'boolean',
                                            'message' => 'string',
                                            'severity' => 'string',
                                        ])
                                        ->etc()
                                )
                        )
                );

            $this->assertNotEmpty($response->json('checks'));
        } finally {
            config()->set('install', $originalConfig);
        }
    }

    public function test_optional_failures_do_not_block_progress(): void
    {
        $originalConfig = config('install');

        config()->set('install.extensions.required', []);
        config()->set('install.extensions.optional', [
            'totally_fake' => 'Totally Fake Extension',
        ]);
        config()->set('install.paths', []);

        try {
            $response = $this->postJson('/api/install/preflight');

            $response
                ->assertOk()
                ->assertJsonPath('ok', true);

            $checks = collect($response->json('checks'));

            $this->assertTrue(
                $checks->contains(
                    fn (array $check) => $check['key'] === 'php.extension.totally_fake'
                        && $check['severity'] === 'optional'
                        && $check['status'] === false
                )
            );
        } finally {
            config()->set('install', $originalConfig);
        }
    }
}
