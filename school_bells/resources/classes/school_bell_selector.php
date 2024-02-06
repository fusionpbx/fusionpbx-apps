<?php
class school_bell_selector {

    private $min, $hou, $day;

    function __construct($time12h = False) {

        # Fill min
        $this->min = array();
        $this->min[-1] = "*";
        for ($i = 0; $i <= 59; $i++) {
            $this->min[$i] = sprintf("%1$02d", $i);
        }
        
        # Fill hou
        $this->hou = array();
        $this->hou[-1] = "*";
        if ($time12h) {
            $this->hou[0] = "12 AM";
            for ($i = 1; $i <= 12; $i++) {
                $this->hou[$i] = sprintf("%1$02d", $i) . " AM";
            }
            for  ($i = 13; $i <= 23; $i++) {
                $this->hou[$i] = sprintf("%1$02d", $i - 12) . " PM";
            }
        } else {
            for ($i = 0; $i <= 23; $i++) {
                $this->hou[$i] = sprintf("%1$02d", $i);
            }
        }

        $this->day = array();
        $this->day[-1] = "*";
        for ($i = 1; $i <= 31; $i++) {
            $this->day[$i] = sprintf("%1$02d", $i);
        }

        # Fill dow
        if ($time12h) {
            $this->dow = array(
                -1  => '*',
                0   => 'Sunday',
                1   => 'Monday',
                2   => 'Tuesday',
                3   => 'Wednesday',
                4   => 'Thursday',
                5   => 'Friday',
                6   => 'Saturday'
            );
        } else {
            $this->dow = array(
                -1  => '*',
                1   => 'Monday',
                2   => 'Tuesday',
                3   => 'Wednesday',
                4   => 'Thursday',
                5   => 'Friday',
                6   => 'Saturday',
                0   => 'Sunday'
            );
        }

        # Fill month
        $this->mon = array(
            -1  => '*',
            1   => 'January',
            2   => 'February',
            3   => 'March',
            4   => 'April',
            5   => 'May',
            6   => 'June',
            7   => 'July',
            8   => 'August',
            9   => 'September',
            10  => 'October',
            11  => 'November',
            12  => 'December'
        );
    }

    private function _option_string($option_item, $draw_item, $selected_item) {

        if ($option_item == $selected_item) {
            return "<option selected value='" . $option_item . "'>" . $draw_item . "</option>\n";
        }
        return "<option value='" . $option_item . "'>" . $draw_item . "</option>\n";
    }

    private function _draw_selected($selector_name ,$range_array, $selected) {
        $selector_text = "<select name = '$selector_name' id = '$selector_name' class='formfld'>\n";

        foreach ($range_array as $option_item => $draw_item) {
            $selector_text .= $this->_option_string($option_item, $draw_item, $selected);
        }
        $selector_text .= "</select>";
        return $selector_text;
    }

    public function draw_min($name, $selected = '') {
        return $this->_draw_selected($name, $this->min, $selected);
    }

    public function draw_hou($name, $selected = '') {
        return $this->_draw_selected($name, $this->hou, $selected);
    }

    public function draw_dom($name, $selected = '') {
        return $this->_draw_selected($name ,$this->day, $selected);
    }

    public function draw_mon($name, $selected = '') {
        return $this->_draw_selected($name ,$this->mon, $selected);
    }

    public function draw_dow($name, $selected = '') {
        return $this->_draw_selected($name, $this->dow, $selected);
    }
}
?>