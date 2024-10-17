<?php

namespace App\Http\Livewire\Administration;

use App\Enums\AccessLevelEnum;
use App\Models\VatsimAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\WireLinkColumn;

class ControllersTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return VatsimAccount::query()
            ->where('access_level', AccessLevelEnum::Controller);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setAdditionalSelects(['vatsim_accounts.given_name as given_name, vatsim_accounts.surname as surname']);
    }

    public function columns(): array
    {
        return [
            Column::make("CID", "id")
                ->sortable(),
            Column::make('Full Name')
                ->label(fn ($row, Column $column) => ucwords($row->given_name ?? '' . ' ' . $row->surname)),
            WireLinkColumn::make("Remove")
                ->title(fn($row) => 'Remove')
                ->confirmMessage('Are you sure you want to remove this account?')
                ->action(fn($row) => 'deleteRow("'.$row->id.'")')
                ->attributes(fn($row) => [
                    'class' => 'btn btn-outline-danger btn-sm',
                ]),
        ];
    }

    public function deleteRow($id)
    {
        $vatsimAccount = VatsimAccount::whereId($id)->first();

        if (Auth::id() == $vatsimAccount->id) {
            flashAlert(type: 'error', title: 'You can\'t remove yourself!', message: null, toast: false, timer: false);

            return redirect()->route('administration.controllers');
        }

        $vatsimAccount->access_level = AccessLevelEnum::Pilot;
        $vatsimAccount->save();

        flashAlert(type: 'info', title: "$vatsimAccount->id's access has been removed.", message: null, toast: false, timer: false);

        return redirect()->route('administration.controllers');
    }
}
