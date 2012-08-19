<?php
    function GeoBibStyle(&$bibentry) {
        $title = $bibentry->getTitle();
        $type = $bibentry->getType();

        $entry=array();

        // author
        if ($bibentry->hasField('author')) {
            $entry[] = $bibentry->formattedAuthors();
        }

        // title (hyperlinked to the steroids page)
        $title = ' "<a'.(BIBTEXBROWSER_BIB_IN_NEW_WINDOW?' target="_blank" ':'').' title="'.$bibentry->getKey().'" href="'.$bibentry->getURL().'">'.$title.'</a>"';
        $entry[] = $title;

        // now the origin of the publication is in italic
        $booktitle = '';

        if ($type=="inproceedings" || $type=="incollection") {
            $booktitle = 'in <i>'.$bibentry->getField(BOOKTITLE).'</i>';
        }

        if ($type=="article") {
            $booktitle = $bibentry->getField("journal");
        }

        //// ******* EDITOR
        $editor='';
        if ($bibentry->hasField(EDITOR)) {
            $editors = $bibentry->getFormattedEditors();
        }

        if ($booktitle!='') {
            if ($editor!='') $booktitle .=' ('.$editor.')';
            $entry[] = $booktitle;
        }

        if ($bibentry->hasField("series")) {
            $entry[] = "ser. " . $bibentry->getField("series");
        }

        $publisher='';
        if ($type=="phdthesis") {
            $publisher = 'PhD thesis, '.$bibentry->getField(SCHOOL);
        }

        if ($type=="mastersthesis") {
            $publisher = 'Master\'s thesis, '.$bibentry->getField(SCHOOL);
        }

        if ($type=="techreport") {
            $publisher = 'Technical report, '.$bibentry->getField("institution");
        }

        if ($bibentry->hasField("publisher") && $type!="inproceedings") {
            $publisher = $bibentry->getField("publisher");
        }

        if ($publisher!='') $entry[] = $publisher;

        if ($bibentry->hasField('volume')) $entry[] = "vol. ".$bibentry->getField("volume");
        if ($bibentry->hasField('number')) $entry[] = 'no. '.$bibentry->getField("number");
        if ($bibentry->hasField('address')) $entry[] = $bibentry->getField("address");
        if ($bibentry->hasField('pages')) $entry[] = str_replace("--", "-", "pp. ".$bibentry->getField("pages"));
        if ($bibentry->hasField(YEAR)) $entry[] = $bibentry->getYear();

        $result = implode(", ",$entry).'.';

        if ($bibentry->hasField('note')) {
            $result .=  " (".$bibentry->getField("note").")";
        }

        // some comments (e.g. acceptance rate)?
        if ($bibentry->hasField('comment')) {
            $result .=  " (".$bibentry->getField("comment").")";
        }

        // add the Coin URL
        $result .=  "\n".$bibentry->toCoins();

        return $result;
    }
?>
