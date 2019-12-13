<?php

use Phinx\Seed\AbstractSeed;

class ComicsSeeder extends AbstractSeed
{
    public function getDependencies() : array
    {
        return [
            'PublisherSeeder'
        ];
    }

    public function run() : void
    {
        $data = [
            [
                'id' => 1,
                'cover_id' => null,
                'comic_vine_id' => 101,
                'name' => 'Comic 1',
                'year' => 2001,
                'publisher_id' => 1,
                'description' => 'Lorem ipsum',
                'added_to_collection' => '2000-01-01 00:00:01',
                'price' => 100,
            ],
            [
                'id' => 2,
                'cover_id' => null,
                'comic_vine_id' => 102,
                'name' => 'Comic 2',
                'year' => 2002,
                'publisher_id' => 2,
                'description' => 'Lorem ipsum',
                'added_to_collection' => '2000-01-01 00:00:02',
                'price' => 200,
            ],
            [
                'id' => 3,
                'cover_id' => null,
                'comic_vine_id' => 103,
                'name' => 'Comic 3',
                'year' => 2003,
                'publisher_id' => 3,
                'description' => 'Lorem ipsum',
                'added_to_collection' => '2000-01-01 00:00:03',
                'price' => 300,
            ],
        ];

        $comics = $this->table('comics');
        $comics->insert($data)->save();
    }
}
