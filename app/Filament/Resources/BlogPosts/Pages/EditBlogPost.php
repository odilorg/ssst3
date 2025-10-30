<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_frontend')
                ->label('View Frontend')
                ->icon(Heroicon::OutlineEye)
                ->color('info')
                ->url(fn () => '/blog-article.html?slug=' . $this->record->slug)
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
