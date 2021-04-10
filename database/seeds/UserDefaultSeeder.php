<?php

use Illuminate\Database\Seeder;
use App\User;

class UserDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
    	if(User::count() < 1) {

    		$password = 'testtest';

	    	User::create([
	    		'name' => 'TEST',
				'email' => 'test@test.mx',
				'password' => bcrypt($password)
	    	]);
	    }

    }
}
