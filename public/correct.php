<?php
    $db = new mysqli('127.0.0.1', 'children', 'children', 'help-children') or die('No DB');

    $upd  = array();
    $del  = array();
    $rows = $db->query('select * from requests where child_id is not null') or die($db->error);
    while ($row = $rows->fetch_assoc()) {
        $oid = $row['order_id'];
        if (empty($oid)) $oid = md5($row['created_at']);
        if (empty($upd[$oid])) {
            $upd[$oid]        = $row;
            $upd[$oid]['id']  = intval($row['id']);
            $upd[$oid]['sum'] = floatval($upd[$oid]['sum']);
        } else {
            $upd[$oid]['sum'] += floatval($row['sum']);
            $del[]            = intval($row['id']);
        }
    }

    foreach ($upd as $row) $db->query("update requests set sum = '{$row['sum']}' where id = {$row['id']}");

    $db->query('delete from requests where id in ('.implode(',', $del).')');

    $db->query('delete from child_history');