<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $helper): Response
    {
        return $this->render('user/index.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),           
        ]);
    }

    /**
     * @Route("/logout")
     */
    public function logoutAction()
    {
    	throw new \Exception("You shouldn't be here! Go away!");
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
    	$user = new User();
    	$form = $this->createForm(UserType::class, $user, []);

    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid())
    	{
    		$password = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
    		$user->setPassword($password);

    		$em = $this->getDoctrine()->getManager();

    		$em->persist($user);

    		$em->flush();

    		$token = new UsernamePasswordToken(
    			$user,
    			$password,
    			'main',
    			$user->getRoles()
    		);

    		$this->get('security.token_storage')->setToken($token);

    		$this->get('session')->set('_security_main', serialize($token));

    		$this->addFlash('success', 'You are now registered!');

    		$this->redirectToRoute('login');
    	}
    	return $this->render('user/register.html.twig', [
    		'registration_form' => $form->createView(),
    	]);
    }
}
