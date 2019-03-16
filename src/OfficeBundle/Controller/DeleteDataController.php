<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 3/16/19
 * Time: 10:08 PM
 */

namespace OfficeBundle\Controller;


use OfficeBundle\Entity\UserPersonal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DeleteDataController extends Controller
{

    public function deleteUserDataAction(Request $request)
    {
        $manager = $this->getDoctrine()->getEntityManager();

        $data['user'] = $manager->getRepository(UserPersonal::class)->findAll();

        if('POST' == $request->getMethod()) {
            $data = $request->get('user-list');
            foreach ($data as $value) {
                $userData = $manager->getRepository(UserPersonal::class)->findOneBy(['id' => $value]);

                $manager->remove($userData);
            }

            $manager->flush();

            $this->get('session')->getFlashBag()->add('message_success', 'Proses hapus user telah berhasil di lakukan.');

            return $this->redirectToRoute('office_admin_delete_multiple');
        }

        return $this->render('OfficeBundle:admin:delete-list.html.twig',[
            'data' => $data
        ]);

    }

}