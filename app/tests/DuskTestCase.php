<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }

        static::configureArtifacts();
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1280,800',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--remote-allow-origins=*',
            '--lang=en-US',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    protected static function configureArtifacts(): void
    {
        $root = storage_path('framework/dusk');

        File::ensureDirectoryExists($root.'/screenshots');
        File::ensureDirectoryExists($root.'/console');
        File::ensureDirectoryExists($root.'/source');

        static::$storeScreenshotsAt = $root.'/screenshots';
        static::$storeConsoleLogAt = $root.'/console';
        static::$storeSourceAt = $root.'/source';
    }
}
