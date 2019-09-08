<?php

namespace Vanguard\Http\Controllers\Api;

use Settings;

/**
 * Class SettingsController
 * @package Vanguard\Http\Controllers\Api\Settings
 */
class SettingsController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:settings.general');
    }
    /**
     * System settings.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Settings::all();
    }
}
