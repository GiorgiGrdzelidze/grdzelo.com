<?php

namespace App\Filament\Resources\CertificationResource\Pages;

use App\Filament\Concerns\HandlesMediaAltState;
use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\CertificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertification extends EditRecord
{
    // HandlesTranslatableForm is used here purely as the integration point
    // for HandlesMediaAltState (Certification itself isn't translatable —
    // its $translatable iteration loop is a no-op).
    use HandlesMediaAltState, HandlesTranslatableForm;

    protected static string $resource = CertificationResource::class;

    protected function mediaAltCollections(): array
    {
        return ['badge'];
    }

    protected function afterSave(): void
    {
        $this->persistMediaAltState();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
