<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Enums\CouponStatus;
use App\Filament\Resources\CouponResource;
use App\Models\Coupon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCoupon extends EditRecord
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('activate')
                ->label('Activate')
                ->color('success')
                ->visible(fn (): bool => $this->record->status !== CouponStatus::Active)
                ->action(fn () => $this->record->update(['status' => CouponStatus::Active])),
            Actions\Action::make('deactivate')
                ->label('Deactivate')
                ->color('gray')
                ->visible(fn (): bool => $this->record->status === CouponStatus::Active)
                ->action(fn () => $this->record->update(['status' => CouponStatus::Inactive])),
            Actions\DeleteAction::make()
                ->visible(fn (): bool => $this->record instanceof Coupon && $this->record->canBeDeleted()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['used_count']);

        return $data;
    }
}
