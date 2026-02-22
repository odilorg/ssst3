<?php

namespace App\Forms\Components;

use App\Services\ImageRepositoryService;
use Filament\Forms\Components\Field;

class ImageRepoPicker extends Field
{
    protected string $view = 'forms.components.image-repo-picker';

    protected bool $isMultiple = false;

    protected string $defaultSize = 'large.webp';

    protected ?string $targetField = null;

    public function multiple(bool $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function defaultSize(string $size): static
    {
        $this->defaultSize = $size;

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->isMultiple;
    }

    public function getDefaultSize(): string
    {
        return $this->defaultSize;
    }

    public function getPickerToken(): ?string
    {
        return app(ImageRepositoryService::class)->getPickerToken();
    }

    public function getImageRepoUrl(): string
    {
        return app(ImageRepositoryService::class)->getPickerUrl();
    }

    public function getRepoOrigin(): string
    {
        return app(ImageRepositoryService::class)->getBaseUrl();
    }

    public function targetField(string $field): static
    {
        $this->targetField = $field;

        return $this;
    }

    public function getTargetField(): ?string
    {
        return $this->targetField;
    }
}
