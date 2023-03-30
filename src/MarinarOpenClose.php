<?php
    namespace Marinar\OpenClose;

    use Marinar\OpenClose\Database\Seeders\MarinarOpenCloseInstallSeeder;

    class MarinarOpenClose {

        public static function getPackageMainDir() {
            return __DIR__;
        }

        public static function injects() {
            return MarinarOpenCloseInstallSeeder::class;
        }
    }
