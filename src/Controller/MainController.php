<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\SignupValidation;
use App\Services\LoginValidation;
use App\Services\SendMail;
use App\Entity\Signup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MainController extends AbstractController
{
    private $em;
    private $loginValue;
    private $signupData;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->loginValue = $em->getRepository(Signup::class);
        $this->signupData = new Signup();
    }

    /**
     * Method index
     *
     * @Route("/main", name="app_main")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }


    /**
     * Method login
     *
     *   @Route("/")
     *     Redirects to login page.
     *
     * @return Response
     */
    public function login(): Response
    {
        return $this->render('login/login.html.twig');
    }

    /**
     * Method signup
     *
     *   @Route("/signup", name="signup")
     *     Redirects to signup page.
     *
     * @return Response
     */
    public function signup(): Response
    {
        return $this->render('signup/signup.html.twig');
    }

    /**
     * Method forgot
     *
     *   @Route("/forgot", name="forgot")
     *     Redirects to Forgot Password page.
     *
     * @return Response
     */
    public function forgot(Request $request): Response
    {
        if ($request->get('email')) {
            $email = $request->get('email');
            $userForgot = $this->loginValue->findOneBy(['Email' => $email]);
            if (!$userForgot) {
                return $this->render('forgot/forgot.html.twig', [
                    'error' => 'Email Address Not Found',
                ]);
            }
            $mailPass = $_ENV['SendMailPassword'];
            $forgotMail = new SendMail($email, $mailPass);
            $mailText = $forgotMail->resetPass();
            return $this->render('forgot/forgot.html.twig', [
                'success' => $mailText,
            ]);
        }
        return $this->render('forgot/forgot.html.twig');
    }

    /**
     * Method reset
     *
     *   @Route("/reset", name="reset")
     *     Redirects to Reset Password page.
     *
     * @return Response
     */
    public function reset(Request $request, SessionInterface $si): Response
    {
        if ($request->get('mail')) {
            $email = $request->get('mail');
            $email = base64_decode($email);
            $si->set('email', $email);
            return $this->render('reset/reset.html.twig');
        }
        $pwd = $request->get("pass");
        $rePwd = $request->get("repass");
        if (empty($pwd) || empty($rePwd)) {
            return $this->render('reset/reset.html.twig', [
                'error' => 'Passwords Cannot Be Empty',
            ]);
        }
        elseif ($pwd <> $rePwd) {
            return $this->render('reset/reset.html.twig', [
                'error' => 'Passwords Do Not Match',
            ]);
        }
        elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s])\S{8,}$/', $pwd)) {
            return $this->render('reset/reset.html.twig', [
                'changePassword' => 'Password Must Have At Least 1 Lowercase Letter, 1 Uppercase Letter, 1 Digit, 1 Special Character, And Be A Minimum Of 8 Characters Long',
            ]);
        }
        $userResetPassword = $this->loginValue->findOneBy(['Email' => $si->get('email')]);
        $userResetPassword->setPassword(base64_encode($pwd));
        $this->em->persist($userResetPassword);
        $this->em->flush();
        $si->clear();
        return $this->render('reset/reset.html.twig', [
            'success' => "Password Has Been Changed",
        ]);
    }

    /**
     * Method signupUser
     *
     *   @Route("/signupUser", name="signupUser")
     *     Redirects to signup page.
     *
     * @return Response
     */
    public function signupUser(Request $request): Response
    {
        $fName = $request->get('fname');
        $lName = $request->get('lname');
        $uName = $request->get('username');
        $phoneNo = $request->get('phone');
        $email = $request->get('email');
        $pwd = $request->get('pass');
        $rePwd = $request->get('repass');
        $validateObject = new SignupValidation($fName, $lName, $uName, $phoneNo, $email, $pwd, $rePwd, $this->loginValue);
        $validateSignup = $validateObject->validateForm();
        if (count($validateSignup)) {
            return $this->render('signup/signup.html.twig', [
                'error' => $validateSignup,
            ]);
        }
        $encryptPass = base64_encode($pwd);
        $this->signupData->storeNewUser($fName, $lName, $uName, $phoneNo, $email, $encryptPass);
        $this->em->persist($this->signupData);
        $this->em->flush();
        return $this->render('login/login.html.twig', [
            "newUser" => ""
        ]);
    }

    /**
     * Method signupUser
     *
     *   @Route("/loginUser", name="loginUser")
     *     Redirects to dashboard page.
     *
     * @return Response
     */
    public function loginUser(Request $request): Response {
        $uName = $request->get('username');
        $pass = $request->get('pass');
        $loginValidation = new LoginValidation($uName, $pass, $this->loginValue);
        $loginValidate = $loginValidation->validateLoginForm();
        if ($loginValidate <> 1) {
            return $this->render('login/login.html.twig', [
                "error" => $loginValidate,
            ]);
        }
        return $this->render('dashboard/dashboard.html.twig');
    }
}
