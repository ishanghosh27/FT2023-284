<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This class validates the input login details from login form and returns errors
 * if any.
 */
class LoginValidation
{
  /**
   *   @var string
   *     Stores the username.
   */
  private $userName;
  /**
   *   @var string
   *     Stores the password.
   */
  private $pwd;
  /**
   *   @var object
   *     Stores the object of Signup class.
   */
  private $loginValue;

  /**
   * This is used to initialize the values of the inputs and store the values in
   * class variables.
   *
   *   @param string $uName
   *     Stores the username.
   *   @param string $pwd
   *     Stores the password.
   *   @param object $loginValue
   *     Stores the object value of Signup class.
   */
  public function __construct(string $uName, string $pwd, object $loginValue) {
    $this->userName = $uName;
    $this->pwd = $pwd;
    $this->loginValue = $loginValue;
  }

  /**
   * This method validates the input values and returns error message if any.
   * Otherwise if everything is fine, returns 1.
   *
   *   @return mixed
   *     Returns error message, or 1.
   */
  public function validateLoginForm() {
    // Checking if the input username is empty or not.
    if (empty($this->userName)) {
      return "Username Cannot Be Empty";
    }
    $userValue = ($this->loginValue->findOneBy(["UserName" => $this->userName]));
    // Checking if the input username exists in the database or not.
    if (!$userValue) {
      return "Username Not Found";
    }
    $passValue = $userValue->getPassword();
    $passValue = base64_decode($passValue);
    // Checking if the input password is empty or not.
    if (empty($this->pwd)) {
      return "Password Cannot Be Empty";
    }
    // Checking if the input password exists in the database or not.
    elseif ($passValue <> $this->pwd) {
      return "Incorrect Password";
    }
    return 1;
  }
}
