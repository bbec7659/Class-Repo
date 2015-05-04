<?php

/**
 *
 * This is the batabase class that is used for all of the database interaction. 
 * It is a complete class that will handle all error handling, inserts, updates, deletes, and various types of selects.
 * 
 * 
 * @author  Brandon Beck <bbeck@bbeck.org>
 *
 * @since 1.0
 *
 */
class db {

    protected $db;

    /**
     *
     * This is the constructor for the databse class. 
     * It can either be used by passing in a database name or it can be used without and argument and connect to the default database.
     *  
     * 
     * @author  Brandon Beck <bbeck@bbeck.org>
     *
     * @since 1.0
     *
     * @param string $db This is the database name that can be passed in if a different database besides the default database is needed.
     */
    public function __construct($db = null) {
        if ($db == null) {
            $db = 'default';
        }
        $args = array('blah blah.com'); //change to url for production
        $options = array(PDO::ATTR_PERSISTENT => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false);

        try {
            if (in_array($_SERVER['HTTP_HOST'], $args)) {
                $this->db = new PDO('mysql:host=localhost;dbname=' . $db, 'root', 'FAV73', $options);
            } else {
                $this->db = new PDO('mysql:host=localhost;dbname=' . $db, 'root', 'FAV73', $options);
            }
        } catch (PDOException $e) {
            $this->dberror($e);
        }
    }


    /**
     *
     * This is method handles inserts, updates, and deletes and returns the affected rows id.
     *  
     * 
     * @author  Brandon Beck <bbeck@bbeck.org>
     *
     * @since 1.0
     * @param string $sql This is the sql argument that is in the format of a parameterized statement or not. This method will handle all of the inserts, updates, and deletes but not the selects. This   
     * @param array $params This is a non zero based array that has all of the arguments for the prepared statement passed in the $sql variable.
     * @param string $db This is the database name that can be passed in if a different database besides the default database is needed.
     * @return mixed If an insert or update is run this method will return ther id that was inserted or updated. 
     */
    public function execaction($sql, $params = '', $db = null) {
        try {
            if ($db != null) {
                $this->db = $db;
            }
            $query = $this->db->prepare($sql);
            if ($params != '') {
                //the value must be by reference in order to work
                foreach ($params as $key => &$value) {
                    //http://stackoverflow.com/questions/1391777/how-do-i-insert-null-values-using-pdo
                    if ($value == NULL) {
                        $query->bindValue($key, NULL, PDO::PARAM_NULL);
                    } else {
                        $query->bindValue($key, $value);
                    }
                }
            }
            $query->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->dberror($e, $sql, $params);
        }
    }

    /**
     *
     * This method selects one column from one row and returns value.
     *  
     * 
     * @author  Brandon Beck <bbeck@bbeck.org>
     *
     * @since 1.0
     * @param string $sql This is the sql argument that is in the format of a parameterized statement or not. This method will handle the selection of one field, one row from the database and will return the value of it.    
     * @param array $params This is a non zero based array that has all of the arguments for the prepared statement passed in the $sql variable.
     * @param string $db This is the database name that can be passed in if a different database besides the default database is needed.
     * @return string this will return the value that is selected from the database. 
     */
    public function getone($sql, $params = '', $db = null) {
        try {
            if ($db != null) {
                $this->db = $db;
            }
            $query = $this->db->prepare($sql);
            if ($params != '') {
                //the value must be by reference in order to work
                foreach ($params as $key => &$value) {
                    $query->bindValue($key, $value);
                }
            }

            $query->execute();
            $res = $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->dberror($e, $sql, $params);
        }
        return $res;
    }

    /**
     *
     * This method selects all and returns a multidimensional array.
     *  
     * 
     * @author  Brandon Beck <bbeck@bbeck.org>
     *
     * @since 1.0
     * @param string $sql This is the sql argument that is in the format of a parameterized statement or not. This method will handle the selection of a select * or any number of fields.    
     * @param array $params This is a non zero based array that has all of the arguments for the prepared statement passed in the $sql variable.
     * @param string $db This is the database name that can be passed in if a different database besides the default database is needed.
     * @return array This will return the multidemensional array that is selected from the database. 
     */
    public function getall($sql, $params = '', $db = null) {
        try {
            if ($db != null) {
                $this->db = $db;
            }
            $query = $this->db->prepare($sql);

            if ($params != '') {
                //the value must be by reference in order to work
                foreach ($params as $key => &$value) {
                    //http://stackoverflow.com/questions/2269840/php-pdo-bindvalue-in-limit
                    if ($key == 'LIMIT' || $key == 'OFFSET') {
                        $query->bindValue($key, intval(trim($value)), PDO::PARAM_INT);
                    } else {
                        $query->bindValue($key, $value);
                    }
                }
            }
            $query->execute();
            $res = $query->fetchAll(PDO::FETCH_ASSOC);

            return $res;
        } catch (PDOException $e) {
            $this->dberror($e, $sql, $params);
        }
    }

    /**
     *
     * This method is responsible for selecting a single row from database and returning a one deminsional array.
     *  
     * 
     * @author  Brandon Beck <bbeck@bbeck.org>
     *
     * @since 1.0
     * @param string $sql This is the sql argument that is in the format of a parameterized statement or not. This method will handle the selection of a select * or any number of fields and a limit 1 from the database.    
     * @param array $params This is a non zero based array that has all of the arguments for the prepared statement passed in the $sql variable.
     * @param string $db This is the database name that can be passed in if a different database besides the default database is needed.
     * @return array This will return the one demensional array that is selected from the database. 
     */
    public function getrow($sql, $params = '', $db = null) {
        try {
            if ($db != null) {
                $this->db = $db;
            }
            $query = $this->db->prepare($sql);

            if ($params != '') {
                //the value must be by reference in order to work
                foreach ($params as $key => &$value) {
                    $query->bindValue($key, $value);
                }
            }

            $query->execute();
            $res = $query->fetch(PDO::FETCH_ASSOC);
            return $res;
        } catch (PDOException $e) {
            $this->dberror($e, $sql, $params);
        }
    }

    /**
     *
     * This method handles all of the db errors and notifies admin of the errors.
     *  
     * 
     * @author  Brandon Beck <bbeck@bbeck.org>
     *
     * @since 1.0
     * @param string $e This is the exception that is the PDO exception variable that is caught by one of the above methods throwing an exception.
     * @param string $sql This is the sql argument that is in the format of a parameterized statement or not. This method will handle the selection of a select * or any number of fields and a limit 1 from the database.    
     * @param array $params This is a non zero based array that has all of the arguments for the prepared statement passed in the $sql variable.
     */
    public function dberror($e, $sql = "", $params = null) {

        if (strpos($_SERVER['HTTP_HOST'], 'test server')) {// if this is test server echo error to screen
            echo $e->getMessage() . $sql;
            print_r($e . $sql);
            syslog(LOG_ERR, $e);
        } else {
            //$u = new works();
            $message = $e . date('m/d/Y H:i') . $sql . "\r\n\r\n"; //. $u->getUserName(); can put username on end of this when in production
            // In case any of our lines are larger than 70 characters, we should use wordwrap()
            $message = wordwrap($message, 70, "\r\n");
            mail('webadmin@fordav.com', 'Database Error', $message);//send error to admin
            exit();
        }
    }

}
