<?php
    include('style.php');
    define('BIBLIOGRAPHYSTYLE','GeoBibStyle');
    define('BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT',false);
    define('BIBLIOGRAPHYSECTIONS','my_sectioning');
    define('BIBTEXBROWSER_CSS', 'pubstyle.css');
    define('BIBTEXBROWSER_URL','');
    define('METADATA_EPRINTS',true);
    define('BIBTEXBROWSER_EMBEDDED_WRAPPER', 'CustomWrapper');

    function my_sectioning() {
        return
            array(
                // Articles
                array(
                  'query' => array(Q_TYPE=>'article'),
                  'title' => 'Refereed Articles'
                ),
                // Conference and Workshop papers
                array(
                  'query' => array(Q_TYPE=>'inproceedings'),
                  'title' => 'Refereed Conference and Workshop Papers'
                ),
                // Books
                array(
                  'query' => array(Q_TYPE=>'book'),
                  'title' => 'Books'
                ),
                // InBook / InCollection
                array(
                  'query' => array(Q_TYPE=>'inbook|incollection'),
                  'title' => 'Edited Book Chapters'
                ),
                // Theses
                array(
                  'query' => array(Q_TYPE=>'phdthesis|mastersthesis'),
                  'title' => 'Theses'
                ),
                // others
                array(
                  'query' => array(Q_TYPE=>'misc|bachelorsthesis|techreport'),
                  'title' => 'Other Publications'
                )
            );
    }

    function update_query($query, $suffix=NULL) {
        $args = explode('&', $query);
        foreach($args as $key => $val) {
            $comp = strtolower($val);
            /* Only remove exact match for year, leave year=foo alone */
            if(strpos($comp, 'academic') !== False || strpos($comp, 'all') !== False || strpos($comp, 'astext') !== False || $comp == 'year') {
                unset($args[$key]);
            }
        }
        if($suffix != NULL) { $args[] = $suffix; }
        $query = implode("&amp;", $args);
        if(strlen($query) > 0) { $query = '?' . $query; }
        return $query;
    }

    class CustomYearMenu  {
        function CustomYearMenu() {
            if (!isset($_GET[Q_DB])) {die('Did you forget to call setDB() before instantiating this class?');}
            $yearIndex = $_GET[Q_DB]->yearIndex();
            ?>
            <div id="yearmenu" class="filterbox toolbox">
                <span class="this">Years:</span>
                <div class="filterlist" id="yearlist">
                <?php
                echo '<span><a '.makeHref(array(Q_YEAR=>'.*')).'>All</a></span>'."\n";
                foreach($yearIndex as $year) {
                    echo '<span><a '.makeHref(array(Q_YEAR=>$year)).'>'.$year.'</a></span>'."\n";
                }
                ?>
            </div>
            </div>
            <?php
        }
    }
?>
<?php
    class CustomAuthorsMenu {
        function CustomAuthorsMenu() {
            if (!isset($_GET[Q_DB])) {die('Did you forget to call setDB() before instantiating this class?');}
            $authorIndex = $_GET[Q_DB]->authorIndex();
            ?>
            <select id="authorlist">
                <?php
                foreach($authorIndex as $author) {
                    echo '<option value="pubs.php?'.createQueryString(array(Q_AUTHOR=>$author)).'">'.$author.'</option>\n';
                }
                ?>
            </select>
            <button onclick="load_author()">Search</button>
            <script language="javascript">
                function load_author() {
                    window.location = $('#authorlist').val();
                }
            </script>
            <?php
        }
    }
?>
<?php
    function getPublisherDisclaimer($entry) {
        $pre = '<div>';
        $post = '</div>';

        $publisher = $entry->getField('publisher');
        $pos = stripos($publisher, 'ieee');
        if($pos !== false) {
            return $pre.'© '.$entry->getYear().' IEEE. Personal use of this material is permitted. Permission from IEEE must be obtained for all other uses, in any current or future media, including reprinting/republishing this material for advertising or promotional purposes, creating new collective works, for resale or redistribution to servers or lists, or reuse of any copyrighted component of this work in other works.' . $post;
        }

        /* Check if publisher contains 'springer' */
        $pos = stripos($publisher, 'springer');
        if($pos !== false) {
            return $pre . 'The original publication is available at <a href="http://www.springerlink.com">http://www.springerlink.com</a>' . $post;
        }
    }
?>
<?php
    Class CustomWrapper {
        function CustomWrapper(&$content, $metatags=array()) {
            //header ("Content-Type:application/xhtml+xml; charset=utf-8");
            echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
                <head>
                    <meta http-equiv="Content-type" content="application/xhtml+xml; charset=utf-8" />
                    <link href="../images/styles.css" rel="stylesheet" type="text/css" />
                    <link href="pubstyle.css" rel="stylesheet" type="text/css" />
                    <script language="javascript" src="../js/jquery-1.7.2.min.js"></script>
                    <title>
                    <?php
                    if ($content instanceof BibEntryDisplay) {
                        echo $content->getTitle();
                    } else {
                        echo 'Vassilios Karakoidas - Publications';
                    }
                    ?>
                    </title>
                    <!--<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />-->
                    <meta name="keywords" content="Vassilios Karakoidas, Vassilios, Karakoidas, bkarak" />
                    <?php
                    /* Add content meta-data, if any */
                    foreach($metatags as $item) {
                        list($name,$value) = $item;
                        echo '<meta name="'.$name.'" content="'.$value.'"/>'."\n";
                    }
                    ?>
                </head>
                <body>
                    <?php
                    if ($content instanceof BibEntryDisplay) {
                        echo "<h1>".$content->getTitle()."</h1>";
                    } else {
                    ?>
                    <div class="content">
                        <h1 class="logo">Vassilios Karakoidas - Publications</h1>

                        <b>Note</b>: <em>This material is presented to ensure timely dissemination of scholarly and technical work. Copyright and all rights therein are retained by authors or by other copyright holders. All persons copying this information are expected to adhere to the terms and constraints invoked by each author's copyright. In most cases, these works may not be reposted without the explicit permission of the copyright holder.</em>
                        <br/><br/>
                        Download the complete <a href="bkarak-publications.bib">BiBTeX</a> publications list.<br/><br/>
                        <a href="http://www.informatik.uni-trier.de/~ley/db/indices/a-tree/k/Karakoidas:Vassilios.html"><img src="dblp.gif" alt="dblp page"/></a>
                        <a href="http://researchr.org/profile/vassilioskarakoidas"><img src="researchr.png" alt="researchr page" /></a>
                        <a href="http://scholar.google.com/citations?user=STFoyREAAAAJ"><img src="scholar-google-beta.png" alt="google scholar"/></a>
                    </div>
                    <?php }
                ?>
                <div class="searchbox">
                    <button onclick="window.location = 'pubs.php?academic'">Show All</button>
                    <button onclick="window.location = '<?php echo update_query($_SERVER['QUERY_STRING'], 'academic'); ?>'">Sort by Type</button>
                    <button onclick="window.location = '<?php echo update_query($_SERVER['QUERY_STRING'], 'year'); ?>'">Sort by Year</button>
                    <button onclick="window.location = '<?php echo update_query($_SERVER['QUERY_STRING'], 'astext'); ?>'">Raw Bib</button>
                </div>
                <div class="search">
                    <form action="pubs.php?academic" method="get">
                        <input type="text" name="<?php echo Q_SEARCH; ?>" class="input_box" id="searchtext" />
                        <input type="hidden" name="<?php echo Q_FILE; ?>" value="<?php echo $_GET[Q_FILE]; ?>" />
                        <input type="submit" value="search" class="input_box" />
                    </form>
                </div>
                <div class="author_search">
                    <?php new CustomAuthorsMenu(); ?>
                </div>
                <div id="bodyText">
                <?php
                    $content->display();
                ?>
                </div>
                <script type="text/javascript">

                    var _gaq = _gaq || [];
                    _gaq.push(['_setAccount', 'UA-1844074-1']);
                    _gaq.push(['_trackPageview']);

                    (function() {
                        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                    })();

                </script>
                </body>
            </html>
            <?php
        }
    }
?>
