<?php

namespace App\Http\Livewire\Controllers;

use App\Models\ClxMessage;
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

final class PgClxMessages extends PowerGridComponent
{
    public $tracks;

    use ActionButton;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

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
        $messages = collect();
        foreach ($this->tracks as $id) {
            $messagesOnTrack = ClxMessage::where('overwritten', false)
                ->when(in_array($id, ['RR', 'CONC']) == false, function ($query) use ($id) {
                    $query->where('track_id', Track::whereIdentifier($id)->firstOrFail()->id);
                }, function ($query) use ($id) {
                    if ($id == 'RR') {
                        $query->where('track_id', null);
                    } elseif ($id == 'CONC') {
                        $query->where('is_concorde', true);
                    }
                })
                ->with(['rclMessage', 'track'])
                ->orderByDesc('created_at')
                ->get();

            foreach ($messagesOnTrack as $message) {
                $messages->add($message);
            }
        }

        return $messages;
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
            ->addColumn('callsign_formatted', function (ClxMessage $entry) {
                return $entry->is_concorde ? "{$entry->rclMessage->callsign} CONC" : $entry->rclMessage->callsign;
            })
            ->addColumn('destination', function (ClxMessage $entry) {
                return  $entry->rclMessage->destination;
            })
            ->addColumn('route_formatted', function (ClxMessage $entry) {
                if ($entry->routeing_changed) {
                    return $entry->track ? 'NAT' : 'RR'.'*';
                } else {
                    return $entry->track ? 'NAT' : 'RR';
                }
            })
            ->addColumn('track_formatted', function (ClxMessage $entry) {
                return $entry->track?->identifier;
            })
            ->addColumn('entry_fix')
            ->addColumn('entry_time_restriction_formatted', function (ClxMessage $entry) {
                if ($entry->raw_entry_time_restriction != $entry->rclMessage->entry_time) {
                    return $entry->entry_time_restriction.'*';
                } else {
                    return $entry->entry_time_restriction;
                }
            })
            ->addColumn('flight_level_formatted', function (ClxMessage $entry) {
                if ($entry->flight_level != $entry->rclMessage->flight_level) {
                    return $entry->flight_level.'*';
                } else {
                    return $entry->flight_level;
                }
            })
            ->addColumn('mach', function (ClxMessage $entry) {
                if ($entry->mach != $entry->rclMessage->mach) {
                    return $entry->mach.'*';
                } else {
                    return $entry->mach;
                }
            })
            ->addColumn('created_at_formatted', function (ClxMessage $entry) {
                return $entry->created_at->format('Hi');
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
            Column::make('ROUTE', 'route_formatted'),
            Column::make('TRACK', 'track_formatted'),
            Column::make('ENTRY', 'entry_fix')->searchable(),
            Column::make('AT', 'entry_time_restriction_formatted'),
            Column::make('FL', 'flight_level_formatted'),
            Column::make('MACH', 'mach'),
            Column::make('CLEARED', 'created_at_formatted'),
            Column::make('Cleared Time Full', 'created_at')->sortable()->hidden(),
        ];
    }

    public function actions(): array
    {
        return [
            Button::add('action-rcl')
                ->caption('ACTION')
                ->class('btn btn-sm btn-primary')
                ->route('controllers.clx.show-rcl-message', function (ClxMessage $message) {
                    return ['rclMessage' => $message->rclMessage];
                }),
        ];
    }
}
