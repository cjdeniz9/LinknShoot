<?php

/**
 * View model for user registration functions.
 *
 * @author jam
 * @version 210307
 */
class RegisterVM {

    public $enteredPW;
    public $enteredConfPW;
    public $registrationType;
    public $errorMsg;
    public $statusMsg;
    public $newUser;
	public $categories;
	private $categoryDAM;
  private $userDAM;


    // User type constants used for switching in the controller.
    const VALID_REGISTRATION = 'valid_registration';
    const INVALID_REGISTRATION = 'invalid_registration';

    public function __construct() {
		$this->categoryDAM = new CategoryDAM();
        $this->userDAM = new UserDAM();
        $this->errorMsg = '';
        $this->statusMsg = array();
        $this->enteredPW = '';
        $this->enteredConfPW = '';
        $this->registrationType = self::INVALID_REGISTRATION;
        $this->newUser = null;
		$this->categories = $this->categoryDAM->readCategories();
    }

    public static function getInstance() {
        $vm = new self();

        $varArray = array('email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        		'lastName' => filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        		'firstName' => filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        		'phoneNumber' => filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $vm->newUser = new User($varArray);
        $vm->enteredPW = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $vm->enteredConfPW = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($vm->validateUserInput()) {
          $vm->newUser->password
          = password_hash($vm->enteredPW, PASSWORD_DEFAULT);
          $vm->userDAM->writeUser($vm->newUser);

            $vm->registrationType = self::VALID_REGISTRATION;
        }
        return $vm;
    }

    private function validateUserInput() {
        $success = '';

        // Add validation code here.
		// If all validation tests pass, set $success = true.

        $email = filter_input(INPUT_POST, 'email');
        $firstName = filter_input(INPUT_POST, 'firstName');
        $lastName = filter_input(INPUT_POST, 'lastName');
        $phoneNumber = filter_input(INPUT_POST, 'phoneNumber');
        $password = filter_input(INPUT_POST, 'password');
        $confirmPassword = filter_input(INPUT_POST, 'confirmPassword');

        $missingFields = 0;
        $invalidEmail = 0;
        $invalidPhoneNumber = 0;

        if(hasPresence($email && $firstName && $lastName && $phoneNumber && $password && $confirmPassword)) {
            $missingFields = '';
            } else {
                $missingFields = 'Missing Fields';
            }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $invalidEmail = 'Invalid Email';
        } else {
            $invalidEmail = '';
        }

        if(hasFormatMatching($phoneNumber, '/\d{10}/')) {
            $invalidPhoneNumber = '';
        } else {
            $invalidPhoneNumber = 'Invalid Phone Number';
        }

        if($missingFields === '' && $invalidEmail === '' && $invalidPhoneNumber === '') {
             $success = true;
        } else {
            $success = false;
        }

        $error = $missingFields . '<br>' . $invalidEmail . '<br>' . $invalidPhoneNumber;

        $this->errorMsg = $error;

        return $success;
    }
}
