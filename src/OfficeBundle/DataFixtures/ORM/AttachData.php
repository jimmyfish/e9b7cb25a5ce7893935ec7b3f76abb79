<?php

/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 09/05/17
 * Time: 23:36.
 */

namespace OfficeBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Alice\DataFixtureLoader;

class AttachData extends DataFixtureLoader
{
    protected function getFixtures()
    {
        return array(__DIR__.'/config_data.yml');
    }
}
