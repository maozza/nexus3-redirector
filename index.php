<?php
include './config.php';
$url = URL;
$input_params = $_REQUEST;

function get_maven_metadata($r = "", $g = "", $a = "", $v = "") {
    $group = str_replace('.', '/', $g);
    global $url;
    if ($v == NULL) {
        $meta_url = "$url/$r/$group/$a/maven-metadata.xml";
    } else {        
        $meta_url = "$url/$r/$group/$a/$v/maven-metadata.xml";
    }
    //print_r($meta_url);
    $maven_metadata = file_get_contents($meta_url);
    return $maven_metadata;
}

function parse_xml($xml_str) {
    $xml = simplexml_load_string($xml_str);
    if ($xml === false) {
        header("HTTP/1.1 500 Internal Server Error");
        die("Failed loading XML: ... ");
        foreach (libxml_get_errors() as $error) {
            echo "<br>", $error->message;
        }
    } else {
        return ($xml);
    }
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    return $length === 0 ||
            (substr($haystack, -$length) === $needle);
}

function is_snapshot($version) {
    return endsWith($version, '-SNAPSHOT');
}

function get_url($input_array) {    
    extract($input_array);    
    if (!(empty($c))){
        $c='-' . $c;
    }
    if (empty($e)){
        $e= 'jar';
    }    
    $group = str_replace('.', '/', $g);
    $maven_metadata = get_maven_metadata($r, $g, $a, NULL);
    $xml_obj = parse_xml($maven_metadata);
    global $url;
    //print_r($xml_obj);
    if (strtolower($v) == 'latest') {
        $v = $xml_obj->versioning->latest;        
    } elseif (strtolower($v) == 'release') {
        $v = $xml_obj->versioning->release;
    }
    if (is_snapshot($v)) {
        $maven_snapshot_metadata = get_maven_metadata($r, $g, $a, $v);        
        $xml_obj_snapshot = parse_xml($maven_snapshot_metadata);
        $timestamp = $xml_obj_snapshot->versioning->snapshot->timestamp;
        $buildNumber = $xml_obj_snapshot->versioning->snapshot->buildNumber;
        
        $link = "$url/$r/$group/$a/$v/$a-" . str_replace('-SNAPSHOT','', $v) . "-$timestamp-$buildNumber$c.$e";
    } else {
        $link = "$url/$r/$group/$a/$v/$a-$v$c.$e";
    }
    return $link;
}


function validate_input($input_params){
    $expected_input_params = array(
        'r',
        'g',
        'a',
        'v',
        //'c',
        //'e'
    );
    foreach ($expected_input_params as $value) {
        if (empty($input_params[$value])){
            return FALSE;
        }
    }
    return TRUE;
}

if ((empty($input_params)) || (!validate_input($input_params))){
    echo "Accepted values are:" .  'r = "Repository", g = "Group", a = "Artifact", v = "Version", c = "Classifier" e="Extention"';
    exit();
}



$link = get_url($input_params);
header("Location: ". $link);
