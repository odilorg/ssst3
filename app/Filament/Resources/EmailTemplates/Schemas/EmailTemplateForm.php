<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use App\Models\EmailTemplate;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Template Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Initial Outreach')
                            ->helperText('Internal name for this template')
                            ->columnSpan(2),

                        Select::make('type')
                            ->label('Template Type')
                            ->options([
                                'initial_contact' => 'Initial Contact',
                                'follow_up_1' => 'Follow-up #1',
                                'follow_up_2' => 'Follow-up #2',
                                'follow_up_3' => 'Follow-up #3',
                                'proposal' => 'Proposal',
                                'custom' => 'Custom',
                            ])
                            ->required()
                            ->default('custom'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active templates can be used'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->placeholder('Internal notes about when to use this template')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Email Content')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Email Subject')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Subject line with {{variables}}')
                            ->helperText('Use {{variable_name}} for dynamic content'),

                        RichEditor::make('body')
                            ->label('Email Body')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'h2',
                                'h3',
                                'bulletList',
                                'orderedList',
                                'link',
                                'undo',
                                'redo',
                            ])
                            ->helperText('Use {{variable_name}} for dynamic content')
                            ->columnSpanFull(),
                    ]),

                Section::make('Available Variables')
                    ->schema([
                        Placeholder::make('variables_help')
                            ->label('How to use variables')
                            ->content(new HtmlString('
                                <div class="text-sm space-y-2">
                                    <p class="font-medium">Copy and paste these variables into your subject or body:</p>
                                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                                        <li><code class="bg-gray-100 px-2 py-1 rounded">{{company_name}}</code> - Company name</li>
                                        <li><code class="bg-gray-100 px-2 py-1 rounded">{{contact_name}}</code> - Contact person name</li>
                                        <li><code class="bg-gray-100 px-2 py-1 rounded">{{country}}</code> - Company country</li>
                                        <li><code class="bg-gray-100 px-2 py-1 rounded">{{website}}</code> - Company website</li>
                                        <li><code class="bg-gray-100 px-2 py-1 rounded">{{sender_name}}</code> - Your name</li>
                                        <li><code class="bg-gray-100 px-2 py-1 rounded">{{sender_email}}</code> - Your email</li>
                                        <li><code class="bg-gray-100 px-2 py-1 rounded">{{sender_company}}</code> - Your company name</li>
                                    </ul>
                                    <p class="text-gray-500 text-xs mt-2">Variables will be replaced with actual values when email is sent</p>
                                </div>
                            ')),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
