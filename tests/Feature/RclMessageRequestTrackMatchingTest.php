<?php

namespace Tests\Feature;

use App\Enums\AccessLevelEnum;
use App\Models\DatalinkAuthority;
use App\Models\Track;
use App\Models\VatsimAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RclMessageRequestTrackMatchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_rr_request_matching_active_track_is_rejected_with_helpful_message(): void
    {
        config()->set('app.rcl_time_constraints_enabled', false);
        config()->set('app.rcl_rr_matching_track_action', 'reject');

        Gate::define('activePilot', fn (VatsimAccount $account) => true);

        $pilot = VatsimAccount::create([
            'given_name' => 'Test',
            'surname' => 'Pilot',
            'rating_int' => 1,
            'access_level' => AccessLevelEnum::Pilot,
        ]);
        $this->actingAs($pilot);

        DatalinkAuthority::create([
            'id' => 'OCEN',
            'name' => 'Oceanic',
            'prefix' => 'OC',
            'auto_acknowledge_participant' => false,
            'valid_rcl_target' => true,
            'system' => false,
        ]);

        Track::create([
            'identifier' => 'P',
            'active' => true,
            'last_routeing' => 'GOMUP 59/20 59/30 58/40 JANJO',
            'valid_from' => now(),
            'valid_to' => now()->addHours(6),
            'last_active' => now(),
        ]);

        $response = $this->from(route('pilots.rcl.create'))
            ->post(route('pilots.rcl.store'), [
                'callsign' => 'BAW123',
                'destination' => 'EGLL',
                'flight_level' => '340',
                'max_flight_level' => '390',
                'mach' => '080',
                'entry_fix' => 'GOMUP',
                'entry_time' => '1200',
                'tmi' => '123',
                'track_id' => null,
                'random_routeing' => 'gomup   59/20 59/30 58/40 janjo',
                'is_concorde' => '0',
                'target_datalink_authority_id' => 'OCEN',
            ]);

        $response->assertRedirect(route('pilots.rcl.create'));
        $response->assertSessionHasErrors([
            'random_routeing' => 'Your requested random routeing exactly matches NAT Track P. Please re-submit your request and select Track P instead of RR.',
        ]);
        $this->assertDatabaseCount('rcl_messages', 0);
    }

    public function test_rr_request_with_non_matching_routeing_is_accepted(): void
    {
        config()->set('app.rcl_time_constraints_enabled', false);
        config()->set('app.rcl_auto_acknowledgement_enabled', false);
        config()->set('app.rcl_rr_matching_track_action', 'reject');

        Gate::define('activePilot', fn (VatsimAccount $account) => true);

        $pilot = VatsimAccount::create([
            'given_name' => 'Test',
            'surname' => 'Pilot',
            'rating_int' => 1,
            'access_level' => AccessLevelEnum::Pilot,
        ]);
        $this->actingAs($pilot);

        DatalinkAuthority::create([
            'id' => 'OCEN',
            'name' => 'Oceanic',
            'prefix' => 'OC',
            'auto_acknowledge_participant' => false,
            'valid_rcl_target' => true,
            'system' => false,
        ]);

        Track::create([
            'identifier' => 'P',
            'active' => true,
            'last_routeing' => 'GOMUP 59/20 59/30 58/40 JANJO',
            'valid_from' => now(),
            'valid_to' => now()->addHours(6),
            'last_active' => now(),
        ]);

        $response = $this->post(route('pilots.rcl.store'), [
            'callsign' => 'BAW123',
            'destination' => 'EGLL',
            'flight_level' => '340',
            'max_flight_level' => '390',
            'mach' => '080',
            'entry_fix' => 'GOMUP',
            'entry_time' => '1200',
            'tmi' => '123',
            'track_id' => null,
            'random_routeing' => 'GOMUP 58/20 57/30 JANJO',
            'is_concorde' => '0',
            'target_datalink_authority_id' => 'OCEN',
        ]);

        $response->assertRedirect(route('pilots.message-history'));
        $this->assertDatabaseHas('rcl_messages', [
            'callsign' => 'BAW123',
            'track_id' => null,
            'random_routeing' => 'GOMUP 58/20 57/30 JANJO',
        ]);
    }

    public function test_rr_request_matching_active_track_is_silently_converted_when_configured(): void
    {
        config()->set('app.rcl_time_constraints_enabled', false);
        config()->set('app.rcl_auto_acknowledgement_enabled', false);
        config()->set('app.rcl_rr_matching_track_action', 'convert');

        Gate::define('activePilot', fn (VatsimAccount $account) => true);

        $pilot = VatsimAccount::create([
            'given_name' => 'Test',
            'surname' => 'Pilot',
            'rating_int' => 1,
            'access_level' => AccessLevelEnum::Pilot,
        ]);
        $this->actingAs($pilot);

        DatalinkAuthority::create([
            'id' => 'OCEN',
            'name' => 'Oceanic',
            'prefix' => 'OC',
            'auto_acknowledge_participant' => false,
            'valid_rcl_target' => true,
            'system' => false,
        ]);

        $track = Track::create([
            'identifier' => 'P',
            'active' => true,
            'last_routeing' => 'GOMUP 59/20 59/30 58/40 JANJO',
            'valid_from' => now(),
            'valid_to' => now()->addHours(6),
            'last_active' => now(),
        ]);

        $response = $this->post(route('pilots.rcl.store'), [
            'callsign' => 'BAW123',
            'destination' => 'EGLL',
            'flight_level' => '340',
            'max_flight_level' => '390',
            'mach' => '080',
            'entry_fix' => 'ZZZZZ',
            'entry_time' => '1200',
            'tmi' => '123',
            'track_id' => null,
            'random_routeing' => 'gomup   59/20 59/30 58/40 janjo',
            'is_concorde' => '0',
            'target_datalink_authority_id' => 'OCEN',
        ]);

        $response->assertRedirect(route('pilots.message-history'));
        $this->assertDatabaseHas('rcl_messages', [
            'callsign' => 'BAW123',
            'track_id' => $track->id,
            'random_routeing' => null,
            'entry_fix' => 'GOMUP',
        ]);
    }
}
