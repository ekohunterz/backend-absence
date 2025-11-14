<?php

namespace App\Livewire;


use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;

class CustomUserInfo extends PersonalInfo
{
    public array $only = ['name', 'email', 'phone', 'gender', 'birth_date', 'nip', 'address'];

    // You can override the default components by returning an array of components.
    protected function getProfileFormComponents(): array
    {
        return [
            $this->getNameComponent(),
            $this->getEmailComponent(),
            $this->getPhoneComponent(),
            $this->getNipComponent(),
            $this->getGenderComponent(),
            $this->getBirthDateComponent(),
            $this->getAddressComponent(),

        ];
    }

    protected function getNameComponent(): TextInput
    {
        return TextInput::make('name')
            ->label('Nama')
            ->required();
    }

    protected function getEmailComponent(): TextInput
    {
        return TextInput::make('email')
            ->email()
            ->unique()
            ->required();
    }

    protected function getNipComponent(): TextInput
    {
        return TextInput::make('nip')
            ->label('NIP')
            ->unique()
            ->placeholder('NIP/NUPTK')
            ->required();
    }

    protected function getPhoneComponent(): TextInput
    {
        return TextInput::make('phone')
            ->tel()
            ->label('Nomor Telepon')
            ->required();
    }


    protected function getGenderComponent(): Select
    {
        return Select::make('gender')
            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
            ->label('Jenis Kelamin')
            ->required();
    }

    protected function getBirthDateComponent(): DatePicker
    {
        return DatePicker::make('birth_date')
            ->label('Tanggal Lahir')
            ->date()
            ->required();
    }

    protected function getAddressComponent(): TextInput
    {
        return TextInput::make('address')
            ->label('Alamat');
    }

    protected function sendNotification(): void
    {
        Notification::make()
            ->success()
            ->title('Saved Data!')
            ->send();
    }
}