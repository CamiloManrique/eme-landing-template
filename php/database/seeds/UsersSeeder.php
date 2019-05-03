<?php


use Phinx\Seed\AbstractSeed;
use Illuminate\Database\Capsule\Manager as Capsule;

class UsersSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        Capsule::table('users')->insert([
            "username" => "Test User"
        ]);

    }
}
