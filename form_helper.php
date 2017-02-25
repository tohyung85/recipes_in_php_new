<?php
namespace GeneralClasses;

class FormHelper {
  protected $values = [];

  public function __construct($values=[]) {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->values = $_POST;
    } else {
      $this->values = $values;
    }
  }

  public function input($type, $attributes=[], $isMultiple=false) {
    $attributes['type'] = $type;
    if($type == 'radio' || $type == 'checkbox') {
      $name = isset($attributes['name']) ? $attributes['name'] : null;
      $value = isset($attributes['value']) ? $attributes['value'] : null;
      if($this->isOptionSelected($name, $value)) {
        $attributes['checked'] = true;
      }
    }
    return $this->tag('input', $attributes, $isMultiple);
  }

  public function select($options, $attributes=[]) {
    $multiple = isset($attributes['multiple']) ? $attributes['multiple'] : false;
    $name_attribute = isset($attributes['name']) ? $attributes['name'] : null;
    return
      $this->start('select', $attributes, $multiple) . 
      $this->options($name_attribute, $options) . 
      $this->end('select');
  }

  public function textarea($attributes=[]) {
    $name = isset($attributes['name']) ? $attributes['name'] : null;
    $value = $this->values['name'];
    return $this->start('textarea', $attributes) .
            htmlentities($value) . 
            $this->end('textarea');
  }

  public function tag($tag, $attributes=[], $isMultiple=false) {
    return "<$tag {$this->attributes($attributes, $isMultiple)} />";
  }

  public function start($tag, $attributes=[], $isMultiple=false) {
    // <select> and <textarea> tags ave no value attributes
    $valueAttribute = !($tag=='select' || $tag=='textarea');
    $attrs = $this->attributes($attributes, $isMultiple, $valueAttribute);
    return "<$tag $attrs>";
  }

  public function end($tag) {
    return "</$tag>";
  }

  protected function attributes($attributes, $isMultiple, $valueAttribute=true) {
    $tmp = [];
    $vals = $this->values;
    // If this tag could include a value attribute and it has a name and there's an
    // entry for the name in the values array, then set a value attribute
    if($valueAttribute && isset($attributes['name']) && array_key_exists($attributes['name'], $this->values)) {
      $attributes['value'] = $this->values[$attributes['name']];
    }

    foreach($attributes as $k => $v) {
      // true boolean means boolean attribute
      if(is_bool($v)) {
        if($v) $tmp[] = $this->encode($k);
      } else {
        $value = $this->encode($v);
        // If this is an element that might have multiple values.
        // tack[] to it's name
        if($isMultiple && $k == 'name') {
          $value .= '[]';
        }
        $tmp[] = "$k=\"$value\"";
      }
    }

    return implode(' ', $tmp);
  }

  protected function options($name, $options) {
    $tmp = [];
    foreach($options as $k=>$v) {
      $s = "<option value=\"{$this->encode($k)}\"";
      if($this->isOptionSelected($name, $k)) {
        $s .= ' selected';
      }
      $s .= ">{$this->encode($v)}</option>";
      $tmp[] = $s;
    }
    return implode(' ', $tmp);
  }

  protected function isOptionSelected($name, $value) {
    // If there's no entry for $name in the values array, option cannot be selected
    if(!isset($this->values[$name])) {
      return false;
    } else if (is_array($this->values[$name])){ // If the entry for $name in the values array is itself an array, check if $value is in that array
      return in_array($value, $this->values[$name]);
    } else { // Otherwise, compare $value to the entry for $name in the values array
      return $value == $this->values[$name];
    }    
  }

  public function encode($s) {
    return htmlentities($s);
  }    
}

class HTMLFunctions {
  public static function add_header($title = 'Recipes') {
    echo '<html>
          <head>
            <title>'.$title.'</title>
            <meta name="viewport" content="initial-scale=1">  
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
            <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
            <script type="text/javascript" src="recipe.js"></script>
          </head>
          <body>';
  }

  public static function add_footer() {
    echo '</body>
          </html>';
  }

  public static function select_rows_with_id($dbc, $id) {
    $query = $dbc->prepare('SELECT * FROM recipesteps WHERE id=?');
    $query->bind_param('d', $id);
    $query->execute();
    $results=$query->get_result();
    $row = $results->fetch_assoc();

    return $row;
  }
}


?>