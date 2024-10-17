<?php

namespace App\Enums;

enum RclResponsesEnum: string
{
    case TooEarly = "RCL REJECTED\nRCL SENT TOO EARLY";
    case TooLate = "RCL REJECTED\nRCL RECEIVED TOO LATE REVERT TO VOICE PROCEDURES";
    case OclServiceNotAvailable = "RCL REJECTED\nOCL SERVICE NOT CURRENTLY AVAILABLE\nREVERT TO VOICE PROCEDURES";
    case Negotiation = "RCL RECEIVED\nNEGOTIATION REQUIRED CONTACT %s BY VOICE";
    case Cancelled = "RCL REJECTED\nCLEARANCE CALLED REVERT TO VOICE PROCEDURES";
    case Invalid = "RCL REJECTED\nINVAILD %s\nRESUBMIT YOUR REQUEST";
    case Contact = "RCL RECEIVED\nCONTACT %s BY VOICE";
    case Acknowledge = "RCL RECEIVED\nCONTINUE TO MONITOR NATTRAK UNTIL ENTERING OCA";
    case AcknowledgeMoved = "RCL RECEIVED BY %s\nCONTINUE TO MONITOR NATTRAK";

    public function text(): string
    {
        return match ($this) {
            self::TooEarly => 'Check your ETA, and re-request not before 60 minutes prior to entry.',
            self::TooLate => 'Check your ETA. If less than 10 minutes from entry, natTrak cannot be used. Contact OCA by voice immediately.',
            self::OclServiceNotAvailable => 'natTrak service is not available at this time. Revert to voice.',
            self::Negotiation => 'Negotiation of your clearance with the controller is required. Contact the controller specified via voice.',
            self::Cancelled => 'Clearance cancelled by the controller. Revert to voice or check private messages in pilot client.',
            self::Invalid => 'Check the identified error, amend and re-submit. For help contact the controller.',
            self::Contact => 'Revert to voice.',
            self::Acknowledge => 'Request acknowledged. Continue as planned. Monitor natTrak for ATC amendments.'
        };
    }
}
