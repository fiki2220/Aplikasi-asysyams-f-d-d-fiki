<?php

namespace App\Filament\Resources\ClassGroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MeetingsRelationManager extends RelationManager
{
    // ⭐ Nama relasi harus sama dengan nama fungsi di Model ClassGroup
    protected static string $relationship = 'meetings'; 
    
    protected static ?string $title = 'Jadwal & Absensi Kelas';
    protected static ?string $modelLabel = 'Pertemuan';

    public function form(Form $form): Form
    {
        // Kita arahkan ke form yang sama dengan MeetingResource
        return \App\Filament\Resources\MeetingResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn ($record): string => $record->date?->diffForHumans() ?? '')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('—')
                    ->wrap(),

                Tables\Columns\TextColumn::make('attendances_count')
                    ->counts('attendances')
                    ->label('Hadir')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-users'),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Pengajar')
                    ->icon('heroicon-m-user-circle')
                    ->placeholder('—'),
            ])
            ->filters([
                // Filter kelas sudah tidak perlu karena otomatis 1 kelas
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Pertemuan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
}