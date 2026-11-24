<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages\Auth;

class Login extends \Filament\Auth\Pages\Login
{
    public function mount(): void
    {
        parent::mount();

        $this->fillTestCredentials();
    }

    /**
     * When `APP_ENV` is set to `local`, fill the login form with test credentials.
     *
     * @return void
     */
    private function fillTestCredentials(): void
    {
        if (! app()->isLocal()) {
            return;
        }

        $this->form->fill([
            'email' => 'admin@example.com',
            'password' => 'password',
            'remember' => true,
        ]);
    }
}
