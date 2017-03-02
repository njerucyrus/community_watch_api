<?php
/**
 * Created by PhpStorm.
 * User: hudutech
 * Date: 3/2/17
 * Time: 9:22 AM
 */
/*
 * import database connection file
 */
require 'connect.php';

/*
 * Get json data posted by the android device
 */

$data = json_decode(file_get_contents('php://input'), true);

/*
 * Declare request method to switch between the requests
 */

$request_method = $_SERVER['REQUEST_METHOD'];

/*
 * declare a global array to hold the api responses
 */
$api_response = array();

class PostCategory
{
    private $categoryCode;
    private $categoryName;


    /**
     * @return mixed
     */
    public function getCategoryCode()
    {
        return $this->categoryCode;
    }

    /**
     * @param mixed $categoryCode
     */
    public function setCategoryCode($categoryCode)
    {
        $this->categoryCode = $categoryCode;
    }

    /**
     * @return mixed
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @param mixed $categoryName
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
    }

    public function addCategory()
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

        $sql = "INSERT INTO post_categories(category_code, category_name)
                VALUES(
                '{$this->getCategoryCode()}',
                '{$this->getCategoryName()}'
                )
                ";

        /*
     * execute the sql to save the category
     * in the database
     */
        $query = $conn->query($sql);

        /*
         * Check if the execution of the sql statement
         * succeeded
         */
        if ($query) {
            $api_response['status'] = 'success';
            $api_response['statusCode'] = 200;
            $api_response['message'] = 'Category added successfully!';

            /*
             * print the json response
             */
            print_r(json_encode($api_response));

        } else {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500;
            $api_response['message'] = 'Error occurred! Category already exists';

            /*
             * print the json response
             */
            print_r(json_encode($api_response));

        }
    }

    public function updateCategory($categoryCode)
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

        $update = "UPDATE post_categories
                    SET 
                    category_name = '{$this->getCategoryName()}'
                    WHERE category_code ='{$categoryCode}'
                    ";

        /*
         * Execute the query now
         */

        $query = $conn->query($update);

        /*
         * Check if the query succeeded
         */
        if ($query) {
            $api_response['status'] = 'success';
            $api_response['statusCode'] = 201;
            $api_response['message'] = 'Category updated successfully!';

            /*
             * print the json response
             */
            print_r(json_encode($api_response));
        } else {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500;
            $api_response['message'] = 'Error  occurred while updating category! [' . $conn->error . ']';

            /*
             * print the json response
             */
            print_r(json_encode($api_response));
        }

    }

    public function deleteCategory($categoryCode){
        global $api_response;
        /*
         * Create an instance of Connection class
         */

        $connection = new Connection();

        /*
         * Create a connection to the database
         */

        $conn = $connection->getConnection();

        $delete = "DELETE FROM post_categories WHERE category_code ='{$categoryCode}'";

        /*
         * Execute delete query
         */
        $query = $conn->query($delete);

        if ($query) {
            $api_response['status'] = 'success';
            $api_response['statusCode'] = 204; /*status code 204 means no content*/

            $api_response['message'] = 'Category deleted successfully!';

            /*
             * print the json response
             */
            print_r(json_encode($api_response));

        } else {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 500; /*status code 204 means no content*/

            $api_response['message'] = 'Error occurred Category not deleted!';

            /*
             * print the json response
             */
            print_r(json_encode($api_response));
        }
    }

    public function getCategoryList()
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

        $select = "SELECT * FROM post_categories WHERE 1";

        /*
         * Query the database to get the registered categories
         */
        $query = $conn->query($select);
        /*
         * loop through the results to create a json array of response
         */

        $json_array = array();
        if ($query->num_rows > 0) {

            while ($row = $query->fetch_assoc()) {
                $api_response['category_code'] = $row['category_code'];
                $api_response['category_name'] = $row['category_name'];

                array_push($json_array, $api_response);
            }
            print_r(json_encode($json_array));
        }
        else
        {
            /*
             * print an empty array since categories found
             */
            $api_response['status'] = 'failed';
            $api_response['message'] = 'no data';
            print_r(json_encode($api_response));
        }

    }

}

/*
 * initialize the PostCategory class
 */

$post_category = new PostCategory();

/*
 * Check if the data was sent by the android device
 */

if(!empty($data)) {


    /*
     * Check the type of request method
     */

    if($request_method == 'POST')
    {

        /*
         * read values sent by the android device then
         * proceed to execute the query for checking.
         */

        $categoryCode = $data['category_code'];
        $categoryName = $data['category_name'];

        /*
         * Set the PostCategory class attributes using
         * the class setters methods
         */
        $post_category->setCategoryCode($categoryCode);
        $post_category->setCategoryName($categoryName);

        /*
         * add category to the database
         */
        $post_category->addCategory();
    }
    elseif ($request_method == 'PUT')
    {
        /*
        * read values sent by the android device then
        * proceed to execute the query for checking.
        */

        $categoryCode = $data['category_code'];
        $categoryName = $data['category_name'];

        /*
         * Set the PostCategory class attributes using
         * the class setters methods
         */
        $post_category->setCategoryCode($categoryCode);
        $post_category->setCategoryName($categoryName);

        /*
         * Update category to the database
         */
        $post_category->updateCategory($categoryCode);
    }

    elseif ($request_method == 'DELETE')
    {
        /*
        * read values sent by the android device then
        * proceed to execute the query for checking.
        */

        $categoryCode = $data['category_code'];
        $post_category->setCategoryCode($categoryCode);

        /*
         * Delete the category from the database
         */

        $post_category->deleteCategory($categoryCode);

    }

}
elseif(empty($data) && $request_method == 'GET')
{

    $post_category->getCategoryList();

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
