<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Features\Posts\Models\Post;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tulis Berita')
                ->icon('heroicon-o-pencil-square'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->icon('heroicon-o-newspaper')
                ->badge(Post::count()),

            'published' => Tab::make('✅ Terbit')
                ->badge(Post::where('is_published', true)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', true)),

            'draft' => Tab::make('📝 Draft')
                ->badge(Post::where('is_published', false)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_published', false)),

            'this_month' => Tab::make('📅 Bulan Ini')
                ->badge(Post::whereMonth('published_at', now()->month)
                    ->whereYear('published_at', now()->year)
                    ->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereMonth('published_at', now()->month)
                    ->whereYear('published_at', now()->year)),

            'popular' => Tab::make('🔥 Populer')
                ->badge(Post::where('views', '>', 0)->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('views', '>', 0)
                    ->orderByDesc('views')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}