<?php

namespace App\Http\Livewire\Administration;

use App\Models\CtpBooking;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CtpBookingsTable extends DataTableComponent
{
    protected $model = CtpBooking::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')->sortable(),
            Column::make('User', 'cid')->sortable(),
            Column::make('Dest', 'destination')->sortable(),
            Column::make('Flight Level', 'flight_level')->sortable(),
            Column::make('Track','track')->sortable(),
            Column::make('Route', 'random_routeing'),
            Column::make('SELCAL', 'selcal'),
        ];
    }
}
