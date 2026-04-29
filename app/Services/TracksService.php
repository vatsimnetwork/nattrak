<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TracksService
{
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
        $entries = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0',
        ])->get(config('services.tracks.nat_notams_url'))->json();

        // Group parts by NOTAM and order each group by part number
        $groups = collect($entries)
            ->groupBy('notam_number_formatted')
            ->map(fn (Collection $parts) => $parts->sortBy('part_no')->values());

        $messages = [];
        /** @var Collection $parts */
        foreach ($groups as $parts) {
            // Each message is split into lines by CRLF.
            // Line 1 is the header, line 2 is the validity timespan, line 3 is the part header,
            // the following lines are the part content, and the last line is the part footer.
            // They aren't needed due to the new structured API data, so we slice them off.
            $message = $parts->flatMap(fn ($part) => Str::of($part['condition_message'])
                ->explode("\r\n")
                ->filter()
                ->slice(3, -1)
            );

            $validFrom = Carbon::parse($parts[0]['start_datetime']);
            $validTo = Carbon::parse($parts[0]['end_datetime']);

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
        }

        return $messages;
    }
}
