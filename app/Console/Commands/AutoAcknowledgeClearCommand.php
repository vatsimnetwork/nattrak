<?php

namespace App\Console\Commands;

use App\Enums\DatalinkAuthorities;
use App\Enums\RclResponsesEnum;
use App\Models\ClxMessage;
use App\Models\RclMessage;
use App\Services\CpdlcService;
use Illuminate\Console\Command;

class AutoAcknowledgeClearCommand extends Command
{
    protected $signature = 'rcl-messages:clear-auto-acknowledged';

    protected $description = 'Move auto acknowledged RCL messages from pending list to processed list';

    public function handle(): void
    {
        $rclMessages = RclMessage::pending()->where('is_acknowledged', false)->get();
        foreach ($rclMessages as $rclMessage) {
            // Don't interfere with clearances being edited
            if ($rclMessage->isEditLocked()) {
                continue;
            }
            $datalinkAuthority = DatalinkAuthorities::SYS;
            $cpdlcService = new CpdlcService();
            $cpdlcService->sendMessage(
                author: $datalinkAuthority,
                recipient: $rclMessage->callsign,
                recipientAccount: $rclMessage->vatsimAccount,
                message: sprintf(RclResponsesEnum::AcknowledgeMoved->value, strtoupper($datalinkAuthority->description())),
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
                'datalink_authority' => $datalinkAuthority,
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
}
