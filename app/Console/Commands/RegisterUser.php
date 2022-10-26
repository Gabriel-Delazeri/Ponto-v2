<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Console\Command;

class RegisterUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:user {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register User';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $passwordBeforeHash = is_null($this->option('password')) ?
                    Helper::generateStrongPassword(6, false, 'lud') :
                    $this->option('password');

        $firstName = $this->ask('Please enter the first name');

        $surname = $this->ask('Please enter the surname');

        do{
            $email = $this->ask('Please enter a e-mail');
        }while(!filter_var($email, FILTER_VALIDATE_EMAIL));

        $this->line('First Name: '.$firstName);
        $this->line('Surname: '.$surname);
        $this->line('E-mail: '.$email);
        $this->line('Password: '.$passwordBeforeHash);

        if ($this->confirm('Are you sure?', true)) {

            $user = User::create([
                'first_name' => $firstName,
                'surname'    => $surname,
                'email'      => $email,
                'password'   => bcrypt($passwordBeforeHash)
            ]);

            SendWelcomeEmailJob::dispatch($user, $passwordBeforeHash);

            $this->info("User [$email] created successfully.");
        }

        return Command::SUCCESS;
    }
}
