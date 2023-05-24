<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * This class sends Reset Password link via mail to the user.
 */
class SendMail
{
  /**
   *   @var string
   *     Stores input email address.
   */
  private $email;
  /**
   *   @var string
   *     Stores email id's password.
   */
  private $mailPass;

  /**
   * This is used to initialize the values of the input email id and store email
   * id's password.
   *
   *   @param string $email
   *     Stores input email address.
   *   @param string $mailPass
   *     Stores email id's password.
   */
  public function __construct(string $email, string $mailPass) {
    $this->email = $email;
    $this->mailPass = $mailPass;
  }

  /**
   * This method returns error message, if any. Otherwise, if email id is correct
   * and present in database, returns success message and sends the Reset Password link
   * to the input email.
   *
   *   @return mixed
   *     Returns error message, or success message and sends the Reset Password link.
   */
  public function resetPass() {
    $mail = new PHPMailer(TRUE);
    try {
      //Server settings
      $mail->isSMTP();                                            //Send using SMTP
      $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
      $mail->SMTPAuth   = TRUE;                                   //Enable SMTP authentication
      $mail->Username   = 'ghosh.ishan.27@gmail.com';                     //SMTP username
      $mail->Password   = $this->mailPass;                               //SMTP password
      $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
      $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
      //Recipients
      $mail->setFrom('ghosh.ishan.27@gmail.com', 'Ishan Ghosh');
      $mail->addAddress($this->email, 'Recipient User');     //Add a recipient
      //Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = "Reset MySQL One Password";
      $mail->Body    = "Link To Reset Password - " . $_SERVER['HTTP_HOST'] . '/reset?mail=' . base64_encode($this->email);
      $mail->send();
      return "Password Reset Mail Has Been Sent To ". $this->email;
    }
    catch (\Exception $e) {
      return "Reset Password Link Could Not Be Sent. Please Enter Correct Email ID";
    }
  }
}

?>
