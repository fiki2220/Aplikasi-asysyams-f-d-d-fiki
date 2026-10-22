<?php

namespace App\Filament\Resources;

use App\Features\Payments\Enums\PaymentStatus;
use App\Features\Payments\Models\Payment;
use App\Filament\Resources\PaymentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PaymentResource extends Resource
{
    use \App\Filament\Concerns\ChecksResourcePermission;

    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Data Pembayaran';
    protected static ?string $modelLabel = 'Pembayaran';
    protected static ?string $pluralModelLabel = 'Data Pembayaran';
    protected static ?int $navigationSort = 1;

    protected static function permission(): string
    {
        return 'payments.manage';
    }

    // ==========================================================
    // FORM
    // ==========================================================

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembayaran')
                    ->icon('heroicon-o-banknotes')
                    ->description('Detail transaksi pembayaran SPP santri.')
                    ->schema([
                        Forms\Components\Select::make('semester_id')
                            ->label('Semester')
                            ->relationship('semester', 'name')
                            ->required()
                            ->native(false)
                            ->searchable(),

                        Forms\Components\Select::make('user_id')
                            ->relationship('student', 'name')
                            ->label('Nama Siswa')
                            ->searchable()
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Bayar')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(0)
                            ->helperText('Masukkan angka tanpa titik. Contoh: 750000'),

                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->options(PaymentStatus::options())
                            ->default(PaymentStatus::Paid->value)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('order_id')
                            ->label('Order ID (Unik)')
                            ->default(fn () => 'MANUAL-' . Str::uuid()->toString())
                            ->required()
                            ->disabledOn('edit')
                            ->dehydrated()
                            ->unique(ignoreRecord: true)
                            ->helperText('ID unik untuk transaksi. Otomatis di-generate.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    // ==========================================================
    // TABLE
    // ==========================================================

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ⭐ Kolom 1: Nama Siswa (paling penting)
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Payment $record): ?string =>
                        $record->student?->nisn
                            ? 'NISN: ' . $record->student->nisn
                            : null)
                    ->wrap(),

                // ⭐ Kolom 2: Nominal
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->alignRight()
                    ->weight('medium')
                    ->color('success')
                    ->width('140px'),

                // ⭐ Kolom 3: Status
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid', 'success' => 'Lunas',
                        'pending' => 'Menunggu',
                        'failed' => 'Gagal',
                        'expired' => 'Kedaluwarsa',
                        'refunded' => 'Dikembalikan',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid', 'success' => 'success',
                        'pending' => 'warning',
                        'failed', 'expired' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'paid', 'success' => 'heroicon-m-check-circle',
                        'pending' => 'heroicon-m-clock',
                        'failed' => 'heroicon-m-x-circle',
                        'expired' => 'heroicon-m-exclamation-triangle',
                        'refunded' => 'heroicon-m-arrow-uturn-left',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->sortable()
                    ->width('110px'),

                // ⭐ Kolom 4: Tanggal
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->width('130px'),

                // ─── Kolom Sekunder (hidden default) ───

                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Semester')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('payment_type')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'bank_transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'manual', 'cash' => 'Tunai',
                        'ewallet', 'e_wallet' => 'E-Wallet',
                        'credit_card' => 'Kartu Kredit',
                        default => $state ? ucfirst(str_replace('_', ' ', $state)) : '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'bank_transfer' => 'info',
                        'qris' => 'primary',
                        'manual', 'cash' => 'success',
                        'ewallet', 'e_wallet' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('order_id')
                    ->label('ID Transaksi')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('ID disalin!')
                    ->fontFamily('mono')
                    ->size('sm')
                    ->weight('medium')
                    ->color('gray')
                    ->limit(18)
                    ->tooltip(fn (Payment $record): string => $record->order_id)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options(PaymentStatus::filterOptions())
                    ->multiple(),

                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'name')
                    ->searchable()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('payment_type')
                    ->label('Metode Pembayaran')
                    ->options([
                        'bank_transfer' => 'Transfer Bank',
                        'qris' => 'QRIS',
                        'manual' => 'Manual / Tunai',
                        'ewallet' => 'E-Wallet',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['until'], fn (Builder $q, $d) => $q->whereDate('created_at', '<=', $d));
                    })
                    ->label('Rentang Tanggal'),
            ])
            ->actions([
                // ⭐ Kwitansi PDF (hanya kalau lunas)
                Tables\Actions\Action::make('kwitansi_pdf')
                    ->label('Kwitansi PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->iconButton()
                    ->tooltip('Download Kwitansi PDF')
                    ->color('info')
                    ->url(fn (Payment $record): string => route('payment.kwitansi.pdf', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Payment $record): bool =>
                        in_array($record->status, ['paid', 'success'], true)),

                // ⭐ Print Kwitansi (hanya kalau lunas)
                Tables\Actions\Action::make('kwitansi_print')
                    ->label('Print Kwitansi')
                    ->icon('heroicon-o-printer')
                    ->iconButton()
                    ->tooltip('Print Kwitansi')
                    ->color('warning')
                    ->url(fn (Payment $record): string => route('payment.kwitansi.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Payment $record): bool =>
                        in_array($record->status, ['paid', 'success'], true)),

                // Tandai Lunas — hanya kalau belum lunas
                Tables\Actions\Action::make('mark_paid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-badge')
                    ->iconButton()
                    ->tooltip('Tandai Lunas')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Pembayaran Lunas?')
                    ->modalDescription(fn (Payment $record): string =>
                        "Tandai pembayaran {$record->order_id} sebesar " .
                        'Rp ' . number_format((float) $record->amount, 0, ',', '.') .
                        " sebagai LUNAS?"
                    )
                    ->modalSubmitActionLabel('Ya, Tandai Lunas')
                    ->action(function (Payment $record) {
                        $record->update([
                            'status' => PaymentStatus::Paid->value,
                        ]);

                        Notification::make()
                            ->title('✅ Pembayaran Ditandai Lunas')
                            ->body("ID: {$record->order_id}")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Payment $record): bool =>
                        ! in_array($record->status, ['paid', 'success'], true)
                    ),

                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_mark_paid')
                        ->label('Tandai Lunas')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Semua Pembayaran Terpilih Sebagai Lunas?')
                        ->modalDescription('Semua pembayaran yang dipilih akan ditandai LUNAS.')
                        ->modalSubmitActionLabel('Ya, Tandai Semua')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => PaymentStatus::Paid->value,
                                ]);
                                $count++;
                            }

                            Notification::make()
                                ->title("✅ {$count} Pembayaran Ditandai Lunas")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->recordClasses(fn (Payment $record): ?string => match ($record->status) {
                'pending' => 'bg-amber-50 dark:bg-amber-950/20',
                'failed', 'expired' => 'bg-red-50 dark:bg-red-950/20',
                default => null,
            })
            ->emptyStateHeading('Belum Ada Transaksi Pembayaran')
            ->emptyStateDescription('Data pembayaran akan muncul setelah ada transaksi.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit'   => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return PaymentStatus::options();
    }

    public static function getStatusFilterOptions(): array
    {
        return PaymentStatus::filterOptions();
    }
}