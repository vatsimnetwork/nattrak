<?php

namespace App\Http\Livewire\Controllers;

use App\Models\RclMessage;
use App\Models\Track;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridEloquent;
use PowerComponents\LivewirePowerGrid\Traits\ActionButton;

final class PgPendingRclMessages extends PowerGridComponent
{
    public $tracks;

    use ActionButton;

    public string $sortField = 'request_time';

    public string $sortDirection = 'asc';

    public bool $withSortStringNumber = true;

    public int $perPage = 15;

    public array $perPageValues = [10, 15, 20, 25];

    /*
    |--------------------------------------------------------------------------
    |  Datasource
    |--------------------------------------------------------------------------
    | Provides data to your Table using a Model or Collection
    |
    */
    public function datasource(): ?Collection
    {
        $pendingRclMsgs = collect();
        foreach ($this->tracks as $track) {
            $trackMsgs = RclMessage::pending()->when(in_array($track, ['RR', 'CONC']) == false, function ($query) use ($track) {
                $query->where('track_id', Track::whereIdentifier($track)->firstOrFail()->id);
            }, function ($query) use ($track) {
                if ($track == 'RR') {
                    $query->where('track_id', null);
                } elseif ($track == 'CONC') {
                    $query->where('is_concorde', true);
                }
            })->get();
            foreach ($trackMsgs as $msg) {
                $pendingRclMsgs->add($msg);
            }
        }

        return $pendingRclMsgs;
    }

    /*
    |--------------------------------------------------------------------------
    |  Relationship Search
    |--------------------------------------------------------------------------
    | Configure here relationships to be used by the Search and Table Filters.
    |
    */
    public function setUp(): array
    {
        return [
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage($this->perPage, $this->perPageValues)
                ->showRecordCount(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    |  Add Column
    |--------------------------------------------------------------------------
    | Make Datasource fields available to be used as columns.
    | You can pass a closure to transform/modify the data.
    |
    */
    public function addColumns(): PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('callsign_formatted', function (RclMessage $entry) {
                return $entry->is_concorde ? "{$entry->callsign} CONC" : $entry->callsign;
            })
            ->addColumn('destination')
            ->addColumn('route', function ($entry) {
                return $entry->track ? 'NAT' : 'RR';
            })
            ->addColumn('track_formatted', function ($entry) {
                return $entry->track?->identifier;
            })
            ->addColumn('entry_fix')
            ->addColumn('entry_time', function ($entry) {
                return $entry->new_entry_time ? "{$entry->entry_time}**" : $entry->entry_time;
            })
            ->addColumn('flight_level')
            ->addColumn('max_flight_level')
            ->addColumn('mach')
            ->addColumn('request_time_formatted', function ($entry) {
                return $entry->request_time->format('Hi');
            });
    }

    /*
    |--------------------------------------------------------------------------
    |  Include Columns
    |--------------------------------------------------------------------------
    | Include the columns added columns, making them visible on the Table.
    | Each column can be configured with properties, filters, actions...
    |

    */
    /**
     * PowerGrid Columns.
     *
     * @return array<int, Column>
     */
    public function columns(): array
    {
        return [
            Column::make('CS', 'callsign_formatted')->searchable(),
            Column::make('DEST', 'destination')->searchable(),
            Column::make('ROUTE', 'route'),
            Column::make('TRACK', 'track_formatted'),
            Column::make('ENTRY', 'entry_fix')->searchable(),
            Column::make('ETA', 'entry_time'),
            Column::make('FL', 'flight_level'),
            Column::make('MFL', 'max_flight_level'),
            Column::make('MACH', 'mach'),
            Column::make('REQ TIME', 'request_time_formatted'),
            Column::make('Request Time Full', 'request_time')->sortable()->hidden(),
        ];
    }

    public function actions(): array
    {
        return [
            Button::add('action-rcl')
                ->caption('ACTION')
                ->class('btn btn-sm btn-primary')
                ->route('controllers.clx.show-rcl-message', function (RclMessage $message) {
                    return ['rclMessage' => $message];
                }),
        ];
    }
}
