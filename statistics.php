<?php
/**
 * Created by PhpStorm.
 * User: hudutech
 * Date: 3/1/17
 * Time: 7:04 PM
 */

/*
 * import the database connection file
 */
require 'connect.php';

$api_response = array();

class Statistics
{
    public function getStatistics()
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

        $sql = "SELECT COUNT(id) as category_count, category FROM suggestions GROUP BY category";
        /*
         * Execute the query
         */
        $query = $conn->query($sql);
        /*community_watch@localhost
         * check if the query returns any row
         */
        if ($query) {

            /*
              * loop through the results to create a json array of response
              */

            if ($query->num_rows > 0) {

                $json_array = array();

                while ($row = $query->fetch_assoc()) {
                    $api_response['count'] = $row['category_count'];
                    $api_response['category'] = $row['category'];

                    array_push($json_array, $api_response);
                }
                print_r(json_encode($json_array));
            }

        } else {
            $api_response['status'] = 'failed';
            $api_response['statusCode'] = 404;
            $api_response['message'] = 'Error! Resource not found! [' . $conn->error . ']';
            print_r(json_encode($api_response));
        }
        /*
        * close database connection
        */
        $conn->close();

    }
}

/*
 * initialize the class
 */
$statistics = new Statistics();
/*
 * show statistics
 */
$statistics->getStatistics();