<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\RequiresDashboardPermission;
use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPaymentsTable extends BaseWidget
{
    use RequiresDashboardPermission;

    protected static ?string $heading = 'Recent Payments';

    protected static ?int $sort = 5;

    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()->with(['user', 'plan'])->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('ZAR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
