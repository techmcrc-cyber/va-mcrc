<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Retreat;
use App\Models\Booking;
use Illuminate\Database\Seeder;

class UpdateOrganizationIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Mount Carmel Retreat Centre organization
        $organization = Organization::where('slug', 'mount-carmel-retreat-centre')->first();

        if (!$organization) {
            $this->command->error('Organization with slug "mount-carmel-retreat-centre" not found!');
            return;
        }

        // Update all retreats with the organization_id
        $retreatsUpdated = Retreat::whereNull('organization_id')
            ->orWhere('organization_id', 0)
            ->update(['organization_id' => $organization->id]);

        $this->command->info("Updated {$retreatsUpdated} retreats with organization_id: {$organization->id}");

        // Update all bookings with the organization_id
        $bookingsUpdated = Booking::whereNull('organization_id')
            ->orWhere('organization_id', 0)
            ->update(['organization_id' => $organization->id]);

        $this->command->info("Updated {$bookingsUpdated} bookings with organization_id: {$organization->id}");

        $this->command->info('Successfully updated all retreats and bookings!');
    }
}
