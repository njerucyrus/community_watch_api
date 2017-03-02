<?php
/**
 * Created by PhpStorm.
 * User: hudutech
 * Date: 3/2/17
 * Time: 11:02 AM
 */



/*
 * import the database connection file
 */
require 'connect.php';
/*
 * get the json data from the android device
 */
$data = json_decode(file_get_contents('php://input'), true);

$api_response = array();

/*
 * Declare request method to switch between the requests
 */

$request_method = $_SERVER['REQUEST_METHOD'];

/*
 * create a Suggestion class
 */

class Suggestion
{
    private $postedBy;
    private $longitude;
    private $latitude;
    private $category;
    private $comment;
    private $imagePath;

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

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @param mixed $imagePath
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /*
     * function to save suggestion post in the database
     */

    public function savePost(){

        global $api_response;

        /*
         * Create an instance of Connection class
         */

        $connection = new Connection();

        /*
         * Create a connection to the database
         */
        $conn = $connection->getConnection();

        $insert = "INSERT INTO 
              suggestions(
              posted_by,
              category,
              lgn, 
              lat,
              comment,
              image_path)
              VALUES (
              '{$this->getPostedBy()}',
              '{$this->getCategory()}',
              '{$this->getLongitude()}',
              '{$this->getLatitude()}',
              '{$this->getComment()}',
              '{$this->getImagePath()}'
              )";


        /*
         * Execute the statement  using the query method
         */
        $query = $conn->query($insert);

        /*
         * Check if the query has executed successfully Otherwise
         * respond with an error.
         */

        if ($query)
        {
            $api_response['status'] = 'success';
            $api_response['statusCode'] = 200;
            $api_response['message'] = 'Suggestion Posted Successfully!';
            print_r(json_encode($api_response));
        }
        else
        {

            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500;
            $api_response['message'] = 'Server Error. ' . $conn->error;
            print_r(json_encode($api_response));
        }
        /*
         *  close database connection
         */
        $conn->close();
    }

    function updatePost($id)
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

        $update = "UPDATE suggestions SET  
                  posted_by='{$this->getPostedBy()}',
                  category='{$this->getCategory()}',
                  lgn='{$this->getLongitude()}', 
                  lat='{$this->getLatitude()}',
                  comment='{$this->getComment()}',
                  image_path='{$this->getImagePath()}'
                  WHERE id='{$id}'
                  ";

        /*
         * Execute the query
         */
        $query = $conn->query($update);

        if($query)
        {
            $api_response['status'] = 'success';
            $api_response['statusCode'] = 201;
            $api_response['message'] = 'Post updated successfully!';
            print_r(json_encode($api_response));

        }
        else
        {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500;
            $api_response['message'] = 'Error occurred while updating ['.$conn->error.']';
            print_r(json_encode($api_response));
        }


        /*
        *  close database connection
        */
        $conn->close();

    }

    function deletePost($id)
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
        $delete = "DELETE FROM suggestions WHERE id='{$id}'";

        /*
         * Execute delete query
         */

        $query = $conn->query($delete);

        if ($query)
        {
            $api_response['status'] = 'success';
            $api_response['statusCode'] = 204;
            $api_response['message'] = 'Post Deleted successfully!';
            print_r(json_encode($api_response));
        }
        else
        {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500;
            $api_response['message'] = 'Error occurred! Post not deleted ['.$conn->error.']';
            print_r(json_encode($api_response));
        }

        /*
        *  close database connection
        */
        $conn->close();
    }
    /*
     * Get all the suggestions posted
     */
    function getSuggestions()
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
         * Create a select query to fetch all posts from the
         * database
         */

        $select = "SELECT * FROM suggestions WHERE 1";
        /*
        * Query the database to get the posted suggestions
        */
        $query = $conn->query($select);
        /*
         * loop through the results to create a json array of response
         */
        if($query->num_rows > 0)
        {

            $json_array = array();

            while($row = $query->fetch_assoc())
            {
                $api_response['id'] = $row['id'];
                $api_response['posted_by'] = $row['posted_by'];
                $api_response['category'] = $row['category'];
                $api_response['comment'] = $row['comment'];
                $api_response['posted_on'] = $row['posted_on'];
                array_push($json_array, $api_response);
            }
            print_r(json_encode($json_array));
        }

        /*
        *  close database connection
        */
        $conn->close();
    }

}


/*
 * initialize the Suggestions class
 */

$suggestion = new Suggestion();

/*
 * Check if the data sent from the android
 * is Not empty other wise provide an error
 * message to the user to input data.
 * this is the server validation enforcement
 */

if(!empty($data)){

    /*
     * check the http request method used by the android
     * and provide the corresponding action
     */

    if ($request_method == 'POST')
    {
        /*
        * set the suggestion properties using the class setters methods
        *
        */

        $suggestion->setCategory($data['category']);
        $suggestion->setLongitude($data['lgn']);
        $suggestion->setLatitude($data['lat']);
        $suggestion->setComment($data['comment']);
        $suggestion->setImagePath($data['image_path']);
        $suggestion->setPostedBy($data['posted_by']);

        /*
         * Save post in the database
         */

        $suggestion->savePost();
    }

    elseif ($request_method == 'PUT')
    {
        /*
        * set the suggestion properties using the class setters methods
        *
        */

        $suggestion->setCategory($data['category']);
        $suggestion->setLongitude($data['lgn']);
        $suggestion->setLatitude($data['lat']);
        $suggestion->setComment($data['comment']);
        $suggestion->setImagePath($data['image_path']);
        $suggestion->setPostedBy($data['posted_by']);

        /*
         * Get the id of the post being updated
         */
        $id = $data['id'];

        /*
         * Save update in the database
         */

        $suggestion->updatePost($id);

    }

    elseif ($request_method == 'DELETE')
    {
        /*
         * Get the id of the post being deleted
         */
        $id = $data['id'];

        /*
         * Save update in the database
         */

        $suggestion->deletePost($id);
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
    $suggestion->getSuggestions();
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