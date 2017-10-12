<?php

namespace OfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AttachmentController extends Controller
{
    public function uploadAction()
    {
        return $this->render('OfficeBundle:attachment:create.html.twig');
    }
}
