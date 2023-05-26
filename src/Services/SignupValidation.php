<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This class validates all the input details from the signup form and returns
 * errors if any.
 */
class SignupValidation
{

  /**
   *   @var string
   *     Stores the username.
   */
  private $userName;
  /**
   *   @var string
   *     Stores the first name.
   */
  private $fName;
  /**
   *   @var string
   *     Stores the last name.
   */
  private $lName;
  /**
   *   @var string
   *     Stores the email address.
   */
  private $email;
  /**
   *   @var string
   *     Stores the phone number.
   */
  private $phone;
  /**
   *   @var string
   *     Stores the input from password field.
   */
  private $pwd;
  /**
   *   @var string
   *     Stores the input from confirm password field.
   */
  private $repwd;
  /**
   *   @var object
   *     Stores the object of Signup class.
   */
  private $loginValue;
  /**
   *   @var array
   *     Stores the errors in array.
   */
  public $error = [];

  /**
   * This is used to intialize the input values and stores the values in class variables.
   *
   *   @param string $fName
   *     Stores the first name.
   *   @param string $lName
   *     Stores the last name.
   *   @param string $uName
   *     Stores the username.
   *   @param string $phoneNo
   *     Stores the phone number.
   *   @param string $email
   *     Stores the email address.
   *   @param string $pwd
   *     Stores the input from password field.
   *   @param string $rePwd
   *     Stores the input from confirm password field.
   *   @param object $loginValue
   *     Stores the object value of Signup class.
   */
  public function __construct(string $fName, string $lName, string $uName, string $phoneNo, string $email, string $pwd, string $rePwd, object $loginValue) {
    $this->userName = $uName;
    $this->fName = $fName;
    $this->lName = $lName;
    $this->email = $email;
    $this->phone = $phoneNo;
    $this->pwd = $pwd;
    $this->repwd = $rePwd;
    $this->loginValue = $loginValue;
  }

  /**
   * This method calls all the validation methods of the input fields and returns
   * error, if any.
   *
   *   @return mixed
   *     Returns error from all the input fields, if any.
   */
  public function validateForm() {
    $this->validateUsername();
    $this->validateName();
    $this->validatePhone();
    $this->validateEmail();
    $this->validatePassword();
    return $this->error;
  }

  /**
   * This method calls the validations of First & Last Name, Phone Number and
   * Password. And returns error, if any.
   *
   *   @return mixed
   *     Returns error from first and last name, phone number and password fields,
   *     if any.
   */
  public function validateProfile() {
    $this->validateName();
    $this->validatePhone();
    $this->validatePassword();
    return $this->error;
  }

  /**
   * This method validates the input value of the username field and returns error
   * message, if any.
   *
   *   @return mixed
   *     Returns error message.
   */
  public function validateUsername() {
    // Checking if input username is empty or not.
    if (empty($this->userName)) {
      $this->error['username'] = "Username Cannot Be Empty";
    }
    // Checking if input username matches the specified pattern or not.
    elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $this->userName)) {
        $this->error['username'] = "Invalid Username Format";
      }
    }

  /**
   * This method validates the input value of the first and last name field and
   * returns error message, if any.
   *
   *   @return mixed
   *     Returns error message.
   */
  public function validateName()
  {
    // Checking if input first name is empty or not.
    if (empty($this->fName)) {
      $this->error['fname'] = "First Name Cannot Be Empty";
    }
    // Checking if input first name contains only alphabets or not.
    elseif ((!preg_match("/^[a-zA-Z-']*$/", $this->fName))) {
      $this->error['fname'] = "First Name Can Only Contain Alphabets";
    }
    // Checking if input last name is empty or not.
    if (empty($this->lName)) {
      $this->error['lname'] = "Last Name Cannot Be Empty";
    }
    // Checking if input last name contains only alphabets or not.
    elseif ((!preg_match("/^[a-zA-Z-']*$/", $this->lName))) {
      $this->error['lname'] = "Last Name Can Only Contain Alphabets";
    }
  }

  /**
   * This method validates the input value of the phone number field and returns
   * error message, if any.
   *
   *   @return mixed
   *     Returns error message.
   */
  public function validatePhone() {
    if (empty($this->phone)) {
      $this->error['phone'] = "Phone Number Cannot Be Empty";
    }
    elseif (!preg_match('/^[6-9]\d{9}$/', $this->phone)) {
      $this->error['phone'] = "Please Enter A Valid 10 Digit Indian Phone Number";
    }
  }

  /**
   * This method validates the input value of the email field and returns error
   * message, if any.
   *
   *   @return mixed
   *     Returns error message.
   */
  public function validateEmail() {
    if (empty($this->email)) {
      $this->error['email'] = "Email ID Cannot Be Empty";
    }
    elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
      $this->error['email'] = "Invalid Email ID Syntax";
    }
    $userExist = $this->loginValue->findOneBy(['Email' => $this->email]);
    if ($userExist) {
      $this->error['email'] = 'User Already Exists. Please Login Or Create New User';
    }
  }

  /**
   * This method validates the input value of the password fields and returns error
   * message, if any.
   *
   *   @return mixed
   *     Returns error message.
   */
  public function validatePassword() {
    if (empty($this->pwd) || empty($this->repwd)) {
      $this->error['pass'] = "Password Cannot Be Empty";
    }
    elseif ($this->pwd <> $this->repwd) {
      $this->error['pass'] = "Passwords Do Not Match";
    }
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s])\S{8,}$/', $this->pwd)) {
      $this->error['pass'] = "Password Must Have At Least 1 Lowercase Letter, 1 Uppercase Letter, 1 Digit, 1 Special Character, And Be A Minimum Of 8 Characters Long";
    }
  }
}
