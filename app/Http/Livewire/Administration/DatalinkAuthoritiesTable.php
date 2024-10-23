<?php

namespace App\Http\Livewire\Administration;

use App\Enums\AccessLevelEnum;
use App\Models\DatalinkAuthority;
use App\Models\VatsimAccount;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\WireLinkColumn;

class DatalinkAuthoritiesTable extends DataTableComponent
{
    protected $model = DatalinkAuthority::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Prefix", "prefix")
                ->sortable(),
            BooleanColumn::make("Auto acknowledge participant", "auto_acknowledge_participant")
                ->sortable(),
            BooleanColumn::make("Valid RCL target", "valid_rcl_target")
                ->sortable(),
            BooleanColumn::make("System", "system")
                ->sortable(),
            WireLinkColumn::make("Toggle auto acknowledge")
                ->title(fn($row) => 'Toggle Auto Acknowledge')
                ->confirmMessage('Are you sure you want to toggle auto acknowledge?')
                ->action(fn($row) => 'toggleAutoAcknowledge("'.$row->id.'")')
                ->attributes(fn($row) => [
                    'class' => 'btn btn-outline-secondary btn-sm',
                ]),
            WireLinkColumn::make("Toggle RCL target")
                ->title(fn($row) => 'Toggle RCL Target')
                ->confirmMessage('Are you sure you want to toggle RCL target?')
                ->action(fn($row) => 'toggleRclTarget("'.$row->id.'")')
                ->attributes(fn($row) => [
                    'class' => 'btn btn-outline-secondary btn-sm',
                ]),
            WireLinkColumn::make("Remove")
                ->title(fn($row) => 'Remove')
                ->confirmMessage('Are you sure you want to remove this authority?')
                ->action(fn($row) => 'deleteRow("'.$row->id.'")')
                ->attributes(fn($row) => [
                    'class' => 'btn btn-outline-danger btn-sm',
                ]),
        ];
    }

    public function deleteRow($id)
    {
        $datalinkAuthority = DatalinkAuthority::whereId($id)->firstOrFail();
        $datalinkAuthority->delete();

        flashAlert(type: 'info', title: "Deleted", message: null, toast: false, timer: false);

        return redirect()->route('administration.datalink-authorities');
    }

    public function toggleAutoAcknowledge($id)
    {
        $datalinkAuthority = DatalinkAuthority::whereId($id)->firstOrFail();
        $datalinkAuthority->auto_acknowledge_participant = !$datalinkAuthority->auto_acknowledge_participant;
        $datalinkAuthority->save();

        flashAlert(type: 'info', title: "Toggled", message: null, toast: false, timer: false);

        return redirect()->route('administration.datalink-authorities');
    }

    public function toggleRCLTarget($id)
    {
        $datalinkAuthority = DatalinkAuthority::whereId($id)->firstOrFail();
        $datalinkAuthority->valid_rcl_target = !$datalinkAuthority->valid_rcl_target;
        $datalinkAuthority->save();

        flashAlert(type: 'info', title: "Toggled", message: null, toast: false, timer: false);

        return redirect()->route('administration.datalink-authorities');
    }
}
