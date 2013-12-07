<?php
final class DateAndTime {
    protected static $years = array("1950", "1951", "1952", "1953", "1954", "1955", "1956", "1957", "1958", "1959", "1960", "1961", "1962", "1963", "1964",
        "1965", "1966", "1967", "1968", "1969", "1970", "1971", "1972", "1973", "1974", "1975", "1976", "1977", "1978", "1979", "1980", "1981", "1982",
        "1983", "1984", "1985", "1986", "1987", "1988", "1989", "1990", "1991", "1992", "1993", "1994", "1995", "1996", "1997", "1998", "1999", "2000",
        "2001", "2002", "2003", "2004", "2005", "2006", "2007", "2008", "2009", "2010", "2011", "2012", "22013", "2014");

    protected static $months = array("01"=>"Jan", "02"=>"Feb", "03"=>"Mar", "04"=>"Apr", "05"=>"May", "06"=>"Jun", "07"=>"Jul", "08"=>"Aug",
        "09"=>"Sep", "10"=>"Oct", "11"=>"Nov", "12"=>"Dec");

    protected static $days = array("01"=> "1", "02"=> "2", "03"=> "3", "04"=> "4", "05"=> "5", "06"=> "6", "07"=> "7", "08"=> "8", "09"=> "9",
        "10"=> "10", "11"=> "11", "12"=> "12", "13"=> "13", "14"=> "14", "15"=> "15", "16"=> "16", "17"=> "17",
        "18"=> "18", "19"=> "19", "20"=> "20", "21"=> "21", "22"=> "22", "23"=> "23", "24"=> "24", "25"=> "25",
        "26"=> "26", "27"=> "27", "28"=> "28", "29"=> "29", "30"=> "30", "31"=> "31");

    protected static $hours = array("00"=> "12AM", "01"=> "1AM", "02"=> "2AM", "03"=> "3AM", "04"=> "4AM", "05"=> "5AM", "06"=> "6AM",
        "07"=> "7AM", "08"=> "8AM", "09"=> "9AM", "10"=> "10AM", "11"=> "11AM", "12"=> "12PM", "13"=> "1PM",
        "14"=> "2PM", "15"=> "3PM", "16"=> "4PM", "17"=> "5PM", "18"=> "6PM", "19"=> "7PM", "20"=> "8PM",
        "21"=> "9PM", "22"=> "10PM", "23"=> "11PM");

    protected static $mins = array("00"=> "0", "01"=> "1", "02"=> "2", "03"=> "3", "04"=> "4", "05"=> "5", "06"=> "6", "07"=> "7", "08"=> "8",
        "09"=> "9", "10"=> "10", "11"=> "11", "12"=> "12", "13"=> "13", "14"=> "14", "15"=> "15", "16"=> "16", "17"=> "17",
        "18"=> "18", "19"=> "19", "20"=> "20", "21"=> "21", "22"=> "22", "23"=> "23", "24"=> "24", "25"=> "25", "26"=> "26",
        "27"=> "27", "28"=> "28", "29"=> "29", "30"=> "30", "31"=> "31", "32"=> "32", "33"=> "33", "34"=> "34", "35"=> "35",
        "36"=> "36", "37"=> "37", "38"=> "38", "39"=> "39", "40"=> "40", "41"=> "41", "42"=> "42", "43"=> "43", "44"=> "44",
        "45"=> "45", "46"=> "46", "47"=> "47", "48"=> "48", "49"=> "49", "50"=> "50", "51"=> "51", "52"=> "52", "53"=> "53",
        "54"=> "54", "55"=> "55", "56"=> "56", "57"=> "57", "58"=> "58", "59"=> "59" );
    const YEAR = "year";
    const MONTH = "month";
    const DAY = "day";
    const HOUR = "hour";
    const MINS = "min";

    private function __construct(){}

    static public function getData($type){
        switch($type){
            case "year": return self::$years;
            case "month": return self::$months;
            case "day": return self::$days;
            case "hour": return self::$hours;
            case "min": return self::$mins;
            default: return "wrong data type";
        }
    }

    static public function getDayAsSelect($type="day", $input_value, $attr=""){
        $select = '<select name="' . $type . '" id="' . $type . '" ' . $attr . '>' . PHP_EOL;
        $select .= '<option value="" selected="selected">Select Day</option>' . PHP_EOL;
        foreach(self::$days as $key=>$value){
            $select .= '<option value="' . $key . '"';
            $select .= ($key == $input_value)? ' selected="selected"' : "";
            $select .= '>' . $value . '</option>' . PHP_EOL;
        }
        $select .= '</select>';
        return $select;
    }

    static public function getMonthAsSelect($type="month", $input_value, $attr=""){
        $select = '<select name="' . $type . '" id="' . $type . '" ' . $attr . '>' . PHP_EOL;
        $select .= '<option value="" selected="selected">Select Month</option>' . PHP_EOL;
        foreach(self::$months as $key=>$value){
            $select .= '<option value="' . $key . '"';
            $select .= ($key == $input_value)? ' selected="selected"' : "";
            $select .= '>' . $value . '</option>' . PHP_EOL;
        }
        $select .= '</select>';
        return $select;
    }

    static public function getYearAsSelect($year_limit="2000", $type="year", $input_value, $attr=""){
        $select = '<select name="' . $type . '" id="' . $type . '" ' . $attr . '>' . PHP_EOL;
        $select .= '<option value="" selected="selected">Select Year</option>' . PHP_EOL;
        $year_array = array_reverse(self::$years);
        foreach($year_array as $value){
            if ($value > $year_limit)
                continue;
            $select .= '<option value="' . $value . '"';
            $select .= ($value == $input_value)? ' selected="selected"' : "";
            $select .= '>' . $value . '</option>' . PHP_EOL;
        }
        $select .= '</select>';
        return $select;
    }
}