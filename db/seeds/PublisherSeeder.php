<?php

use Phinx\Seed\AbstractSeed;

class PublisherSeeder extends AbstractSeed
{
    public function run() : void
    {
        $data = [
            [
                'id' => 1,
                'comic_vine_id' => '121323',
                'name' => 'Marvel',
            ],
            [
                'id' => 2,
                'comic_vine_id' => '121324',
                'name' => 'Image',
            ],
            [
                'id' => 3,
                'comic_vine_id' => '121325',
                'name' => 'DC',
            ]
        ];

        $publishers = $this->table('publishers');
        $publishers->insert($data)->save();
    }
}
