<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserLoginFormType;
use App\Form\UserRegistrationFormType;
use App\Helper\DownloadTrait;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    use DownloadTrait;

    private $avatarsDirectory;

    public function __construct($avatarsDirectory)
    {
        $this->avatarsDirectory = $avatarsDirectory;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(UserLoginFormType::class);

        return $this->render(
            'security/login.html.twig',
            [
                'loginForm' => $form->createView(),
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('Will be intercepted before getting here');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $formAuthenticator
    ) {
        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();

            $user = new User();
            $user->setFirstName($userModel->firstName);
            $user->setEmail($userModel->email);

            $url = 'https://robohash.org/'.$userModel->email;

            $imagename = basename($url);
            $image = DownloadTrait::getimg($url);
            file_put_contents($this->avatarsDirectory.$imagename.'.png', $image);

            $user->setImageFilename($imagename);

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $userModel->plainPassword
                )
            );

            if (true === $userModel->agreeTerms) {
                $user->agreeToTerms();
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $formAuthenticator,
                'main'
            );
        }

        return $this->render(
            'security/register.html.twig',
            [
                'registrationForm' => $form->createView(),
            ]
        );
    }
}