<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BookedTableResource\Pages;
use App\Models\BookedTable;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Table as TableModel;

class BookedTableResource extends Resource
{
    protected static ?string $model = BookedTable::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('table_id')
                    ->label('Table Number')
                    ->options(TableModel::all()->pluck('number', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('status')->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'during' => 'During',
                    'rejected' => 'Rejected',
                ])
                ->default('pending')
                ->required(),


                Select::make('user_id')
                    ->label('User')
                    ->options(User::all()->mapWithKeys(function ($user) {
                        return [$user->id => $user->name . ' ' . $user->last_name];
                    }))
                    ->required()
                    ->searchable()
                    ->rule('different:guest_id'),

                Select::make('guest_id')
                    ->label('Guest')
                    ->options(User::all()->mapWithKeys(function ($user) {
                        return [$user->id => $user->name . ' ' . $user->last_name];
                    }))
                    ->required()
                    ->searchable()
                    ->rule('different:user_id'),

                Toggle::make('user_accepted')
                    ->label('User Accepted')
                    ->inline(false),

                Toggle::make('guest_accepted')
                    ->label('Guest Accepted')
                    ->inline(false),

                DateTimePicker::make('time_from')->seconds(false)->required(),
                DateTimePicker::make('time_to')->seconds(false)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('table.number')->label('Table Number'),

                TextColumn::make('user_id')
                    ->label('User')
                    ->formatStateUsing(function ($record) {
                        return $record->user->name . ' ' . $record->user->last_name;
                    }),

                    TextColumn::make('guest_id')
                    ->label('Guest')
                    ->formatStateUsing(function ($record) {
                        return $record->guest->name . ' ' . $record->guest->last_name;
                    }),

                TextColumn::make('status'),

                TextColumn::make('time_from'),
                TextColumn::make('time_to'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'during' => 'During',
                        'rejected' => 'Rejected',
                    ])

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookedTables::route('/'),
            'create' => Pages\CreateBookedTable::route('/create'),
            'edit' => Pages\EditBookedTable::route('/{record}/edit'),
        ];
    }
}
