<?php
namespace Dogpatch;

////
// Via: http://www.php.net/manual/en/function.http-parse-headers.php#112986
///
function http_parse_headers($raw_headers) {
    if (function_exists('http_parse_headers')) {
        return \http_parse_headers($raw_headers);
    }

    $headers = array();
    $key = '';

    foreach (explode("\n", $raw_headers) as $i => $h) {
        $h = explode(':', $h, 2);

        if (isset($h[1])) {
            if (!isset($headers[$h[0]])) {
                $headers[$h[0]] = trim($h[1]);
            } elseif (is_array($headers[$h[0]])) {
                $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
            } else {
                $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
            }

            $key = $h[0];
        } else {
            if (substr($h[0], 0, 1) == "\t") {
                $headers[$key] .= "\r\n\t" . trim($h[0]);
            } elseif (!$key) {
                $headers[0] = trim($h[0]);
            }
            trim($h[0]);
        }
    }

    return $headers;
}

////
// Via: http://stackoverflow.com/a/4254008/425964
////
function is_assoc($array) {
    return (bool) count(array_filter(array_keys($array), 'is_string'));
}

////
// Via: http://stackoverflow.com/a/9776726/425964
////
function pretty_print_json($json) {
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = null;
    $json_length = strlen($json);

    for ($i = 0; $i < $json_length; $i++) {
        $char = $json[$i];
        $new_line_level = null;
        $post = "";
        if ($ends_line_level !== null) {
            $new_line_level = $ends_line_level;
            $ends_line_level = null;
        }
        if ($char === '"' && $prev_char != '\\') {
            $in_quotes = !$in_quotes;
        } else {
            if (!$in_quotes) {
                switch ($char) {
                    case '}':
                    case ']':
                        $level--;
                        $ends_line_level = null;
                        $new_line_level = $level;
                        break;

                    case '{':
                    case '[':
                        $level++;
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = " ";
                        break;

                    case " ":
                    case "\t":
                    case "\n":
                    case "\r":
                        $char = "";
                        $ends_line_level = $new_line_level;
                        $new_line_level = null;
                        break;
                }
            }
        }
        if ($new_line_level !== null) {
            $result .= "\n" . str_repeat("\t", $new_line_level);
        }
        $result .= $char . $post;
        $prev_char = $char;
    }

    return $result;
}
