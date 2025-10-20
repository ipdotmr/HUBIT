<?php

namespace Tests\Feature\Install;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PreflightCheckTest extends TestCase
{
    public function test_preflight_endpoint_returns_check_results(): void
    {
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
    }
}
