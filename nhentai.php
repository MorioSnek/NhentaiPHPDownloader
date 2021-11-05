<?php
error_reporting(0);
ini_set('display_errors', 0);

$nuclearCode = $argv[1];
$title = getTitle($nuclearCode);
$nPages = getnPages($nuclearCode);
$galleryID = getID($nuclearCode);

if (file_exists("$nuclearCode - $title") == 0) {
    mkdir("$nuclearCode - $title");
}
for ($i = 1; $i <= $nPages; $i++) {
    file_put_contents("./$nuclearCode - $title/$i.jpg", file_get_contents("https://i.nhentai.net/galleries/$galleryID/$i.jpg"));
    file_put_contents("./$nuclearCode - $title/$i.png", file_get_contents("https://i.nhentai.net/galleries/$galleryID/$i.png"));
    if (filesize("./$nuclearCode - $title/$i.jpg") == 0) {
        unlink("./$nuclearCode - $title/$i.jpg");
    }
    if (filesize("./$nuclearCode - $title/$i.png") == 0) {
        unlink("./$nuclearCode - $title/$i.png");
    }
}
createCBZ($nuclearCode, $title);

function getnPages($nuclearCode)
{
    $source = file_get_contents("https://nhentai.net/g/$nuclearCode/1/");
    $source = strstr($source, '<span class="num-pages">');
    $source = explode("</span>", $source);
    $source[0] = substr($source[0], 24);
    return $source[0];
}

function getID($nuclearCode)
{
    $source = file_get_contents("https://nhentai.net/g/$nuclearCode/1/");
    $source = strstr($source, 'media_id\u0022:\u0022');
    $source = explode("\u0022", $source);
    return intval($source[2]);
}

function getTitle($nuclearCode)
{
    $source = file_get_contents("https://nhentai.net/g/$nuclearCode/");
    $source = strstr($source, '<meta itemprop="name" content="');
    $source = explode('" />', $source);
    $source[0] = substr($source[0], strlen('<meta itemprop="name" content="'));
    $source[0] = preg_replace("/[^a-zA-Z 0-9.,]+/", "-", $source[0]);
    return $source[0];
}

function createCBZ($nuclearCode, $title)
{
    //inizio codice trovato in giro
    $zipFile = "./$nuclearCode - $title.cbz";
    $zipArchive = new ZipArchive();
    if ($zipArchive->open($zipFile, (ZipArchive::CREATE | ZipArchive::OVERWRITE)) !== true)
        die("Failed to create archive\n");
    $zipArchive->addGlob("./$nuclearCode - $title/*.*");
    if ($zipArchive->status != ZipArchive::ER_OK)
        echo "Failed to write files to zip\n";
    $zipArchive->close();
    //fine codice trovato in giro
}
