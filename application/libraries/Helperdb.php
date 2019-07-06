<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Helperdb {

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->database();
    }

    function __call($name, $arguments) {
        $funciones = array("add", "edit", "del", "get");
        $tabla = "";
        foreach ($funciones as $funcion) {
            if (preg_match("/" . $funcion . "/", $name) > 0) {
                $action = $funcion;
                $tabla = "tbl_" . strtolower(substr($name, strlen($funcion)));
                break;
            }
        }

        if ($tabla == "") {
            //error_log("Funcion " . $name . " no esta definida"); 
            return false;
        }



        $sql = "select column_name as columna,column_key AS isprimary from INFORMATION_SCHEMA.columns where table_name = '" . $tabla . "' and TABLE_SCHEMA = '" . $this->CI->db->database . "'"; //pendiente de esto


        $columnas = $this->CI->db->query($sql);


        foreach ($columnas->result_array() as $columna) {
            if ($columna["isprimary"] == "PRI")
                $pk = $columna["columna"];
            //else if(preg_match("/fk_estatus/",$columna["columna"]) > 0)
            //continue;
            else
                $fields[] = $columna["columna"];
        }



        //var_dump($arguments[0]);

        switch ($action) {
            case "add":
            case "edit":
                return $this->addGeneral($pk, $tabla, $fields, $arguments[0]);
                break;

            case "get":
                $arr = $arguments[0];
                $arr["tabla"] = $tabla;
                if (intval($arguments[0][$pk]) > 0)
                    $arr[$pk] = intval($arguments[0][$pk]);
                else { //parche
                    $arr = $arguments[0];
                    $arr["tabla"] = $tabla;
                    unset($arr[$pk]);
                }
                //var_dump($arr);
                if (intval($arguments[2]) == 0)
                    $arguments[2] = 999999;

                $data = $this->getTabla($arr, $pk, $arguments[2], intval($arguments[1]));
                return $data;
                break;

            case "del":
                $sql = "update " . $tabla . " set fk_estatus = 0 where " . $pk . " = " . $arguments[0];
                $this->CI->db->query($sql);
                break;
        }
    }

    function addGeneral($pk, $tabla, $fields, $post) {
        if (isset($post[$pk]) && intval($post[$pk]) > 0) {//es un edit realmente
            array_push($fields, $pk);
        } else {
            array_push($fields, "date_added");
            $post["date_added"] = date("Y/m/d H:i");
        }

        array_push($fields, "date_modified");
        $post["date_modified"] = date("Y/m/d H:i");

        $arrTemp = array("tabla" => $tabla);
        $arrTemp = array_merge($arrTemp, $this->clearSql_Array($fields, $post));

        $id = $this->addEdtTabla($arrTemp, $pk);
        if (intval($post[$pk]) > 0) {//es un edit realmente
            $id = $post[$pk];
        }

        return $id;
    }

    function addEdtTabla($columnas, $pk) {
        if ($columnas[$pk] == 0) {
            $sql = "insert into {$columnas["tabla"]} (";
            $sqlValues = "";
            foreach ($columnas as $key => $value) {
                if ($key != $pk && $key != "tabla") {
                    $sql .= "`$key`,";
                    $sqlValues .= "'$value',";
                }
            }
            $sql = substr($sql, 0, strlen($sql) - 1) . ") values (" . substr($sqlValues, 0, strlen($sqlValues) - 1) . ")";
        } else {
            $sql = "update {$columnas["tabla"]} set ";
            foreach ($columnas as $key => $value) {
                if ($key != $pk && $key != "tabla") {
                    $sql .= "`$key` = '$value',";
                }
            }
            $sql = substr($sql, 0, strlen($sql) - 1) . " where $pk = '{$columnas[$pk]}'";
        }

        //echo $sql . "<br><br>";
        //error_log($sql);
        $this->CI->db->query($sql);
        //error_log($this->CI->db->insert_id());
        return $this->CI->db->insert_id();
    }

    function getTabla($columnas, $pk, $results_per_page = 999999, $page = 0) {
        //var_dump($columnas);
        //$Admin = new Admin;
        //$is_admin=$Admin->isAdmin();

        $estatus = ($is_admin) ? "fk_estatus >= 1" : "fk_estatus=1";


        if (isset($columnas["fk_estatus"])) {
            if ($columnas["fk_estatus"] == "null")
                $estatus = "1 = 1";
            else
                $estatus = "fk_estatus = " . $columnas["fk_estatus"];
        }

        if (sizeof($columnas) <= 1 || (sizeof($columnas) <= 2 && isset($columnas["orderby"]))) {
            $sql = "select * from {$columnas["tabla"]} where {$estatus}";
        } else {
            $sql = "select * from {$columnas["tabla"]} where {$estatus}";
            foreach ($columnas as $key => $value) {
                if ($key != 'tabla' && $key != "orderby" && $key != "fk_estatus" && $key != "sql_like")
                    if (preg_match("/fk_/", $key) > 0 || preg_match("/pk_/", $key) > 0) {
                        $sql .= " and " . $this->clearSql_s($key) . "=" . $this->clearSql_n($value);
                    } else {
                        if (isset($columnas["sql_like"]))
                            $sql .= " and " . $this->clearSql_s($key) . " like '%" . $this->clearSql_s($value) . "%'";
                        else
                            $sql .= " and " . $this->clearSql_s($key) . "='" . $this->clearSql_s($value) . "'";
                    }
            }
        }

        if (trim($pk) != '' && !isset($columnas["orderby"]))
            $sql .= " order by $pk desc";
        elseif (isset($columnas["orderby"]))
            $sql .= " order by " . $columnas["orderby"];

        //error_log($sql);

        return $this->Execute($sql, $results_per_page, $page);
    }

    function ExecuteAlone($sql, $secuencia = '') {
        $this->CI->db->query($sql);
        if (preg_match("/^insert/", trim($this->last_sentence)) > 0) {
            return $this->CI->db->insert_id();
        }
    }

    function Execute($sql, $results_per_page = 999999, $page = 0, $secuencia = '') {


        if (preg_match("/^insert/", trim($sql)) > 0) {
            $this->CI->db->query($sql);
            return $this->CI->db->insert_id();
        } elseif (preg_match("/^update/", trim($sql)) > 0) {
            $this->CI->db->query($sql);
        } else {
            $indiceResult = 0;
            $array = null;

            $arreglo = array();
            $results_per_page = intval($results_per_page);
            $sql = trim($sql);

            $query = $sql;

            if ($secuencia == '') {
                if (preg_match('#GROUP\s+BY#is', $query) === 1 ||
                        preg_match('#SELECT.+SELECT#is', $query) === 1 ||
                        preg_match('#\sUNION\s#is', $query) === 1 ||
                        preg_match('#SELECT.+DISTINCT.+FROM#is', $query) === 1
                ) {
                    $this->CI->db->query($query);
                    $query = $this->CI->db->query("SELECT FOUND_ROWS() as total");
                    $data = $query->row(0);

                    $total_results = $data->total;
                    $query->free_result();
                } else {
                    // don't query the whole table, just get the number of rows
                    $query = preg_replace('#SELECT\s.+\sFROM#is', 'SELECT COUNT(*) as total FROM', $query);

                    $query = $this->CI->db->query($query);
                    $data = $query->row(0);
                    //print_r($query);
                    //$query = $this->CI->db->query("SELECT FOUND_ROWS() as total");
                    //$data = $query->row(0);
                    //print_r($data);
                    $total_results = $data->total;
                    $query->free_result();
                }
            }

            $page = intval($page);



            $limit_down = intval($page * $results_per_page);

            $num_pages = floor($total_results / $results_per_page);

            if (($total_results / $results_per_page) > $num_pages) {
                $num_pages = $num_pages + 1;
            }
            $sql .= " limit $results_per_page offset $limit_down";

            $data = $this->CI->db->query($sql);



            //error_log($sql);

            $arrReturn = array();
            $arrReturn["total"] = $total_results;
            $arrReturn["pages"] = $num_pages;
            $arrReturn["currpage"] = $page;
            $arrReturn["currresults"] = $data->num_rows();

            $arrtmp = array();
            if ($arrReturn["currresults"] != 0) {

                foreach ($data->result_array() as $row) {
                    $arrtmp[] = $row;
                }
            }
            $arrReturn["results"] = $arrtmp;
            return $arrReturn;
        }
    }

    /* evaluar bien esta funcion es posible que no funcione como queremos al intentar detectar el primary_key */

    function clearSql_Array($fields, $post) {
        foreach ($fields as $key => $value) {
            if (isset($post[$value])) {
                if (preg_match("/^fk_/", $value) > 0 || preg_match("/^id/", $value) > 0 || preg_match("/^pk_/", $value) > 0) { //si pinta ser un entero le paso el clear_N
                    $arrTemp[(string) $value] = $this->clearSql_n($post[$value]);
                } else {
                    $arrTemp[(string) $value] = $this->clearSql_s($post[$value]);
                }
            }
        }
        return $arrTemp;
    }

    function clearSql_s($cadena) {
        return trim(str_replace("'", "\'", str_replace("\'", "'", $cadena)));
    }

    function clearSql_f($cadena) {
        return $this->clearSql_s($cadena);
    }

    function clearSql_n($cadena) {
        if (is_numeric($cadena))
            return $cadena;
        else
            return 0;
    }

    function paginateResults($arrReturn, $next = '', $previous = '', $getVar = "page") {
        //global $HTTP_GET_VARS;
        $variablesget = "";
        $str_paginacion_prev = "";
        $str_paginacion_next = "";
        $next_page = 0;
        $prev_page = 0;

        foreach ($_GET as $key => $value) {
            if ($key != $getVar) {
                if (is_array($value)) {
                    foreach ($value as $key2 => $value2)
                        $variablesget .= $key . "[]=" . $value2 . "&";
                } else {
                    $variablesget .= "$key=$value&";
                }
            }
        }

        $next_page = $arrReturn["currpage"] + 1;
        if ($arrReturn["pages"] > ($arrReturn["currpage"] + 1)) {
            $css = "";
            $href = "?" . $getVar . "=$next_page&$variablesget";
        } else {
            $css = "disabled";
            $href = "#";
        }
        $str_paginacion_next = "<li class='next " . $css . "'><a href='" . $href . "'>$next &rarr;</a></li>";



        $prev_page = $arrReturn["currpage"] - 1;
        if ($arrReturn["currpage"] != 0) {
            $css = "";
            $href = "?" . $getVar . "=$prev_page&$variablesget";
        } else {
            $css = "disabled";
            $href = "#";
        }
        $str_paginacion_prev = "<li class='prev " . $css . "'><a href='" . $href . "'>&larr; $previous</a></li>";

        for ($a = $next_page + 1; $a <= $next_page + 3; $a++) {
            if ($a <= $arrReturn["pages"] && $arrReturn["currpage"] != ($arrReturn["pages"] - 1)) {
                //$str_paginacion_nextInd .= " <a href='?page=" . ($a-1) . "&$variablesget' class='" . $siguienteClass ."'>" . $a . "</a>"; 
                $str_paginacion_nextInd .= "<li class=''><a href='?" . $getVar . "=" . ($a - 1) . "&$variablesget'>" . $a . "</a></li>";
            }
        }
        for ($a = $prev_page - 1; $a <= $prev_page + 1; $a++) {
            if ($a > 0 && $arrReturn["currpage"] != 0) {
                //$str_paginacion_prevInd .= " <a href='?page=" . ($a-1) . "&$variablesget' class='" . $siguienteClass ."'>" . $a . "</a>"; 
                $str_paginacion_prevInd .= "<li class=''><a href='?" . $getVar . "=" . ($a - 1) . "&$variablesget'>" . $a . "</a></li>";
            }
        }

        $actual = "<li class='active'><a href='#'>" . intval($arrReturn["currpage"] + 1) . "</a></li>";


        if ($arrReturn["pages"] < 0) {
            $arrReturn["pages"] = 1;
        }

        $str_paginacion = "<div class='pagination pagination-centered'><ul>" . $str_paginacion_prev . $str_paginacion_prevInd . $actual . $str_paginacion_nextInd . $str_paginacion_next . "</ul></div>";
        return $str_paginacion;
    }

}
