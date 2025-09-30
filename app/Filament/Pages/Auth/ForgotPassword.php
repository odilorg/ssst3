<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Password;
use Filament\Notifications\Notification;

class ForgotPassword extends Page
{
    protected string $view = 'filament-panels::pages.auth.request-password-reset';

    public ?string $email = '';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
            ])
            ->statePath('data');
    }

    public function requestPasswordReset(): void
    {
        $data = $this->form->getState();

        $status = Password::sendResetLink(
            ['email' => $data['email']]
        );

        if ($status === Password::RESET_LINK_SENT) {
            Notification::make()
                ->title('Password reset link sent!')
                ->body('Check your email for the password reset link.')
                ->success()
                ->send();

            $this->email = '';
        } else {
            Notification::make()
                ->title('Error')
                ->body('Unable to send password reset link. Please try again.')
                ->danger()
                ->send();
        }
    }

    public function getTitle(): string | Htmlable
    {
        return 'Forgot Password';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Forgot Password';
    }

    public function getSubheading(): string | Htmlable
    {
        return 'Enter your email address and we will send you a password reset link.';
    }
}
