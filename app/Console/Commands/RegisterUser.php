<?php

namespace App\Console\Commands;

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
                    self::generateStrongPassword(6, false, 'lud') :
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

    public static function generateStrongPassword($length = 7, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';

        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];

        $password = str_shuffle($password);

        if(!$add_dashes)
            return $password;

        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }
}
