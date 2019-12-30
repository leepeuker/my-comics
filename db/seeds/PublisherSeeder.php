<?php

use Phinx\Seed\AbstractSeed;

class PublisherSeeder extends AbstractSeed
{
    public function run() : void
    {
        $data = [
            [
                'id' => 1,
                'comic_vine_id' => '31',
                'name' => 'Marvel',
            ],
            [
                'id' => 2,
                'comic_vine_id' => '513',
                'name' => 'Image',
            ],
            [
                'id' => 3,
                'comic_vine_id' => '10',
                'name' => 'DC Comics',
            ]
        ];

        $publishers = $this->table('publishers');
        $publishers->insert($data)->save();
    }
}
