<?php
/**
 * Created by PhpStorm.
 * User: hudutech
 * Date: 3/2/17
 * Time: 12:14 PM
 */


/*
 * import the database connection file
 */
require 'connect.php';
/*
 * get the json data from the android device
 */
$data = json_decode(file_get_contents('php://input'), true);

/*
 * Declare request method to switch between the requests
 */

$request_method = $_SERVER['REQUEST_METHOD'];

class Issue
{
    private $issueText;
    private $postedBy;

    /**
     * @return mixed
     */
    public function getIssueText()
    {
        return $this->issueText;
    }

    /**
     * @param mixed $issueText
     */
    public function setIssueText($issueText)
    {
        $this->issueText = $issueText;
    }

    /**
     * @return mixed
     */
    public function getPostedBy()
    {
        return $this->postedBy;
    }

    /**
     * @param mixed $postedBy
     */
    public function setPostedBy($postedBy)
    {
        $this->postedBy = $postedBy;
    }

    /*
     * function to save issue in the database
     *
     */
    public function postIssue()
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
         * Create insert sql statement
         */

        $insert = "INSERT INTO issues(posted_by, text) 
                  VALUES (
                  '{$this->getPostedBy()}',
                  '{$this->getIssueText()}'
                  )";

        $query = $conn->query($insert);

        /*
         * Check if the query has executed successfully Otherwise
         * respond with an error.
         */

        if ($query) {
            $api_response['status'] = 'success';
            $api_response['statusCode'] = 200;
            $api_response['message'] = 'Issue Posted Successfully!';
            print_r(json_encode($api_response));
        } else {

            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500;
            $api_response['message'] = 'Server Error. ' . $conn->error;
            print_r(json_encode($api_response));
        }

    }


    public function updateIssue($id)
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
         * Create update sql statement
         */

        $update = "UPDATE issues SET
                    text='{$this->getIssueText()}'
                   WHERE id='{$id}'
                ";

        $query = $conn->query($update);

        if ($query) {
            $api_response['status'] = 'success';
            $api_response['statusCode'] = 201;

            $api_response['message'] = 'Issue updated successfully!';

            /*
             * print the json response
             */
            print_r(json_encode($api_response));
        } else {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500;
            $api_response['message'] = 'Error occurred while updating [' . $conn->error . ']';
            print_r(json_encode($api_response));
        }
    }

    public function deleteIssue($id)
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
         * Create delete sql statement
         */
        $delete = "DELETE FROM issues WHERE id='{$id}'";

        /*
         * Excecute the query
         */
        $query = $conn->query($delete);

        if ($query) {
            $api_response['status'] = 'success';
            $api_response['statusCode'] = 204; /* No content */

            $api_response['message'] = 'Issue deleted successfully!';

            /*
             * print the json response
             */
            print_r(json_encode($api_response));
            $conn->close();
        } else {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500;
            $api_response['message'] = 'Error occurred while deleting issue [' . $conn->error . ']';
            print_r(json_encode($api_response));
        }


    }

    public function getIssues()
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
         * Create update sql statement
         */

        $select = "SELECT * FROM issues WHERE 1";

        /*
        * Query the database to get the posted suggestions
        */

        $query = $conn->query($select);

        /*
         * loop through the results to create a json array of response
         */

        if ($query->num_rows > 0) {

            $json_array = array();

            while ($row = $query->fetch_assoc()) {
                $api_response['id'] = $row['id'];
                $api_response['posted_by'] = $row['posted_by'];
                $api_response['text'] = $row['text'];
                /*
                 * push the response the the json_array
                 */
                array_push($json_array, $api_response);
            }
            /*
             * print the response
             */
            print_r(json_encode($json_array));
            $conn->close();
        }
    }
}

/*
 * initialize the Suggestions class
 */

$issue = new Issue();

/*
* Check if the data sent from the android
* is Not empty other wise provide an error
* message to the user to input data.
* this is the server validation enforcement
*/

if (!empty($data))
{
    if ($request_method == 'POST')
    {
        /*
         * set the issue properties using the class setters methods
         */
        $issue->setPostedBy($data['posted_by']);
        $issue->setIssueText($data['text']);

        /*
         * Save issue to the database
         */
        $issue->postIssue();
    }
    elseif ($request_method == 'PUT')
    {
        /*
         * set the issue properties using the class setters methods
         */
        $issue->setPostedBy($data['posted_by']);
        $issue->setIssueText($data['text']);

        /*
         * Now get the id for the issue we want to update
         */

        $id = $data['id'];

        /*
         * update the issues now
         */

        $issue->updateIssue($id);

    }
    elseif ($request_method == 'DELETE')
    {
        /*
         * Now get the id for the issue we want to delete
         */

        $id = $data['id'];

        /*
         * update the issues now
         */

        $issue->deleteIssue($id);
    }
}

/*
 * Check if the http request is get
 * the display the posts
 * this request is sent with empty data so
 * we consider this option
 */

elseif (empty($data) && $request_method == 'GET')
{
    /*
     * Fetch all the issues from the database
     */

    $issue->getIssues();
}

else
{
    /*
    * Android sent blank data so we display the error
    */

    $api_response['status'] = 'failed';

    $api_response['message'] = 'No json data was received';
    /*
     * Print the response to the server
     */
    print_r(json_encode($api_response));

}