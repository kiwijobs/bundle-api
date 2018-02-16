<?php

namespace Absolvent\api\Console\Commands\Db;

use Illuminate\Console\Command;
use DB;

final class DbDropTables extends Command
{
    const SIGNATURE = 'db:drop:tables {--y|yes}';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::SIGNATURE;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all database tables (RUN ONLY IN NON-PRODUCTION ENV)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (env('APP_ENV') === 'production') {
            $this->output->writeln("Cannot run this command in production!");
            return -2;
        }

        if (!$this->input->getOption('yes') && !$this->confirm('Confirm drop all tables in the database?')) {
            $this->output->error('Drop Tables command aborted');
            return -1;
        }

        $tables = collect(DB::select('SHOW TABLES'));

        if ($tables->isEmpty()) {
            $this->output->warning('No tables to drop');
            return 0;
        }

        $dropList = $tables->map(function ($table) {
            return head((array) $table);
        })->implode(',');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement("DROP TABLE $dropList");
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->comment('All tables dropped');

        return 0;
    }
}
