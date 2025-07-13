<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ProductionOptimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:production-optimize {--force : Force optimization without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the application for production deployment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will optimize the application for production. Continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('🚀 Starting production optimization...');

        try {
            // 1. Clear all caches
            $this->info('📦 Clearing caches...');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            // 2. Cache configurations
            $this->info('⚡ Caching configurations...');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            // 3. Optimize autoloader
            $this->info('🔧 Optimizing autoloader...');
            $this->execCommand('composer install --optimize-autoloader --no-dev');

            // 4. Build assets
            $this->info('🎨 Building assets...');
            $this->execCommand('npm run build');

            // 5. Set proper permissions
            $this->info('🔐 Setting file permissions...');
            $this->setPermissions();

            // 6. Create storage link if not exists
            $this->info('🔗 Creating storage link...');
            if (!File::exists(public_path('storage'))) {
                Artisan::call('storage:link');
            }

            // 7. Optimize database
            $this->info('🗄️ Optimizing database...');
            $this->optimizeDatabase();

            // 8. Health check
            $this->info('🏥 Running health checks...');
            $this->healthCheck();

            $this->info('✅ Production optimization completed successfully!');
            $this->info('');
            $this->info('📋 Next steps:');
            $this->info('1. Set up your web server (Nginx/Apache)');
            $this->info('2. Configure SSL certificates');
            $this->info('3. Set up queue workers');
            $this->info('4. Configure monitoring');
            $this->info('5. Set up backups');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Optimization failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Execute a shell command
     */
    private function execCommand($command)
    {
        $output = [];
        $returnCode = 0;
        
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Command failed: ' . implode("\n", $output));
        }
        
        $this->line('✓ ' . $command);
    }

    /**
     * Set proper file permissions
     */
    private function setPermissions()
    {
        $paths = [
            storage_path() => '775',
            'bootstrap/cache' => '775',
            'public' => '755',
        ];

        foreach ($paths as $path => $permission) {
            $fullPath = base_path($path);
            if (File::exists($fullPath)) {
                chmod($fullPath, octdec($permission));
                $this->line("✓ Set permissions {$permission} on {$path}");
            }
        }
    }

    /**
     * Optimize database
     */
    private function optimizeDatabase()
    {
        try {
            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            
            $this->line('✓ Database optimized');
        } catch (\Exception $e) {
            $this->warn('⚠ Database optimization skipped: ' . $e->getMessage());
        }
    }

    /**
     * Run health checks
     */
    private function healthCheck()
    {
        $checks = [
            'Configuration cached' => File::exists(bootstrap_path('cache/config.php')),
            'Routes cached' => File::exists(bootstrap_path('cache/routes.php')),
            'Views cached' => File::exists(bootstrap_path('cache/views.php')),
            'Storage link exists' => File::exists(public_path('storage')),
            'Storage writable' => is_writable(storage_path()),
            'Bootstrap cache writable' => is_writable(bootstrap_path('cache')),
        ];

        foreach ($checks as $check => $result) {
            $status = $result ? '✓' : '✗';
            $this->line("{$status} {$check}");
        }
    }
} 