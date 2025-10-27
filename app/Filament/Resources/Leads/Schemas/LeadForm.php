<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Information')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(255)
                            ->placeholder('example.com'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Brief description of the company'),
                    ])
                    ->columns(2),

                Section::make('Contact Person')
                    ->schema([
                        TextInput::make('contact_name')
                            ->label('Contact Name')
                            ->maxLength(255),

                        TextInput::make('contact_position')
                            ->label('Position')
                            ->maxLength(255)
                            ->placeholder('e.g., CEO, Sales Manager'),

                        TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('contact_phone')
                            ->label('Contact Phone')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Location & Source')
                    ->schema([
                        TextInput::make('country')
                            ->label('Country')
                            ->maxLength(255)
                            ->placeholder('e.g., USA, Germany, France'),

                        TextInput::make('city')
                            ->label('City')
                            ->maxLength(255),

                        Select::make('source')
                            ->label('Lead Source')
                            ->options([
                                'manual' => 'Manual Entry',
                                'csv_import' => 'CSV Import',
                                'web_scraper' => 'Web Scraper',
                                'referral' => 'Referral',
                                'directory' => 'Industry Directory',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->default('manual'),

                        TextInput::make('source_url')
                            ->label('Source URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('URL where found'),

                        Textarea::make('source_notes')
                            ->label('Source Notes')
                            ->rows(2)
                            ->columnSpanFull()
                            ->placeholder('How/where did you find this lead?'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Tourism Details')
                    ->schema([
                        Select::make('business_type')
                            ->label('Business Type')
                            ->options([
                                'tour_operator' => 'Tour Operator',
                                'dmc' => 'DMC (Destination Management Company)',
                                'travel_agency' => 'Travel Agency',
                                'ota' => 'OTA (Online Travel Agency)',
                                'consolidator' => 'Consolidator',
                                'other' => 'Other',
                            ])
                            ->placeholder('Select business type'),

                        TagsInput::make('tour_types')
                            ->label('Tour Types')
                            ->suggestions([
                                'adventure',
                                'cultural',
                                'luxury',
                                'budget',
                                'eco-tourism',
                                'wellness',
                                'religious',
                                'educational',
                                'honeymoon',
                                'family',
                                'group',
                                'mice',
                            ])
                            ->placeholder('Add tour types')
                            ->helperText('Types of tours they specialize in'),

                        TagsInput::make('target_markets')
                            ->label('Target Markets')
                            ->placeholder('e.g., USA, Germany, China')
                            ->helperText('Countries/markets they serve'),

                        TextInput::make('annual_volume')
                            ->label('Annual Volume')
                            ->numeric()
                            ->suffix('pax/year')
                            ->placeholder('0')
                            ->helperText('Estimated annual passenger volume'),

                        TagsInput::make('certifications')
                            ->label('Certifications')
                            ->suggestions(['IATA', 'ASTA', 'ABTA', 'CLIA'])
                            ->placeholder('Add certifications'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Uzbekistan Partnership & Working Status')
                    ->schema([
                        Toggle::make('has_uzbekistan_partner')
                            ->label('Has Uzbekistan Partner')
                            ->live()
                            ->helperText('Is this company already working with a partner in Uzbekistan?'),

                        TextInput::make('uzbekistan_partner_name')
                            ->label('Uzbekistan Partner Name')
                            ->maxLength(255)
                            ->visible(fn ($get) => $get('has_uzbekistan_partner'))
                            ->placeholder('Partner company name'),

                        Select::make('uzbekistan_partnership_status')
                            ->label('Partnership Status')
                            ->options([
                                'active' => 'Active - Currently Working',
                                'inactive' => 'Inactive - Not Working',
                                'expired' => 'Expired Contract',
                                'seasonal' => 'Seasonal Partnership',
                                'pending' => 'Pending New Partnership',
                            ])
                            ->visible(fn ($get) => $get('has_uzbekistan_partner'))
                            ->placeholder('Select status'),

                        Textarea::make('uzbekistan_partnership_notes')
                            ->label('Partnership Notes')
                            ->rows(2)
                            ->visible(fn ($get) => $get('has_uzbekistan_partner'))
                            ->placeholder('Details about the partnership')
                            ->columnSpanFull(),

                        Select::make('working_status')
                            ->label('Company Working Status')
                            ->options([
                                'active' => 'Active - Currently Operational',
                                'inactive' => 'Inactive - Not Operating',
                                'seasonal' => 'Seasonal Operation',
                                'temporary_pause' => 'Temporarily Paused',
                                'unknown' => 'Unknown',
                            ])
                            ->required()
                            ->default('active')
                            ->helperText('Current operational status of the company'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Status & Assignment')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'new' => 'New',
                                'researching' => 'Researching',
                                'qualified' => 'Qualified',
                                'contacted' => 'Contacted',
                                'responded' => 'Responded',
                                'negotiating' => 'Negotiating',
                                'partner' => 'Partner',
                                'not_interested' => 'Not Interested',
                                'invalid' => 'Invalid Data',
                                'on_hold' => 'On Hold',
                            ])
                            ->required()
                            ->default('new'),

                        Select::make('assigned_to')
                            ->label('Assigned To')
                            ->relationship('assignedUser', 'name')
                            ->searchable()
                            ->preload()
                            ->default(auth()->id()),

                        Select::make('quality_score')
                            ->label('Lead Quality')
                            ->options([
                                1 => '⭐ Low Priority',
                                2 => '⭐⭐ Medium',
                                3 => '⭐⭐⭐ Good',
                                4 => '⭐⭐⭐⭐ High Potential',
                                5 => '⭐⭐⭐⭐⭐ VIP/Strategic',
                            ])
                            ->placeholder('Rate this lead'),

                        DateTimePicker::make('next_followup_at')
                            ->label('Next Follow-up Date')
                            ->native(false)
                            ->placeholder('Set follow-up date'),
                    ])
                    ->columns(2),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Additional notes about this lead'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('AI Email Outreach')
                    ->description('AI-powered email generation and personalization')
                    ->schema([
                        Select::make('selected_email_template_id')
                            ->label('Email Template')
                            ->relationship('selectedEmailTemplate', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select template (optional)')
                            ->helperText('Choose a template or let AI generate from scratch'),

                        TextInput::make('email_draft_subject')
                            ->label('Email Subject')
                            ->maxLength(255)
                            ->placeholder('Will be generated by AI or enter manually')
                            ->columnSpanFull(),

                        RichEditor::make('email_draft_body')
                            ->label('Email Body')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'link',
                                'bulletList',
                                'orderedList',
                            ])
                            ->placeholder('Will be generated by AI or enter manually')
                            ->columnSpanFull(),

                        Textarea::make('email_draft_notes')
                            ->label('Strategy Notes')
                            ->rows(2)
                            ->placeholder('Why this approach? Key points to emphasize?')
                            ->helperText('Internal notes about email strategy')
                            ->columnSpanFull(),

                        Select::make('email_priority')
                            ->label('Email Priority')
                            ->options([
                                'high' => '🔴 High Priority',
                                'medium' => '🟡 Medium Priority',
                                'low' => '🟢 Low Priority',
                            ])
                            ->default('medium'),

                        TextInput::make('best_contact_time')
                            ->label('Best Contact Time')
                            ->maxLength(255)
                            ->placeholder('e.g., Morning EST, Avoid Mondays')
                            ->helperText('When to send this email'),

                        Select::make('email_response_status')
                            ->label('Response Status')
                            ->options([
                                'no_response' => 'No Response',
                                'replied' => 'Replied',
                                'interested' => 'Interested',
                                'not_interested' => 'Not Interested',
                                'auto_reply' => 'Auto Reply',
                                'bounced' => 'Bounced',
                            ])
                            ->default('no_response'),

                        Placeholder::make('email_stats')
                            ->label('Email Statistics')
                            ->content(fn ($record) => $record
                                ? "Total Emails Sent: {$record->total_emails_sent}" .
                                  ($record->last_email_sent_at
                                      ? " | Last Sent: " . $record->last_email_sent_at->diffForHumans()
                                      : " | Never sent")
                                : 'No emails sent yet')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
