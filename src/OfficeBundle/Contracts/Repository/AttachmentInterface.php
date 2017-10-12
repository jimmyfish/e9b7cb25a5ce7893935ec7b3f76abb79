<?php

/**
 * Created by PhpStorm.
 * User: dzaki
 * Date: 17/06/17
 * Time: 1:05.
 */

namespace OfficeBundle\Contracts\Repository;

use OfficeBundle\Entity\Attachment;

interface AttachmentInterface
{
    /**
     * @param $id
     *
     * @return Attachment
     */
    public function findById($id);

    /**
     * @param $userId
     *
     * @return Attachment
     */
    public function findByUserId($userId);
}
