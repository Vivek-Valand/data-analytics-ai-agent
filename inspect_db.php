<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::select('SHOW TABLES');
// Key for column containing table name might vary (e.g. Tables_in_bookings_db)
// We'll iterate the first row to find the property name if needed.
$key = null;

if (!empty($tables)) {
    foreach ($tables as $tableRow) {
        $tableRowArray = (array)$tableRow;
        if (!$key) {
            $key = array_keys($tableRowArray)[0];
        }
        $tableName = $tableRowArray[$key];
        
        if (in_array($tableName, ['migrations', 'failed_jobs', 'personal_access_tokens', 'password_reset_tokens', 'users', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches'])) {
            continue;
        }

        echo "- $tableName\n";
        $columns = Schema::getColumnListing($tableName);
        echo "  Columns: " . implode(', ', $columns) . "\n";
    }
} else {
    echo "No tables found.\n";
}
