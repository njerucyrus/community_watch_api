<?php
/**
 * Created by PhpStorm.
 * User: hudutech
 * Date: 3/1/17
 * Time: 5:49 PM
 */
require 'connect.php';
/*
 * get the json data from the android device
 */
$data = json_decode(file_get_contents('php://input'), true);
/*
 * define an api response array variable
 *  to hold the responses of the php api
 */

$api_response = array();

$request_method = $_SERVER['REQUEST_METHOD'];

/*
 * Create a class to authenticate user
 */

class Auth
{
    public function authenticate($username, $password, $user_type)
    {
        global $api_response;

        /*
         * Create an instance of Connection class
         */

        $connection = new Connection();

        /*
         * Create a connection to the database
         */
        $conn = $connection->getConnection();


        /*
          * perform the sql query to check if the user exists
          * in the database
          */
        $sql = "SELECT * FROM users 
                WHERE
                 username='{$username}'
                 AND  
                 password='{$password}'
                 AND  
                 user_type='{$user_type}'
                 ";

        $query = $conn->query($sql);
        if ($query) {
            /*
             * The sql passes Now check of there is any row returned
             */
            if ($query->num_rows == 1) {
                /*
                 * the query has returned exactly one user
                 * therefore the user is authenticated
                 * We will print json response to the show
                 * the user details supplied are correct therefore
                 * the user can login.
                 */
                $api_response['status'] = 'success';
                $api_response['statusCode'] = 200;
                $api_response['message'] = "Login success correct credentials provided!";

                print_r(json_encode($api_response));
            } elseif ($query->num_rows > 1 )
            {
                /*
                 * respond with a status code 401 Access Denied
                 * since multiple users are found
                 */
                $api_response['status'] = 'failed';
                $api_response['statusCode'] = 401;
                $api_response['message'] = 'Access Denied! Found Multiple users with same credentials.';

                print_r(json_encode($api_response));
            }
            /* the query returned 0 rows meaning
            * the username, password, user_type combinations
            * are invalid
            */
        else {

                $api_response['status'] = 'failed';
                $api_response['statusCode'] = 401;
                $api_response['message'] = 'Invalid login credentials';
                print_r(json_encode($api_response));
            }
        }
        else
        {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500;
            $api_response['message'] = 'Error occurred! ['.$conn->error.']';
        }


    }
}

// check the http request method used

if ($request_method != 'POST') {
    /*
  *  respond with an http status code
  * 405 Method Not Allowed
  */
    $api_response['status'] = 'failed';
    $api_response['statusCode'] = 405;
    $api_response['message'] = 'HTTP REQUEST METHOD NOT ALLOWED. Use only POST';
    print_r(json_encode($api_response));
} else {
    /*
     * now request  method is post so continue to
     * authenticate the user
     */

    $auth = new Auth();

    if (!empty($data)) {
        $username = $data['username'];
        $password = $data['password'];
        $user_type = $data['user_type'];

        $auth->authenticate($username, $password, $user_type);
    } else {
        $api_response['status'] = 'failed';
        $api_response['statusCode'] = 404;
        $api_response['message'] = 'No data received';
        print_r(json_encode($api_response));
    }
}