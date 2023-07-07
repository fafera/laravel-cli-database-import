<?php

namespace App\Console\Commands;

use phpseclib3\Net\SFTP;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportDatabaseCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a SQL file into the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->truncate();
        $this->dump();
        $this->populate();
    }
    private function truncate():void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->info("Table $table truncated successfully.");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    private function dump():void
    {
        $sftp = new SFTP(env('SSH_DOMAIN'));
        if (! $sftp->login(env('SSH_USER'), env('SSH_PASSWORD'))) {
            $this->error('Falha no Login!');
            return;
        }
        $dumpFile = $this->argument('file');
        $sftp->exec('cd '.env('SSH_PROJECT_PATH'));
        $sftp->exec("mysqldump -u ".env('SSH_DB_USER')." -p'".env('SSH_DB_PASSWORD')."' ".env('SSH_DB_NAME')."  > $dumpFile --no-create-info ");
        $contents = $sftp->get($dumpFile);
        if (!is_string($contents)) {
            $this->error('Falha no Dump!');
            return;
        }
        Storage::disk('public')->put($dumpFile, $contents);
        $sftp->delete($dumpFile);
        $this->info('Dump realizado com sucesso!');
    }
    private function populate():void
    {
        $file = $this->argument('file');

        if (! Storage::disk('public')->exists($file)) {
            $this->error('Falha na hora de importar os dados!');
            return;
        }
        DB::unprepared(Storage::disk('public')->get($file));

        $this->info('Importação concluída com sucesso!');
    }
}
