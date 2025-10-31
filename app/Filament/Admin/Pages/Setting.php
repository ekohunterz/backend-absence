<?php

namespace App\Filament\Admin\Pages;

use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use App\Models\Setting as SettingModel;
use Illuminate\Support\Facades\DB;
use UnitEnum;
use BackedEnum;


class Setting extends Page implements HasSchemas
{
    use InteractsWithSchemas;
    protected string $view = 'filament.admin.pages.setting';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan';

    protected static string|null|BackedEnum $navigationIcon = Phosphor::Gear;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 10;

    public ?array $data = [];
    public SettingModel $setting;

    public function mount(): void
    {
        // Ambil record pertama atau buat baru jika belum ada
        $this->setting = SettingModel::firstOrCreate([]);
        $this->form->fill($this->setting->toArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Pengaturan')
                    ->tabs([
                        Tab::make('Pengaturan Umum')
                            ->schema([
                                FileUpload::make('school_logo')
                                    ->label('Logo Sekolah')
                                    ->directory('school-logos')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->imagePreviewHeight('250')
                                    ->panelAspectRatio('6:5')
                                    ->panelLayout('integrated')
                                    ->columnSpan(2),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('school_name')
                                            ->label('Nama Sekolah')
                                            ->placeholder('Nama Sekolah')
                                            ->columnSpanFull()
                                            ->required(),
                                        TextInput::make('school_phone')
                                            ->label('Nomor Telepon')
                                            ->placeholder('Nomor Telepon')
                                            ->tel()
                                            ->required(),
                                        TextInput::make('school_email')
                                            ->label('Email')
                                            ->placeholder('Email')
                                            ->email()
                                            ->required(),
                                        Textarea::make('school_address')
                                            ->label('Alamat')
                                            ->placeholder('Alamat Lengkap')
                                            ->columnSpanFull()
                                            ->required(),
                                    ])
                                    ->columnSpan(4),
                            ])
                            ->columns(6),
                        Tab::make('Pengaturan Presensi')
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->placeholder('Contoh: -7.12345')
                                    ->numeric()
                                    ->required(),
                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->placeholder('Contoh: 110.12345')
                                    ->numeric()
                                    ->required(),
                                TextInput::make('radius')
                                    ->label('Radius (meter)')
                                    ->numeric()
                                    ->columnSpanFull()
                                    ->required(),
                                TimePicker::make('start_time')
                                    ->label('Jam Masuk')
                                    ->required(),
                                TimePicker::make('end_time')
                                    ->label('Jam Pulang')
                                    ->required(),
                            ])->columns(2),
                    ])
            ])
            ->statePath('data');
    }



    public function save(): void
    {
        DB::beginTransaction();
        try {
            $this->validate();

            $this->setting->update($this->form->getState());
            DB::commit();

            Notification::make()
                ->title('Pengaturan berhasil disimpan')
                ->success()
                ->send();

        } catch (\Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->title('Gagal menyimpan pengaturan')
                ->body($th->getMessage())
                ->danger()
                ->send();
            throw $th;

        }


    }


}
