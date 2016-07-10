<?php
/**
|--------------------------------------------------------------------------|
|   https://github.com/Bigjoos/                			    |
|--------------------------------------------------------------------------|
|   Licence Info: GPL			                                    |
|--------------------------------------------------------------------------|
|   Copyright (C) 2010 U-232 V5					    |
|--------------------------------------------------------------------------|
|   A bittorrent tracker source based on TBDev.net/tbsource/bytemonsoon.   |
|--------------------------------------------------------------------------|
|   Project Leaders: Mindless, Autotron, whocares, Swizzles.					    |
|--------------------------------------------------------------------------|
_   _   _   _   _     _   _   _   _   _   _     _   _   _   _
/ \ / \ / \ / \ / \   / \ / \ / \ / \ / \ / \   / \ / \ / \ / \
( U | - | 2 | 3 | 2 )-( S | o | u | r | c | e )-( C | o | d | e )
\_/ \_/ \_/ \_/ \_/   \_/ \_/ \_/ \_/ \_/ \_/   \_/ \_/ \_/ \_/
*/
//made by putyn @tbdev
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'bittorrent.php');
//require_once (INCL_DIR . 'phpzip.php');
dbconn();
loggedinorreturn();
$lang = array_merge(load_language('global'));

$action = (isset($_POST["action"]) ? htmlsafechars($_POST["action"]) : "");
if ($action == "download") {
    $id = isset($_POST["sid"]) ? (int) $_POST["sid"] : 0;
    if ($id == 0)
        stderr($lang['gl_error'], $lang['gl_not_a_valid_id']);
    else {
        $res = sql_query("SELECT id, name, filename, lang FROM subtitles WHERE id=" . sqlesc($id)) or sqlerr(__FILE__, __LINE__);
        $arr = mysqli_fetch_assoc($res);
        $ext = (substr($arr["filename"], -3));
        $fileName2 = str_replace(array(
            " ",
            ".",
            "-"
        ), "_", $arr["name"]) . '.' . $ext;
        $fileloc = $INSTALLER09['sub_up_dir'] . "/" . $arr["filename"];
        $zip = new ZipArchive();
        $file = $INSTALLER09['sub_up_dir'] . "/" . $arr["filename"];
        $zipname = $fileName2 . "_" . $arr["lang"] . ".zip";
        $filename = $INSTALLER09['sub_up_dir'] . "/" . $zipname;
        if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
            exit("cannot open <$zipname>\n");
        }
        $zip->addFile($fileloc, $arr["filename"]);
        $zip->close();
        ///Then download the zipped file.
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipname);
        header('Content-Length: ' . filesize($filename));
        readfile($filename);
        
        @unlink($filename);
        //        @unlink("{$INSTALLER09['sub_up_dir']}/$fileName");
        sql_query("UPDATE subtitles SET hits=hits+1 where id=" . sqlesc($id));
    }
} else
    stderr($lang['gl_error'], $lang['gl_no_way']);
?>
