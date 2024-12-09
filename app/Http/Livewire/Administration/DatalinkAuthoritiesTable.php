<?php

namespace App\Http\Livewire\Administration;

use App\Models\DatalinkAuthority;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

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
                ->sortable()->toggleable('changeAutoAcknowledge'),
            BooleanColumn::make("Valid rcl target", "valid_rcl_target")
                ->sortable()->toggleable('changeValidRclTarget'),
            BooleanColumn::make("System", "system")
                ->sortable()->toggleable('changeSystem'),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
        ];
    }

    public function changeAutoAcknowledge(string $id)
    {
        $authority = $this->model::find($id);
        $authority->auto_acknowledge_participant = !$authority->auto_acknowledge_participant;
        $authority->save();
    }

    public function changeValidRclTarget(string $id)
    {
        $authority = $this->model::find($id);
        $authority->valid_rcl_target = !$authority->valid_rcl_target;
        $authority->save();
    }

    public function changeSystem(string $id)
    {
        $authority = $this->model::find($id);
        $authority->system = !$authority->system;
        $authority->save();
    }
}
