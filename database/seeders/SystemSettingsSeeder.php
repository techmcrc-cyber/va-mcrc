<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'MAX_ADDITIONAL_MEMBERS',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Maximum number of additional participants allowed per booking'
            ],
            [
                'key' => 'API_KEY',
                'value' => 'mcrc_retreat_api_key_2025',
                'type' => 'string',
                'description' => 'API authentication key for external integrations'
            ],
            [
                'key' => 'CANCELLATION_DEADLINE_DAYS',
                'value' => '1',
                'type' => 'integer',
                'description' => 'Minimum days before retreat start date to allow cancellation'
            ],
            [
                'key' => 'ENABLE_EMAIL_NOTIFICATIONS',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable or disable email notifications system-wide'
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
