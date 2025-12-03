<?php

namespace App\Services;

use App\Enums\RclResponsesEnum;
use App\Exceptions\InvalidTrackException;
use App\Models\ClxMessage;
use App\Models\DatalinkAuthority;
use App\Models\RclMessage;
use App\Models\Track;

class ClxMessageService
{
    private CpdlcService $cpdlcService;

    public function __construct()
    {
        $this->cpdlcService = new CpdlcService();
    }

    /**
     * @param string $newTrackId
     * @param string $newRandomRouteing
     * @return array
     * @throws InvalidTrackException
     */
    public function getNewEntryFixOrTrack(?string $newTrackId, ?string $newRandomRouteing): array
    {
        $newTrack = null;
        $newEntryFix = null;

        if ($newTrackId) {
            $newTrack = Track::active()->where('id', $newTrackId)->first();
            if (! $newTrack) {
                throw new InvalidTrackException();
            }
            $newEntryFix = strtok($newTrack->last_routeing, ' ');
        }
        elseif ($newRandomRouteing) {
            $newEntryFix = strtok($newRandomRouteing, ' ');
        }

        return [
            'newTrack' => $newTrack,
            'newEntryFix' => $newEntryFix,
        ];
    }

    /**
     * @param string $entryTimeType
     * @param string $entryTimeRequirement
     * @return string|null
     */
    public function formatEntryTimeRequirement(?string $entryTimeType, ?string $entryTimeRequirement): ?string
    {
        if ($entryTimeType != 'none') {
            return "{$entryTimeType}{$entryTimeRequirement}";
        } else {
            return null;
        }
    }

    public function createDatalinkMessage(ClxMessage $clxMessage): array
    {
        $clxMessage->load(['rclMessage', 'datalinkAuthority']);

        $array = [
            'CLX '.now()->format('Hi dmy').' '.$clxMessage->datalinkAuthority->id.' CLRNCE '.$clxMessage->id,
            $clxMessage->rclMessage->callsign.' CLRD TO '.$clxMessage->rclMessage->destination.' VIA '.$clxMessage->entry_fix,
            $clxMessage->track ? 'NAT '.$clxMessage->track->identifier : 'RANDOM ROUTE',
            $clxMessage->track ? $clxMessage->track->last_routeing : $clxMessage->random_routeing,
        ];
        if ($clxMessage->rclMessage->is_concorde) {
            $array[] = 'FM '.$clxMessage->entry_fix.'/'.$clxMessage->rclMessage->entry_time.' MNTN BLOCK LOWER F'.$clxMessage->flight_level.' UPPER F'.$clxMessage->upper_flight_level.' M'.$clxMessage->mach;
        } else {
            $array[] = 'FM '.$clxMessage->entry_fix.'/'.$clxMessage->rclMessage->entry_time.' MNTN F'.$clxMessage->flight_level.' M'.$clxMessage->mach;
        }
        // Only show crossing restriction if entry time =/= the restriction due to the bodge
        if ($clxMessage->entry_time_restriction && ($clxMessage->raw_entry_time_restriction != $clxMessage->rclMessage->entry_time)) {
            $array[] = "/ATC CROSS {$clxMessage->entry_fix} {$clxMessage->formatEntryTimeRestriction()}";
        }
        if (($clxMessage->mach != $clxMessage->rclMessage->mach) || ($clxMessage->rclMessage->latestClxMessage && ($clxMessage->mach != $clxMessage->rclMessage->latestClxMessage->mach))) {
            $array[] = '/ATC SPEED CHANGED';
        }
        if (($clxMessage->flight_level != $clxMessage->rclMessage->flight_level) || ($clxMessage->rclMessage->latestClxMessage && ($clxMessage->flight_level != $clxMessage->rclMessage->latestClxMessage->flight_level))) {
            $array[] = '/ATC FLIGHT LEVEL CHANGED';
        }
        if ($clxMessage->routeing_changed) {
            if ($clxMessage->track) {
                $array[] = '/ROUTE CHANGED TO TRACK '.$clxMessage->track->identifier;
            } else {
                $array[] = '/ROUTE CHANGED TO RANDOM ROUTEING';
            }
        }
        if ($clxMessage->free_text) {
            $array[] = '/ATC '.strtoupper($clxMessage->free_text);
        }
        $array[] = 'END OF MESSAGE';

        return $array;
    }

    public function createSimpleDatalinkMessage(ClxMessage $clxMessage): string
    {
        $msg = '';
        if ($clxMessage->track) {
            $msg = "{$clxMessage->datalinkAuthority->name} clears {$clxMessage->rclMessage->callsign} to {$clxMessage->rclMessage->destination} via {$clxMessage->entry_fix}, track {$clxMessage->track->identifier}. From {$clxMessage->entry_fix} maintain Flight Level {$clxMessage->flight_level}, Mach {$clxMessage->mach}.";
        } else {
            $msg = "{$clxMessage->datalinkAuthority->name} clears {$clxMessage->rclMessage->callsign} to {$clxMessage->rclMessage->destination} via {$clxMessage->entry_fix}, random routeing {$clxMessage->random_routeing}. From {$clxMessage->entry_fix} maintain Flight Level {$clxMessage->flight_level}, Mach {$clxMessage->mach}.";
        }
        // Only show crossing restriction if entry time =/= the restriction due to the bodge
        if ($clxMessage->entry_time_restriction && ($clxMessage->raw_entry_time_restriction != $clxMessage->rclMessage->entry_time)) {
            $msg .= " Cross {$clxMessage->entry_fix} ".strtolower($clxMessage->formatEntryTimeRestriction()).'.';
        }
        if (($clxMessage->mach != $clxMessage->rclMessage->mach) || ($clxMessage->rclMessage->latestClxMessage && ($clxMessage->mach != $clxMessage->rclMessage->latestClxMessage->mach))) {
            $msg .= ' Speed changed.';
        }
        if (($clxMessage->flight_level != $clxMessage->rclMessage->flight_level) || ($clxMessage->rclMessage->latestClxMessage && ($clxMessage->flight_level != $clxMessage->rclMessage->latestClxMessage->flight_level))) {
            $msg .= ' Flight level changed.';
        }
        if ($clxMessage->free_text) {
            $msg .= " {$clxMessage->free_text}";
        }
        return $msg;
    }

    public function moveAutoAcknowledgedRclToProcessedList(RclMessage $rclMessage, bool $overrideEditLock = false)
    {
        // Don't interfere with clearances being edited
        if (!$overrideEditLock && $rclMessage->isEditLocked()) {
            return;
        }
        $datalinkAuthority = DatalinkAuthority::find('SYST');
        $this->cpdlcService->sendMessage(
            author: $datalinkAuthority,
            recipient: $rclMessage->callsign,
            recipientAccount: $rclMessage->vatsimAccount,
            message: sprintf(RclResponsesEnum::AcknowledgeMoved->value, strtoupper($datalinkAuthority->name)),
            caption: RclResponsesEnum::Acknowledge->text()
        );

        $clxMessage = new ClxMessage([
            'vatsim_account_id' => $rclMessage->vatsim_account_id,
            'rcl_message_id' => $rclMessage->id,
            'flight_level' => $rclMessage->flight_level,
            'upper_flight_level' => $rclMessage->upper_flight_level ? $rclMessage->upper_flight_level : null,
            'mach' => $rclMessage->mach,
            'entry_fix' => $rclMessage->entry_fix,
            'entry_time_restriction' => null,
            'raw_entry_time_restriction' => $rclMessage->entry_time,
            'free_text' => "** AUTO ACKNOWLEDGE **",
            'datalink_authority_id' => $datalinkAuthority->id,
            'is_concorde' => $rclMessage->is_concorde,
            'simple_datalink_message' => '** AUTO ACKNOWLEDGED REFER RCL REQUEST **',
            'datalink_message' => ['** AUTO ACKNOWLEDGED REFER RCL REQUEST **'],
        ]);

        /**
         * Assign track or RR
         */
        if ($rclMessage->track) {
            $clxMessage->track_id = $rclMessage->track->id;
            $clxMessage->random_routeing = null;
        } elseif ($rclMessage->random_routeing) {
            $clxMessage->random_routeing = $rclMessage->random_routeing;
            $clxMessage->track_id = null;
        }

        /**
         * Save
         */
        $clxMessage->save();

        /**
         * Assign Clx message to Rcl
         */
        $rclMessage->clx_message_id = $clxMessage->id;
        $rclMessage->save();

        //ClxIssuedEvent::dispatch($rclMessage->vatsimAccount, $clxMessage);

        activity('datalink')
            ->causedBy($clxMessage->vatsimAccount)
            ->performedOn($rclMessage)
            ->withProperties(['datalink' => $clxMessage->data_link_message])
            ->log('Pending Auto Acknowledge Move');
    }
}
