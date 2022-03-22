<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Passport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:move';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'move env oauth to storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $OAUTH_PUBLIC_KEY = getenv("OAUTH_PUBLIC_KEY");
        $OAUTH_PRIVATE_KEY = getenv("OAUTH_PRIVATE_KEY");
        if ($OAUTH_PUBLIC_KEY && $OAUTH_PRIVATE_KEY) {
            file_put_contents(storage_path('oauth-public.key'), $OAUTH_PUBLIC_KEY);
            file_put_contents(storage_path('oauth-private.key'), $OAUTH_PRIVATE_KEY);
            $this->info('move OAUTH key success');
        } else {
            $this->error('not find OAUTH key in env');
        }
    }
}
