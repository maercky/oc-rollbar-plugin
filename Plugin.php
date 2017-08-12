<?php

namespace Maercky\Rollbar;

use Event;
use Log;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Rollbar\Rollbar;
use System\Classes\PluginBase;
use VojtaSvoboda\ErrorLogger\Models\Settings;

/**
 * @author Tim Vermaercke <tim@timvermaercke.be>
 *
 * Extension to Vojta Svoboda's Error Logger, so Rollbar works with it.
 */
class Plugin extends PluginBase
{
    /**
     * Require Vojta Svoboda's Error Logger
     *
     * @var array
     */
    public $require = [
        'VojtaSvoboda.ErrorLogger',
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'maercky.rollbar::lang.plugin.name',
            'description' => 'maercky.rollbar::lang.plugin.description',
            'author' => 'Tim Vermaercke',
            'icon' => 'icon-bug',
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        $monolog = Log::getMonolog();
        $this->addRollbarHandler($monolog);

        // Extend Vojta Svoboda's Error Logger settings page
        Event::listen('backend.form.extendFields', function ($widget) {
            if (!$widget->model instanceof Settings) {
                return;
            }

            $widget->addTabFields([
                'rollbar_enabled' => [
                    'tab' => 'maercky.rollbar::lang.tab.name',
                    'label' => 'maercky.rollbar::lang.fields.rollbar_enabled.label',
                    'type' => 'switch',
                ],
                'rollbar_access_token' => [
                    'tab' => 'maercky.rollbar::lang.tab.name',
                    'label' => 'maercky.rollbar::lang.fields.rollbar_access_token.label',
                    'comment' => 'maercky.rollbar::lang.fields.rollbar_access_token.comment',
                    'required' => true,
                    'trigger' => [
                        'action' => 'show',
                        'field' => 'rollbar_enabled',
                        'condition' => 'checked',
                    ],
                ],
                'rollbar_environment' => [
                    'tab' => 'maercky.rollbar::lang.tab.name',
                    'label' => 'maercky.rollbar::lang.fields.rollbar_environment.label',
                    'comment' => 'maercky.rollbar::lang.fields.rollbar_environment.comment',
                    'trigger' => [
                        'action' => 'show',
                        'field' => 'rollbar_enabled',
                        'condition' => 'checked',
                    ],
                ],
            ]);
        });
    }

    /**
     * Add Rollbar to the Monolog Handler
     *
     * @param Logger $monolog
     * @return Logger
     */
    private function addRollbarHandler(Logger $monolog)
    {
        $settings = ['rollbar_enabled', 'rollbar_access_token', 'rollbar_environment'];

        if (!$this->checkIfSettingsAreFilledOut($settings)) {
            return $monolog;
        }

        $config = [
            'access_token' => Settings::get('rollbar_access_token'),
            'environment' => Settings::get('rollbar_environment'),
        ];

        Rollbar::init($config);
        $monolog->pushHandler(new PsrHandler(Rollbar::logger()));
    }

    /**
     * Check whether all settings are filled out, and if so, return true.
     * If not, this extension should not be loaded.
     *
     * @param array $settings
     * @return bool
     */
    private function checkIfSettingsAreFilledOut(array $settings = [])
    {
        foreach ($settings as $setting) {
            $value = Settings::get($setting);
            if (!$value) {
                return false;
            }
        }

        return true;
    }
}
