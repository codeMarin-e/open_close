<?php
    namespace Marinar\OpenClose\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\OpenClose\MarinarOpenClose;

    class MarinarOpenCloseInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_open_close';
            static::$packageDir = MarinarOpenClose::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoInstall();

            $this->refComponents->info("Done!");
        }

    }
