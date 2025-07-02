<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TracksService
{
    public const NAT_NOTAMS_URL = 'https://www.notams.faa.gov/common/nat.html';

    public const MONTHS = [
        'JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4, 'MAY' => 5, 'JUN' => 6,
        'JUL' => 7, 'AUG' => 8, 'SEP' => 9, 'OCT' => 10, 'NOV' => 11, 'DEC' => 12,
    ];

    public function getTmi(): int|string|null
    {
        $messages = $this->getMessages();

        return $messages[0]['tmi'] ?? ($this->getEstimatedTmi() ?? null);
    }

    /**
     * @return int|string|null
     *
     * Returns an estimate of the real world TMI based off the day of the year. * added to denote such.
     */
    private function getEstimatedTmi(): int|string|null
    {
        $now = Carbon::now();
        return $now->dayOfYear . '*';
    }

    public function getTracks(): ?array
    {
        $tracks = [];

        foreach ($this->getMessages() as $message) {
            foreach ($message['tracks'] as $track) {
                $tracks[] = [
                    'ident' => $track['ident'],
                    'route' => $track['route'],
                    'valid_from' => $message['valid_from'],
                    'valid_to' => $message['valid_to'],
                    'flight_levels' => $track['flight_levels'],
                    'direction' => $track['direction'],
                ];
            }
        }

        return $tracks;
    }

    private function getMessages(): array
    {
        $html = Http::get(self::NAT_NOTAMS_URL)->body();
        $html = strip_tags($html);

        // Each NOTAM is enclosed in \x02, parentheses, and \n\v\x03... thanks FAA?
        preg_match_all('/\x02\((.*?)\)\n\v\x03/s', $html, $matches);
        $notams = $matches[1];

        $messages = [];
        $message = [];
        foreach ($notams as $text) {
            $lines = explode("\n", $text);

            // Each message is split into lines by \n
            // Line 1 is the header, line 2 is the validity timespan, line 3 is the part header,
            // the following lines are the part content, and the last line is the part footer.

            // Parse header
            preg_match('/^NAT-([1-9])\/([1-9])/', $lines[0], $matches);
            $currentPart = (int) $matches[1];
            $totalParts = (int) $matches[2];

            // Parse validity
            preg_match('/^([A-Z]{3}) ([0-9]{2})\/([0-9]{2})([0-9]{2})Z TO ([A-Z]{3}) ([0-9]{2})\/([0-9]{2})([0-9]{2})Z$/', $lines[1], $matches);
            $validFrom = Carbon::create(year: null, month: self::MONTHS[$matches[1]], day: $matches[2], hour: $matches[3], minute: $matches[4]);
            $validTo = Carbon::create(year: null, month: self::MONTHS[$matches[5]], day: $matches[6], hour: $matches[7], minute: $matches[8]);

            // Combine parts
            $message = array_merge($message, array_slice($lines, 3, -1));

            // Haven't received all parts yet
            if ($currentPart !== $totalParts) {
                continue;
            }

            // Split the full message into its sections
            $sections = [];
            $section = [];
            foreach ($message as $line) {
                $section[] = rtrim($line, '-');

                if (str_ends_with($line, '-')) {
                    $sections[] = implode("\n", $section);
                    $section = [];
                }
            }

            // Parse sections
            $tracks = [];
            $remarks = [];
            foreach ($sections as $section) {
                if (preg_match('/^([A-Z]) (.*?)\nEAST LVLS (.*?)\nWEST LVLS (.*?)\n/s', $section, $matches)) {
                    if ($matches[3] === 'NIL') {
                        $direction = 'west';
                        $rawLevels = $matches[4];
                    } elseif ($matches[4] === 'NIL') {
                        $direction = 'east';
                        $rawLevels = $matches[3];
                    } else {
                        $direction = 'unknown';
                        $rawLevels = '';
                    }

                    $tracks[] = [
                        'ident' => $matches[1],
                        'route' => $matches[2],
                        'direction' => $direction,
                        'flight_levels' => array_map(fn ($fl) => intval($fl) * 100, explode(' ', $rawLevels)),
                    ];
                } else {
                    $remarkLines = array_slice(explode("\n", $section), 1);

                    $remark = [];
                    foreach ($remarkLines as $remarkLine) {
                        if (preg_match('/^\d+\. ?(.*)/', $remarkLine, $matches)) {
                            if ($remark) {
                                $remarks[] = implode("\n", $remark);
                            }

                            $remark = [$matches[1]];
                            continue;
                        }

                        $remark[] = $remarkLine;
                    }
                    $remarks[] = implode("\n", $remark);
                }
            }

            $tmi = 0;
            foreach ($remarks as $remark) {
                if (preg_match('/^TMI IS ([0-9]{3})/', $remark, $matches)) {
                    $tmi = (int) $matches[1];
                    break;
                }
            }

            // Finalize message
            $messages[] = [
                'tmi' => $tmi,
                'valid_from' => $validFrom,
                'valid_to' => $validTo,
                'tracks' => $tracks,
                'remarks' => $remarks,
            ];
            $message = [];
        }

        return $messages;
    }
}
