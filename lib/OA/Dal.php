<?php

/*
+---------------------------------------------------------------------------+
| Max Media Manager v0.3                                                    |
| =================                                                         |
|                                                                           |
| Copyright (c) 2003-2006 m3 Media Services Limited                         |
| For contact details, see: http://www.m3.net/                              |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

require_once MAX_PATH . '/lib/OA/DB.php';
require_once 'DB/DataObject.php';

/**
 * The common Data Abstraction Layer (DAL) class.
 *
 * @package    OpenadsDal
 * @author     Radek Maciaszek <radek.maciaszek@openads.org>
 */
class OA_Dal
{

    /**
     * A factory method to obtain the appropriate DB_DataObject for a given
     * table name.
     *
     * @static
     * @param  string $table The name of the table for which a DB_DataObject is required.
     * @return DB_DataObjectCommon The appropriate DB_DataObjectCommon implementaion,
     *                             or false on error.
     */
    function factoryDO($table)
    {
        OA_Dal::_setupDataObjectOptions();
        $do = DB_DataObject::factory($table);
        if (is_a($do, 'DB_DataObjectCommon')) {
            $do->init();
            return $do;
        }
        return false;
    }

    /**
     * A method to obtain an appropriate DB_DataObject for a given table name, pre-loaded
     * with the desired data, when possible.
     *
     * Example use:
     *   $doBanners = OA_Dal::staticGetDO("banners", 12);
     * Return the oject pre-loaded with the banner ID 12.
     *
     * Example use:
     *   $doClients = OA_Dal::staticGetDO("clients", "name", "fred");
     * Return the object pre-loaded with all clients where the "name" column is
     * equal to "fred".
     *
     * @static
     * @param string $table The name of the table for which a DB_DataObject is required.
     * @param string $k     Either the column name, if $v is supplied, otherwise the
     *                      value of the table's primary key.
     * @param string $v     An optional value when $k is a column name of the table.
     * @return DB_DataObjectCommon The appropriate DB_DataObjectCommon implementaion,
     *                             or false on error.
     */
    function staticGetDO($table, $k, $v = null)
    {
        OA_DAL::_setupDataObjectOptions();
        $do = OA_Dal::factoryDO($table);
        if (PEAR::isError($do)) {
            return false;
        }
        if (!$do->get($k, $v)) {
            return false;
        }
        return $do;
    }

    /**
     * A factory method to load the appropriate MAX_Dal_Admin class for a
     * given table name.
     *
     * @static
     * @param string $table The name of the table for which a MAX_Dal_Admin class is
     *                      required.
     * @return MAX_Dal_Common The appropriate MAX_Dal_Common implementaion,
     *                        or false on error.
     */
    function factoryDAL($table)
    {
        include_once MAX_PATH . '/lib/max/Dal/Common.php';
        return MAX_Dal_Common::factory($table);
    }

    /**
     * Set up the required DB_DataObject options.
     *
     * @static
     * @access private
     */
    function _setupDataObjectOptions()
    {
        static $needsSetup;
        if (isset($needsSetup)) {
            return;
        }
        $needsSetup = false;

        // Set DB_DataObject options
        $MAX_ENT_DIR = MAX_PATH . '/lib/max/Dal/DataObjects';
        $options = &PEAR::getStaticProperty('DB_DataObject', 'options');
        $options = array(
            'database'              => OA_DB::getDsn(),
            'schema_location'       => $MAX_ENT_DIR,
            'class_location'        => $MAX_ENT_DIR,
            'require_prefix'        => $MAX_ENT_DIR . '/',
            'class_prefix'          => 'DataObjects_',
            'debug'                 => 0,
            'production'            => 0,
        );
    }

    /**
     * A method to return the SQL required to obtain an INTERVAL
     * value, depending on the datbase type in use.
     *
     * For example, in MySQL:
     *  INTERVAL 30 DAY;
     *
     * For example, in PostgreSQL:
     *  (30 DAY)::interval
     *
     * @static
     * @param string $interval The INTERVAL field or integer value. For example,
     *                         "30", or "table.column".
     * @param string $type     The INTERVAL length. For example, "DAY".
     * @return string The SQL code required to obtain the INTERVAL value.
     */
    function quoteInterval($interval, $type)
    {
        $oDbh = &OA_DB::singleton();
        if ($oDbh->dsn['phptype'] == 'pgsql') {
            return "($interval || ' $type')::interval";
        }
        return "INTERVAL $interval $type";
    }

    
    /**
     * Returns a valid SQL-formatted date for a current database.
     * Examples:
     * sqlDate(true, 2007, 2, 3) returns '2007-02-03'.
     * sqlDate(false, 2007, 2, 3) returns $dbh->noDateValue.
     * sqlDate(true, 2007, '-', 3) returns $dbh->noDateValue.
     *
     * @param boolean $validDate If true, the function will try to generate
     * a valid date. Otherwise, it will ignore other arguments and just return
     * an 'empty' date for this database.
     * @param integer $year
     * @param integer $month Month number from 1 to 12
     * @param integer $day Day number from 1 to 28/29/30/31
     * @return string If $validDate is true and all other parameters are valid
     * integers, constructs a proper sql date string. If any of the $year,
     * $month, $day is '-' or $validDate is false, returns a valid 'empty'
     * date string for the database.
     */
    function sqlDate($validDate, $year, $month, $day)
    {
        if (!$validDate || $year == '-' || $month == '-' || $day == '-') {
            $dbh = OA_DB::singleton();
            return $dbh->noDateValue;
        }
        $month = OA_Dal::to2digitFormat($month);
        $day = OA_Dal::to2digitFormat($day);
        return "$year-$month-$day";
    }
    
    
    /**
     * If the number is less < 10 returns the number prefixed with '0',
     * for example '02' for '2'. Otherwise, returns number as it is.
     *
     * @param integer $value
     * @return string
     */
    function to2digitFormat($value)
    {
        if ($value < 10) {
            return "0$value";
        }
        return $value;
    }

    
    /**
     * Returns true if $sqlDate is not an 'empty' date, false otherwise.
     *
     * @param string $sqlDate
     */
    function isValidDate($sqlDate)
    {
        $dbh = OA_DB::singleton();
        return preg_match('#\d\d\d\d-\d\d-\d\d#', $sqlDate) && $sqlDate != $dbh->noDateValue;
    }
    
    
    /**
     * Returns true if the $sqlDate represents 'empty' Openads date,
     * false otherwise.
     *
     * @param string $sqlDate
     */
    function isNullDate($sqlDate)
    {
        return !OA_Dal::isValidDate($sqlDate);
    }
    
    
}

?>
