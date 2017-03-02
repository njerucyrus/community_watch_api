<?php
/**
 * Created by PhpStorm.
 * User: hudutech
 * Date: 3/2/17
 * Time: 8:04 AM
 */

require 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);
/*
 * Declare request method to switch between the requests
 */
$request_method = $_SERVER['REQUEST_METHOD'];
/*
 * declare a global array to hold the api responses
 */
$api_response = array();
class User
{
    private $fullName;
    private $regNo;

    private $type;

    private $username;

    private $password;


    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return mixed
     */
    public function getRegNo()
    {
        return $this->regNo;
    }

    /**
     * @param mixed $regNo
     */
    public function setRegNo($regNo)
    {
        $this->regNo = $regNo;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /*
     * Register the user using this function
     */

    function registerUser()
    {
        global $request_method, $api_response;
        /*
         * Check if the request method is POST
         */
        if ($request_method == 'POST') {
            /*
             * Create an instance of Connection class
             */
            $connection = new Connection();
            /*
             * Create a connection to the database
             */
            $conn = $connection->getConnection();


            $sql_statement = " INSERT INTO users(username, fullname, regno, user_type, password)
                VALUES(
                    '{$this->getUsername()}',
                     '{$this->getFullName()}', 
                     '{$this->getRegNo()}',
                     '{$this->getType()}',
                      '{$this->getPassword()}'
                  )";

            /*
             * Execute the query
             */

            $query = $conn->query($sql_statement);

            if ($query) {
                $api_response['status'] = 'success';
                $api_response['statusCode'] = 200;
                $api_response['message'] = 'User Registered Successfully!';
                print_r(json_encode($api_response));
            } /*
     * Error occurred so we print the error
     */
            else {

                $api_response['status'] = 'failed';
                $api_response['statusCode'] = 500;
                $api_response['message'] = 'Server Error. User already exists';
                print_r(json_encode($api_response));
            }

        }
        else
        {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 405;
            $api_response['message'] = 'Invalid http request method only POST allowed';
            print_r(json_encode($api_response));
        }

        $conn->close();
    }
}

/*
 * Now Check if there is any data sent
 * to this api if data is sent
 * we proceed to register the user
 */

if(!empty($data)){
    /*
    * initialize the User class
    */

    $user = new User();

    /*
     * set the user properties using the class setters methods
     *
     */
    $user->setUsername($data['username']);
    $user->setFullName($data['fullname']);

    $user->setRegNo($data['regno']);
    $user->setType($data['user_type']);
    $user->setPassword($data['password']);
    /*
     * now save the user in the database
     */
    $user->registerUser();
}
else
{
    /*
     * Print error to show data sent was empty
     */
    $api_response['status'] = 'failed';
    $api_response['statusCode'] = 404;
    $api_response['message'] = 'No data received!';

    /*
     * Print the json response
     */
    print_r(json_encode($api_response));
}

