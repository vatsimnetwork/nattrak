<?php

namespace App\Http\Livewire\Controllers;

use App\Enums\DatalinkAuthorities;
use App\Http\Controllers\ClxMessagesController;
use App\Models\DatalinkAuthority;
use App\Models\Track;
use Illuminate\Database\Eloquent\Builder;
use phpDocumentor\Reflection\Types\Boolean;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\RclMessage;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\WireLinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectDropdownFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class LtNewPending extends DataTableComponent
{
    public function builder(): Builder
    {
        return RclMessage::pending();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        // ->setTableRowUrl(function($row) {
        //     return route('controllers.clx.show-rcl-message', $row->id);
        // })
        // ->setTableRowUrlTarget(function($row) {

        //     return '_self';
        // })
        $this->setDefaultSort('request_time', 'asc');
        $this->setTrimSearchStringEnabled();
        $this->setRefreshTime(10000);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id"),
            Column::make("Callsign", "callsign")
                ->searchable(),
            Column::make("Destination", "destination")
                ->searchable(),
            Column::make("Track")
                ->label(function ($row, Column $column) {
                    $msg = RclMessage::whereId($row->id)->first();
                    if ($msg == null) return "N/A";
                    return $msg->track ? $msg->track->identifier : 'RR';
                }),
            Column::make("Flight level", "flight_level"),
            Column::make("Entry fix", "entry_fix")
                ->searchable(),
            Column::make("Entry time", "entry_time")
                ->sortable(),
            Column::make("Request time", "request_time")
                ->sortable(),
            Column::make("Target")
                ->label(function ($row, Column $column) {
                   return Rclmessage::whereId($row->id)->first()->targetDatalinkAuthority->id ?? 'N/A';
                }),
            LinkColumn::make('View') // make() has no effect in this case but needs to be set anyway
            ->title(fn($row) => 'View ' . $row->callsign)
                ->location(function($row) {
                    return route('controllers.clx.show-rcl-message', [$row->id]);
                })
                ->attributes(function($row) {
                    return [
                        'class' => 'btn btn-primary btn-sm',
                    ];
                }),
            WireLinkColumn::make("Delete Item")
                ->title(fn($row) => 'Delete')
                ->confirmMessage('Are you sure you want to delete this item?')
                ->action(fn($row) => 'deleteRow("'.$row->id.'")')
                ->attributes(fn($row) => [
                    'class' => 'btn btn-outline-danger btn-sm',
                ]),
            BooleanColumn::make('Auto Ackw', 'is_acknowledged')->yesNo(),
        ];
    }

    private function scopeWhereRandomRouteing($query)
    {
        return $query->whereNotNull('random_routeing');
    }

    /* private function whereTrack($query, $selected)
    {
        return $query->where
    } */

    public function filters(): array
    {
        $trackOptions = Track::query()
            ->orderBy('identifier')
            ->get()
            ->keyBy('id')
            ->map(fn($track) => $track->identifier)
            ->toArray();
        $trackOptions[100] = 'RR';
        $authorityOptions = DatalinkAuthority::query()
            ->orderBy('id')
            ->get()
            ->keyBy('id')
            ->map(fn($authority) => $authority->name)
            ->toArray();
        return [
            MultiSelectFilter::make('Track')
                ->options($trackOptions)
                ->setFirstOption('All')
                ->filter(function(Builder $builder, array $value) use ($trackOptions) {
                    $selections = [];
                    foreach ($value as $selection) {
                        $selections[] = $trackOptions[$selection];
                    }
                    if (in_array('RR', $selections)) {
                        unset($value[array_search('100', $value)]);
                        if (!empty($value)) {
                            $builder->where('random_routeing', '!=', null)->orWhereIn('track_id', array_values($value));
                        } else {
                            $builder->where('random_routeing', '!=', null);
                        }
                    }
                    else {
                        $builder->whereIn('track_id', array_values($value));
                    }
                }),
            MultiSelectFilter::make('Target OCA')
                ->options($authorityOptions)
                ->setFirstOption('All')
                ->filter(function(Builder $builder, array $value) {
                    $builder->whereIn('target_datalink_authority_id', array_values($value));
                }),
            SelectFilter::make('Acknowledged')
                ->options([
                    '' => 'All',
                    'true' => 'Yes',
                    'false' => 'No'
                ])
                ->filter(function(Builder $builder, string $value) {
                    if ($value === 'true') {
                        $builder->where('is_acknowledged', true);
                    }
                    else if ($value === 'false') {
                        $builder->where('is_acknowledged', false);
                    }
                })
        ];
    }

    public array $bulkActions = [
        'deleteSelected' => 'Delete'
    ];

    public function deleteRow($id, $redirect = true)
    {
        $rclMessage = RclMessage::whereId($id)->firstOrFail();
        $redirectToProcessed = $rclMessage->clxMessages->count() > 0;
        $rclMessage->delete();
        flashAlert(type: 'success', title: null, message: 'Request deleted.', toast: true, timer: true);
        if ($redirect) {
            if ($redirectToProcessed) {
                return redirect()->route('controllers.clx.processed');
            } else {
                return redirect()->route('controllers.clx.pending');
            }
        }
    }

    public function deleteSelected()
    {
        foreach ($this->getSelected() as $item) {
            $this->deleteRow($item, false);
        }
    }
}
