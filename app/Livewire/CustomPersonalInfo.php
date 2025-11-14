<?php

namespace App\Livewire;


use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;

class CustomPersonalInfo extends PersonalInfo
{
    public array $only = ['name', 'email', 'phone', 'gender', 'birth_date', 'parent_name', 'parent_phone', 'address'];

    // You can override the default components by returning an array of components.
    protected function getProfileFormComponents(): array
    {
        return [
            $this->getNameComponent(),
            $this->getEmailComponent(),
            $this->getPhoneComponent(),
            $this->getGenderComponent(),
            $this->getBirthDateComponent(),
            $this->getParentNameComponent(),
            $this->getParentPhoneComponent(),
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

    protected function getPhoneComponent(): TextInput
    {
        return TextInput::make('phone')
            ->tel()
            ->label('Nomor Telepon')
            ->required();
    }

    protected function getParentNameComponent(): TextInput
    {
        return TextInput::make('parent_name')
            ->label('Nama Orang Tua')
            ->required();
    }

    protected function getParentPhoneComponent(): TextInput
    {
        return TextInput::make('parent_phone')
            ->label('Nomor Telepon Orang Tua')
            ->tel()
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