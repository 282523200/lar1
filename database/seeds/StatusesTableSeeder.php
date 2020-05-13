<?php

use Illuminate\Database\Seeder;
use App\Status;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_ids = ['1','2','3'];
        //app 就是new一个实例
        $faker = app(Faker\Generator::class);

        $statuses = factory(Status::class)->times(30)
            ->make()
            ->each(function ($status) use ($faker, $user_ids) {
                //随机取出值
                $status->user_id = $faker->randomElement($user_ids);
            });

        Status::insert($statuses->toArray());
    }
}
