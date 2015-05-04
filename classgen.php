<?php

/**
 *
 * This is a class generation class. It will produce all of the getters, setters and all of the accomanying select, insert, and update functions needed for a table. 
 * 
 * 
 * @author  Brandon Beck <bbeck@bbeck.org>
 *
 * @since 1.0
 *
 */
class gen {

    /**
     * Class properties to generate the setter and getter functions
     */
    private $tablename = 'timesheet';
    private $id, $start, $stop, $workorder, $costcode, $warranty, $description;

    public function __construct() {
// Constructor
    }

    /**
     * ADD THE FUNCTIONS BELOW INTO YOUR CLASS. BE SURE TO DELETE AFTER USE OR WHEN YOUR CODE GOES LIVE
     */

    /**
     * Generate setter functions
     */
    public function generate_setter_functions() {
        $class_vars = get_class_vars(get_class($this));

        foreach ($class_vars as $name => $value) {
            if ($name != 'tablename') {
                self::debug('public function set' . $name . '( $x ){
                    $this->x[\'' . $name . '\'] = $this->ui->cleaninput($x);
              }');
            }
        }
    }

    /**
     * Generate getter functions
     */
    public function generate_getter_functions() {
        $class_vars = get_class_vars(get_class($this));

        foreach ($class_vars as $name => $value) {
            if ($name != 'tablename') {
                self::debug('public function get' . $name . '(){
                    return $this->ui->cleanoutput($this->x[\'' . $name . '\']);
                }');
            }
        }
    }

    public function generate_sql_functions() {
        self::debug('
                class ' . $this->tablename . ' {

    private $multi = array();
    protected $ui = null;

    public function __construct($id = null, $summary = null) {
        $this->ui = new UserInterface();
        $this->d = new db(\'apps\');

        if ($summary != null) {
            if (!empty($id) && is_array($id)) {
                $ids = \'\';
                $i = 0;
                if (!empty($id) && is_array($id)) {
                    foreach ($id as $r) {
                        $i++;
                        $ids .= $r;
                        if ($i < sizeof($id)) {
                            $ids .= ", ";
                        }
                    }
                } else {
                    $ids = $id;
                }

                $sql =\'SELECT * FROM ' . $this->tablename . ' WHERE id in (\' . $ids . \')\';
                $i = 0;
                foreach ($this->d->getall($sql) as $row) {
                ');
        $class_vars = get_class_vars(get_class($this));
        $i = 0;
        foreach ($class_vars as $name => $value) {
            if ($name != 'tablename') {
                self::debug('
$this->set' . $name . '($row[\'' . $name . '\']);
    $this->multi[$i][\'' . $name . '\'] = $this->get' . $name . '();');
                $i++;
            }
        }
        self::debug(' $i++;
                }
            }
        } else {
            $sql = \'SELECT * FROM ' . $this->tablename . ' WHERE id=?\';
            foreach ($this->d->getall($sql, array(1 => $id)) as $row) {
');
        foreach ($class_vars as $name => $value) {
            if ($name != 'tablename') {
                self::debug('$this->set' . $name . '($row[\'' . $name . '\']);
                    $this->multi[\'' . $name . '\'] = $this->get' . $name . '();');
            }
        }

        self::debug(' }
        }
    }
    
 public function getarraysize() {
        return count($this->multi);
    }

    public function getmulti() {
        return $this->multi;
    }

    public function getjsonmulti() {
        return json_encode($this->multi);
    }
');
    }

    /**
     * Print with a twist!
     */
    public static function debug($o) {
        echo '<pre>' . print_r($o, 1) . '</pre>';
    }

    public static function debugwolines($o) {
        echo print_r($o, 1);
    }

    /**
     * END OF GENERATOR FUNCTIONS!
     */
    public function generate_update_function() {
        $class_vars = get_class_vars(get_class($this));

        self::debugwolines(' public function update() {<br>
                                    if ($this->getid() == "") {<br>
                                        $sql = "INSERT INTO ' . $this->tablename . '( ');
        $z = 0;
        foreach ($class_vars as $name => $value) {
            if ($name != 'tablename') {
                self::debugwolines($name);
                if ($z < count($class_vars) - 2) {
                    self::debugwolines(', ');
                }
                $z++;
            }
        }
        self::debugwolines(') VALUES(');
        $z = 0;
        foreach ($class_vars as $name => $value) {
            if ($name != 'tablename') {
                self::debugwolines('?');
                if ($z < count($class_vars) - 2) {
                    self::debugwolines(', ');
                }
                $z++;
            }
        }
        self::debugwolines(')";');
        self::debugwolines('<br>$id = $this->d->execaction($sql, array(');
        $i = 1;
        $z = 0;
        foreach ($class_vars as $name => $value) {
            if ($name != 'tablename') {
                self::debugwolines($i . '=>$this->x["' . $name . '"]');
                $i++;
                if ($z < count($class_vars) - 2) {
                    self::debugwolines(', ');
                }
                $z++;
            }
        }
        self::debugwolines(')); <br>$this->setid($id);<br>
            return $this->getid();<br>
        } else {<br>
            $sql = "UPDATE ' . $this->tablename . ' SET '
        );
        $z = 0;
        foreach ($class_vars as $name => $value) {
            if ($name != 'tablename') {
                self::debugwolines($name . '=?');
                if ($z < count($class_vars) - 2) {
                    self::debugwolines(', ');
                }
                $z++;
            }
        }
        self::debugwolines(' WHERE id = ? ";
        <br> $this->d->execaction($sql, array(');

        $i = 1;
        $z = 0;
        foreach ($class_vars as $name => $value) {
            if ($name != 'tablename') {
                self::debugwolines($i . '=>$this->x["' . $name . '"]');
                $i++;
                if ($z < count($class_vars) - 2) {
                    self::debugwolines(', ');
                }
                $z++;
            }
        }

        self::debugwolines('));<br> 
        return $this->getid();<br>
        }<br>
    }
');
    }

    public function generate_delete_function() {

        self::debug('public function deletetimesheet() { 
                 $sql = \'DELETE FROM $this->tablename WHERE id = ?\';
        $this->d->execaction($sql, array(1 => $this->x[\'id\']));
        
    } <br>}');
    }

}

/**
 * USAGE
 */
$x = new gen();
$x->generate_sql_functions();
$x->generate_setter_functions();
$x->generate_getter_functions();
$x->generate_update_function();
$x->generate_delete_function();

