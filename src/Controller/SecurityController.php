<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        //dump($utils);die();
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/index.html.twig', ['error' => $error]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    { }
}
