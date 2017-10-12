<?php

namespace OfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PrintController extends Controller
{
    public function karyawanAction()
    {
        return $this->render('OfficeBundle:print:data-karyawan.html.twig');
    }
}
