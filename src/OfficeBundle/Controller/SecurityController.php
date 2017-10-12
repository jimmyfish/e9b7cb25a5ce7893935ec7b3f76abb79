<?php

namespace OfficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('office_admin_index');
        } elseif ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('office_user_homepage');
        } elseif ($this->isGranted('ROLE_VALIDATOR')) {
            return $this->redirectToRoute('office_validator_index');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('OfficeBundle:Default:login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    public function loginCheckAction()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('office_admin_index');
        } elseif ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('office_user_homepage');
        } elseif ($this->isGranted('ROLE_VALIDATOR')) {
            return $this->redirectToRoute('office_validator_index');
        } elseif ($this->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
            return $this->redirectToRoute('office_login');
        }
    }
}
