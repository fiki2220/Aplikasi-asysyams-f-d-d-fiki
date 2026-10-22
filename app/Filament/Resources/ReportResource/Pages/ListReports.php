<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    public ?int $periodMonth = null;
    public ?int $periodYear = null;

    // ==========================================================
    // MOUNT — Ambil periode dari URL
    // ==========================================================

    public function mount(): void
    {
        parent::mount();

        $this->periodMonth = (int) request()->query('month', now()->month);
        $this->periodYear  = (int) request()->query('year', now()->year);
    }

    // ==========================================================
    // HEADER ACTIONS — Filter Periode
    // ==========================================================

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('filter_period')
                ->label(fn (): string => '📅 ' . $this->getPeriodLabel())
                ->color('gray')
                ->icon('heroicon-o-calendar')
                ->form([
                    Forms\Components\Select::make('month')
                        ->label('Bulan')
                        ->options(self::monthOptions())
                        ->default(fn (): int => $this->periodMonth)
                        ->native(false)
                        ->required(),

                    Forms\Components\Select::make('year')
                        ->label('Tahun')
                        ->options(self::yearOptions())
                        ->default(fn (): int => $this->periodYear)
                        ->native(false)
                        ->required(),
                ])
                ->modalHeading('Pilih Periode Rapor')
                ->modalDescription('Pilih bulan dan tahun periode rapor. Filter ini berlaku untuk tombol Download PDF.')
                ->modalSubmitActionLabel('Terapkan')
                ->action(function (array $data): void {
                    $this->periodMonth = (int) $data['month'];
                    $this->periodYear  = (int) $data['year'];

                    // ⭐ Redirect ke URL yang proper, bukan fullUrlWithQuery (Livewire konflik)
                    $this->redirect(ReportResource::getUrl('index', [
                        'month' => $this->periodMonth,
                        'year'  => $this->periodYear,
                    ]));
                }),
        ];
    }

    // ==========================================================
    // TABS
    // ==========================================================

    public function getTabs(): array
    {
        $base = fn (): Builder => ReportResource::getEloquentQuery();

        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-squares-2x2')
                ->badge($base()->count()),

            'with_students' => Tab::make('Ada Santri')
                ->icon('heroicon-o-user-group')
                ->badge($base()->has('students')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->has('students')),

            'empty' => Tab::make('Kelas Kosong')
                ->icon('heroicon-o-exclamation-triangle')
                ->badge($base()->doesntHave('students')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->doesntHave('students')),

            'this_semester' => Tab::make('Semester Aktif')
                ->icon('heroicon-o-calendar')
                ->badge($base()
                    ->whereHas('semester', fn ($q) => $q->where('is_active', true))
                    ->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereHas('semester', fn ($q) => $q->where('is_active', true))),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }

    // ==========================================================
    // HELPERS
    // ==========================================================

    public static function monthOptions(): array
    {
        return [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    public static function yearOptions(): array
    {
        $currentYear = (int) now()->year;
        $years = [];

        for ($y = $currentYear + 1; $y >= $currentYear - 3; $y--) {
            $years[$y] = (string) $y;
        }

        return $years;
    }

    protected function getPeriodLabel(): string
    {
        $bulan = self::monthOptions()[$this->periodMonth] ?? '—';

        return "Periode: {$bulan} {$this->periodYear}";
    }

    // ==========================================================
    // TITLE & SUBHEADING
    // ==========================================================

    public function getTitle(): string
    {
        return 'Rapor Santri';
    }

    public function getSubheading(): ?string
    {
        return null;
    }
}