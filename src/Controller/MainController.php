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
use App\Entity\Songs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use getID3 as getID3;
use getID3_lib as getID3_lib;

/**
 * This class handles the input request and stores the data in database. And redirects
 * the user to the respective pages
 */
class MainController extends AbstractController
{
    /**
     *   @var object
     *     Intiaizes the Entity Manager instance.
     */
    private $em;
    /**
     *   @var object
     *     Setting up the repository for the Signup entity using the EntityManager.
     */
    private $loginValue;
    /**
     *   @var object
     *     Creates a new instance of the Signup entity.
     */
    private $signupData;

    /**
     * This is used to initialize the EntityManagerInterface instance, and Signup repository.
     *
     *   @param EntityManagerInterface $em
     *     Intiaizes the Entity Manager instance.
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        $this->loginValue = $em->getRepository(Signup::class);
        $this->signupData = new Signup();
    }

    /**
     * This method redirects the user to the main page.
     *
     *   @Route("/main", name="app_main")
     *
     *   @return Response
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }


    /**
     * This method redirects the user to the login page.
     *
     *   @Route("/", name=" ")
     *     Redirects to login page.
     *
     *   @return Response
     *     The response object representing the rendered login page is returned.
     */
    public function login(): Response
    {
        return $this->render('login/login.html.twig');
    }

    /**
     * This method redirects the user to the signup page.
     *
     *   @Route("/signup", name="signup")
     *     Redirects to signup page.
     *
     *   @return Response
     *     The response object representing the rendered signup page is returned.
     */
    public function signup(): Response
    {
        return $this->render('signup/signup.html.twig');
    }

    /**
     * This method validates the input email address and sends the Reset Password
     * link to the entered email address.
     *
     *   @Route("/forgot", name="forgot")
     *     Redirects to Forgot Password page.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *
     *    @return Response
     *      The response object representing the forgot password page is returned.
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
     * This method validates the input password and if all looks good, it changes
     * the password in the database of the corresponding email address the Reset
     * Password link was sent to.
     *
     *   @Route("/reset", name="reset")
     *     Redirects to Reset Password page.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the reset password page is returned.
     */
    public function reset(Request $request, SessionInterface $si): Response
    {
        // Checking if the email id is fetching from the url or the user clicks
        // on the reset password after giving new password.
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
     * This method enters the input data from the signup form into the database
     * and returns errors if any, otherwise redirects user to the login page.
     *
     *   @Route("/signupUser", name="signupUser")
     *     Redirects to signup page.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *
     *   @return Response
     *     The response object representing the signup page is returned.
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
     * This method checks the input data from the login form with the existing
     * data from the database, and if it finds a match, i.e., user exists; User
     * is redirected to the main dashboard page.
     *
     *   @Route("/loginUser", name="loginUser")
     *     Redirects to dashboard page.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the login page is returned.
     */
    public function loginUser(Request $request, SessionInterface $si): Response {
        $uName = $request->get('username');
        $pass = $request->get('pass');
        $loginValidation = new LoginValidation($uName, $pass, $this->loginValue);
        $loginValidate = $loginValidation->validateLoginForm();
        if ($loginValidate <> 1) {
            return $this->render('login/login.html.twig', [
                "error" => $loginValidate,
            ]);
        }
        $si->set('username', $uName);
        $si->set('password', $pass);
        return $this->redirectToRoute('dashboard');
    }

    /**
     * This method redirects the user back to the login page, if not logged in.
     * Otherwise, user is able to login and view the dashboard.
     *
     *   @Route("/dashboard", name="dashboard")
     *     Redirects user to login page if not logged in.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the login page is returned if user is
     *     not logged in..
     */
    public function dashboard(SessionInterface $si): Response {
        if ($si->get('username')) {
            return $this->render('dashboard/dashboard.html.twig');
        }
        return $this->redirectToRoute(' ');
    }

    /**
     * This method logs out the user, clears the session and redirects back to
     * the login page.
     *
     *   @Route("/logout", name="logout")
     *     Logs out from dashboard and redirects back to login page.
     *
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the logout action is performed.
     */
    public function logOut(SessionInterface $si): Response {
        $si->clear();
        return $this->redirectToRoute(' ');
    }

    /**
     * This method redirects the user to the profile page.
     *
     *   @Route("/profile", name="profile")
     *     Redirects to profile page.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the profile page is returned.
     */
    public function profile(Request $request, SessionInterface $si): Response {
        $uName = $si->get('username');
        $profileUid = $this->loginValue->findOneBy(['UserName' => $uName]);
        return $this->render('profile/profileEdit.html.twig',[
            'username' => $profileUid
        ]);
    }

    /**
     * This method redirects the user to the profile page.
     *
     *   @Route("/mainProfile", name="mainProfile")
     *     Redirects to profile page.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the profile page is returned.
     */
    public function mainProfile(Request $request, SessionInterface $si): Response {
        $uName = $si->get('username');
        $profileData = $this->loginValue->findOneBy(['UserName' => $uName]);
        $profileData = [
            'fname' => $profileData->getFirstName(),
            'lname' => $profileData->getLastName(),
            'username' => $uName,
            'email' => $profileData->getEmail(),
            "phone" => $profileData->getPhoneNumber(),
        ];
        return $this->render('profile/profile.html.twig',[
            'profileData' => $profileData
        ]);
    }

    /**
     * This method edits the profile information and validates the input changes.
     * If everything is validated successfully, values are changed in the database.
     *
     *   @Route("/editprofile", name="editprofile")
     *     Redirects to profile page.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the editing of profile information
     *     action is performed.
     */
    public function editProfile(Request $request, SessionInterface $si): Response {
        $uName = $si->get('username');
        $profileUid = $this->loginValue->findOneBy(['UserName' => $uName]);
        $fName = $request->get('fname');
        $lName = $request->get('lname');
        $phoneNo = $request->get('phone');
        $pwd = $request->get('pass');
        $rePwd = $request->get('repass');
        $validateProfile = new SignupValidation($fName, $lName, ' ', $phoneNo, ' ', $pwd, $rePwd, $this->loginValue);
        $validateSignup = $validateProfile->validateProfile();
        if (count($validateSignup)) {
            return $this->render('profile/profileEdit.html.twig', [
                'error' => $validateSignup,
                'username' => $profileUid
            ]);
        }
        $encryptPass = base64_encode($pwd);
        $updateUserDetails = $this->loginValue->findOneBy(['UserName' => $si->get('username')]);
        $updateUserDetails->setFirstName($fName);
        $updateUserDetails->setLastName($lName);
        $updateUserDetails->setPhoneNumber($phoneNo);
        $updateUserDetails->setPassword($encryptPass);
        $this->em->persist($updateUserDetails);
        $this->em->flush();
        return $this->render('profile/profileEdit.html.twig',[
            'username' => $profileUid,
            'success' => 'Profile Has Been Updated'
        ]);
    }

    /**
     * This method redirects the user to the upload page.
     *
     *   @Route("/upload", name="upload")
     *     Redirects to upload page.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the upload page is returned.
     */
    public function upload(Request $request, SessionInterface $si): Response {
        $uName = $si->get('username');
        $profileUid = $this->loginValue->findOneBy(['UserName' => $uName]);
        return $this->render('upload/upload.html.twig',[
            'username' => $profileUid
        ]);
    }

    /**
     * This method uploads the song, the name of artist and the album art and
     * stores the values in the Songs database.
     *
     *   @Route("/uploadSong", name="uploadSong")
     *     Redirects back to the upload page after the song has been uploaded.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the upload of song action is performed.
     */
    public function uploadSong(Request $request, SessionInterface $si): Response {
        $file = $request->files->get('upload');
        $artist = $request->get('artist');
        $thumb = $request->files->get('thumb');
        $name = $file->getClientOriginalName();
        $path = $this->getParameter('kernel.project_dir').'/public/audio';
        $file->move($path, $name);
        $uploadSong = new Songs();
        $uploadSong->setTitle($name);
        $uploadSong->setArtist($artist);
        $uploadSong->setUploadedBy($si->get('username'));
        $uploadSong->setThumbnail($thumb);
        $this->em->persist($uploadSong);
        $this->em->flush();
        return $this->render('upload/upload.html.twig',[
            'success' => 'Song Has Been Uploaded Successfully!'
        ]);
    }

    /**
     * This method redirects to playlist page.
     *
     *   @Route("/playlist", name="playlist")
     *     Redirects to playlist page.
     *
     *   @param object $request
     *     Stores the object of Request class.
     *   @param object $si
     *     Stores the object of SessionInterface class.
     *
     *   @return Response
     *     The response object representing the playlist page is returned.
     */
    public function playlist(Request $request, SessionInterface $si): Response {
        return $this->render('playlist/playlist.html.twig');
    }

}
