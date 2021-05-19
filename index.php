<?php

! file_exists(__DIR__ . '/key') && response(500, 'key not found');
$key = file_get_contents(__DIR__ . '/key');

$title = $_REQUEST['title'] ?? '空白标题';
$content = $_REQUEST['content'] ?? '空白内容';
$type = $_REQUEST['type'] ?? 'text';

switch ($type) {
    case 'text':
        $rs = send($title, $content);
        break;
    case 'table':
        $content = json_decode($content, true);
        $rs = send($title, buildMdTable($content));
        break;
    default:
        response(400, 'type not supported');
}

response($rs['code'], $rs['msg']);

function buildMdTable($content) {
    $rs = '';
    $firstVal = current($content);
    if (! is_array($firstVal)) {
        $firstIter = true;
        foreach ($content as $key => $val) {
            $rs .= ('| ' . $key . ' | ' . $val . ' |' . PHP_EOL);
            if ($firstIter) {
                $rs .= '| - | - |' . PHP_EOL;
                $firstIter = false;
            }
        }
    } else {
        $secondKey = key(current($content));
        if (is_int($secondKey)) {
            $firstIter = true;
            foreach ($content as $row) {
                $rs .= '| ';
                foreach ($row as $val) {
                    is_array($val) && $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                    $rs .= ($val . ' |');
                }
                $rs .= PHP_EOL;
                if ($firstIter) {
                    $divider = array_fill(0, count($row), '-');
                    $rs .= ('| ' . implode(' | ', $divider) . ' |' . PHP_EOL);        
                    $firstIter = false;
                }
            }
        } else {
            $keys = array_keys($firstVal);
            $divider = array_fill(0, count($keys), '-');
            $rs .= ('| ' . implode(' | ', $keys) . ' |' . PHP_EOL);
            $rs .= ('| ' . implode(' | ', $divider) . ' |' . PHP_EOL);
            foreach ($content as $row) {
                foreach ($row as $val) {
                    is_array($val) && $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                    $rs .= ('| ' . $val . ' ');
                }
                $rs .= ('|' . PHP_EOL);
            }
        }
    }
    return $rs;
}

function send($title, $content) {
    global $key;
    $req = curl_init("https://xizhi.qqoq.net/$key.send");
    curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($req, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($req, CURLOPT_POST, 1);
    curl_setopt($req, CURLOPT_POSTFIELDS, (compact('title', 'content')));
    curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
    $curlErrNo = curl_errno($req);
    $curlErrNo  != 0 && response(500, 'curl err no ' . $curlErrNo);
    $rs = json_decode(curl_exec($req), true);
    ! $rs && response(500, 'api err');
    return $rs;
}

function response($code, $msg) {
    header('Content-Type: application/json');
    echo json_encode(compact('code', 'msg'), JSON_UNESCAPED_UNICODE);
    exit;
}
