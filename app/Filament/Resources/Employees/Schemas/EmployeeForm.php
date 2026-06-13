<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\Gender;
use App\Enums\Religion;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(6)
            ->components([
                Section::make(__('employee.sections.personal_information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('employee.fields.name'))
                            ->columnSpan(3)
                            ->required(),
                        TextInput::make('nik')
                            ->numeric()
                            ->columnSpan(3)
                            ->label(__('employee.fields.nik'))
                            ->required(),
                        Radio::make('gender')
                            ->options(Gender::class)
                            ->columnSpan(2)
                            ->required(),
                        TextInput::make('email')
                            ->label(__('employee.fields.email'))
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->columnSpan(2)
                            ->required(),
                        TextInput::make('phone')
                            ->numeric()
                            ->columnSpan(2)
                            ->label(__('employee.fields.phone'))
                            ->prefixIcon('heroicon-o-phone')
                            ->required(),
                        TextInput::make('address')
                            ->label(__('employee.fields.address'))
                            ->prefixIcon('heroicon-o-home')
                            ->columnSpan(3)
                            ->required(),
                        TextInput::make('residential_address')
                            ->label(__('employee.fields.residential_address'))
                            ->prefixIcon('heroicon-o-map-pin')
                            ->columnSpan(3)
                            ->required(),
                        TextInput::make('place_of_birth')
                            ->label(__('employee.fields.place_of_birth'))
                            ->columnSpan(2)
                            ->required(),
                        DatePicker::make('birth_date')
                            ->label(__('employee.fields.birth_date'))
                            ->columnSpan(2)
                            ->required(),
                        Select::make('religion')
                            ->columnSpan(2)
                            ->label(__('employee.fields.religion'))
                            ->prefixIcon('heroicon-o-moon')
                            ->native(false)
                            ->options(Religion::class)
                            ->required(),
                        TextInput::make('mothers_name')
                            ->columnSpan(2)
                            ->label(__('employee.fields.mothers_name')),
                        Select::make('blood_group')
                            ->columnSpan(2)
                            ->options(Employee::BLOOD_GROUP)
                            ->native(false)
                            ->prefixIcon('heroicon-o-beaker')
                            ->label(__('employee.fields.blood_group')),
                        // Select::make('last_education')
                        //     ->options(Education::class)
                        //     ->prefixIcon('heroicon-o-academic-cap')
                        //     ->label(__('employee.fields.last_education')),
                        TextInput::make('last_education')
                            ->columnSpan(2)
                            ->prefixIcon('heroicon-o-academic-cap')
                            ->label(__('employee.fields.last_education')),
                    ])->columnSpan(4)->columns(6),
                Section::make(__('employee.sections.company_information'))
                    ->columns(2)
                    ->schema([
                        Select::make('department_id')
                            ->native(false)
                            ->columnSpanFull()
                            ->label(__('employee.fields.department'))
                            ->relationship('department', 'name')
                            ->getOptionLabelFromRecordUsing(fn(\App\Models\Department $record) => "{$record->name}-{$record->code}")
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set, callable $get) => static::updateNip($set, $get))
                            ->required(),
                        Select::make('employee_pos_id')
                            ->native(false)
                            ->columnSpanFull()
                            ->label(__('employee.fields.position'))
                            ->relationship('employeePos', 'name')
                            ->getOptionLabelFromRecordUsing(fn(\App\Models\EmployeePos $record) => "{$record->name}-{$record->code}")
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set, callable $get) => static::updateNip($set, $get))
                            ->required(),
                        TextInput::make('job')
                            ->label(__('employee.fields.job'))
                            ->required(),
                        TextInput::make('employee_number')
                            ->label(__('employee.fields.employee_number'))
                            ->readOnlyOn('create')
                            ->required()
                            ->reactive(),
                        DatePicker::make('join_date')
                            ->label(__('employee.fields.join_date'))
                            ->native(false)
                            ->required(),
                        DatePicker::make('exit_date')
                            ->hiddenOn('create')
                            ->label(__('employee.fields.exit_date')),
                        TextInput::make('exit_reason')
                            ->hiddenOn('create')
                            ->label(__('employee.fields.exit_reason')),
                        Radio::make('is_active')
                            ->inline()
                            ->boolean()
                            ->required()
                            ->default(1)
                            ->label(__('employee.fields.is_active')),
                        FileUpload::make('photo')
                            ->columnSpanFull()
                            ->disk('public')
                            ->directory('employee')
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions([
                                '3:4',
                                '1:1',
                            ])
                            ->preserveFilenames(false)
                            ->label(__('employee.fields.photo')),
                    ])->columnSpan(2),
                Section::make(__('employee.sections.other_information'))
                    ->schema([
                        Radio::make('bank_name')
                            ->columnSpan(2)
                            ->options(Employee::BANK_NAME)
                            ->label(__('employee.fields.bank_name')),
                        TextInput::make('bank_account')
                            ->columnSpan(2)
                            ->label(__('employee.fields.bank_account')),
                        TextInput::make('npwp')
                            ->columnSpan(2)
                            ->label(__('employee.fields.npwp')),
                        TextInput::make('bpjs_kesehatan')
                            ->columnSpan(2)
                            ->label(__('employee.fields.bpjs_kesehatan')),
                        TextInput::make('bpjs_ketenagakerjaan')
                            ->columnSpan(2)
                            ->label(__('employee.fields.bpjs_ketenagakerjaan')),
                        Select::make('ptkp_status')
                            ->columnSpan(2)
                            ->native(false)
                            ->options(Employee::PTKP_STATUS)
                            ->label(__('employee.fields.ptkp_status')),
                    ])->columnSpanFull()->columns(6),
            ]);
    }

    protected static function updateNip(callable $set, callable $get): void
    {
        $departmentId = $get('department_id');
        $EmployeePosId = $get('employee_pos_id');

        if (!$departmentId || !$EmployeePosId) {
            return;
        }

        // Cari karyawan terakhir dengan kombinasi tersebut
        $lastNip = Employee::where('department_id', $departmentId)
            //ambil data termasuk yang soft deleted
            ->withTrashed()
            ->where('employee_pos_id', $EmployeePosId)
            ->orderByDesc('employee_number')
            ->value('employee_number');
        $dept = \App\Models\Department::where('id', $departmentId)->value('code');
        $employeePos = \App\Models\EmployeePos::where('id', $EmployeePosId)->value('code');


        // Ambil nomor terakhir, jika ada
        $number = \Illuminate\Support\Str::afterLast($lastNip, '-');
        $nextNumber = $number
            ? str_pad((int) $number + 1, 5, '0', STR_PAD_LEFT)
            : '00001';

        // $set('employee_number', $nextNumber);
        // Buat ID Card
        $employeeNumber = "SNE-{$dept}-{$employeePos}-{$nextNumber}";
        $set('employee_number', $employeeNumber);
    }
}
