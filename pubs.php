<?php
    $_GET['library']=1;
    $_GET['bib']='authors.bib;bkarak-publications.bib';
    $_GET['all']=1;

    /* arguments below are those we want to ignore */
    unset($_GET['frameset']);
    unset($_GET['menu']);

    include('bibtexbrowser.php');

    setDB();

    new Dispatcher();
?>

