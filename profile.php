<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2015 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

$_language->readModule('profile');

if (isset($_GET[ 'id' ])) {
    $id = (int)$_GET[ 'id' ];
} else {
    $id = $userID;
}
if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if (isset($id) && getnickname($id) != '') {
    if (isbanned($id)) {
        $banned =
            '<br><p class="text-center" style="color:red;font-weight:bold;font-size:11px;letter-spacing:1px;">' .
            $_language->module[ 'is_banned' ] . '</p>';
    } else {
        $banned = '';
    }

    if ($user_guestbook == 1) {
        if (getuserguestbookstatus($id) == 1) {
            $title_user_guestbook = '<td class="title" bgcolor="' . BGHEAD .
                '" width="20%">&nbsp; <a class="titlelink" href="index.php?site=profile&amp;id=' . $id .
                '&amp;action=guestbook">' . $_language->module[ 'guestbook' ] . '</a></td>';
            $title_width_main = 14;
            $title_width_galleries = 18;
            $title_width_buddys = 18;
            $title_width_lastposts = 30;
            $title_colspan = 5;
        } else {
            $title_user_guestbook = '';
            $title_width_main = 19;
            $title_width_galleries = 23;
            $title_width_buddys = 23;
            $title_width_lastposts = 35;
            $title_colspan = 5;
        }
    } else {
        $title_user_guestbook = '';
        $title_width_main = 19;
        $title_width_galleries = 23;
        $title_width_buddys = 23;
        $title_width_lastposts = 35;
        $title_colspan = 5;
    }

    //profil: buddys
    if ($action == "buddies") {
        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$banned'] = $banned;
        $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
        echo $title_profile;

        $buddylist = "";
        $buddys = safe_query("SELECT buddy FROM " . PREFIX . "buddys WHERE userID='" . $id . "'");
        if (mysqli_num_rows($buddys)) {
            $n = 1;
            while ($db = mysqli_fetch_array($buddys)) {
                $n % 2 ? $bgcolor = BG_1 : $bgcolor = BG_2;
                $flag = '[flag]' . getcountry($db[ 'buddy' ]) . '[/flag]';
                $country = flags($flag);
                $nicknamebuddy = getnickname($db[ 'buddy' ]);
                $email = "<a href='mailto:" . mail_protect(getemail($db[ 'buddy' ])) .
                    "'><span class='glyphicon glyphicon-envelope'></span></a>";

                if (isignored($userID, $db[ 'buddy' ])) {
                    $buddy =
                        '<a href="buddies.php?action=readd&amp;id=' . $db[ 'buddy' ] . '&amp;userID=' . $userID . '">
                            <img src="images/icons/buddy_readd.gif"
                                alt="' . $_language->module[ 'back_buddylist' ] . '">
                        </a>';
                } elseif (isbuddy($userID, $db[ 'buddy' ])) {
                    $buddy = '<a
                        href="buddies.php?action=ignore&amp;id=' . $db[ 'buddy' ] . '&amp;userID=' . $userID . '">
                        <img src="images/icons/buddy_ignore.gif" alt="' . $_language->module[ 'ignore_user' ] .
                        '">
                    </a>';
                } elseif ($userID == $db[ 'buddy' ]) {
                    $buddy = '';
                } else {
                    $buddy = '<a href="buddies.php?action=add&amp;id=' . $db[ 'buddy' ] . '&amp;userID=' . $userID . '">
                        <img src="images/icons/buddy_add.gif" alt="' . $_language->module[ 'add_buddylist' ] . '">
                    </a>';
                }

                if (isonline($db[ 'buddy' ]) == "offline") {
                    $statuspic = '<img src="images/icons/offline.gif" alt="' . $_language->module[ 'offline' ] . '">';
                } else {
                    $statuspic = '<img src="images/icons/online.gif" alt="' . $_language->module[ 'online' ] . '">';
                }

                $buddylist .= '<tr>
            <td>
            <table class="table">
                <tr>
                    <td>' . $country . ' <a href="index.php?site=profile&amp;id=' . $db[ 'buddy' ] . '"><b>' .
                        $nicknamebuddy . '</b></a></td>
                    <td class="text-right">' . $email . '&nbsp;&nbsp;' . $buddy . '&nbsp;&nbsp;' . $statuspic . '</td>
                </tr>
                </table>
                </td>
            </tr>';

                $n++;
            }
        } else {
            $buddylist = '<tr>
            <td colspan="2">' . $_language->module[ 'no_buddies' ] . '</td>
        </tr>';
        }

        $data_array = array();
        $data_array['$buddylist'] = $buddylist;
        $profile = $GLOBALS["_template"]->replaceTemplate("profile_buddys", $data_array);
        echo $profile;
    } elseif ($action == "galleries") {
        //galleries
        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$banned'] = $banned;
        $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
        echo $title_profile;

        $galclass = new \webspell\Gallery();

        $border = BORDER;
        $bgcat = BGCAT;
        $pagebg = PAGEBG;

        $galleries = safe_query("SELECT * FROM " . PREFIX . "gallery WHERE userID='" . $id . "'");

        echo '<table class="table">
        <tr>
            <td colspan="4">
                ' . $_language->module[ 'galleries' ] . ' ' . $_language->module[ 'by' ] . ' ' . getnickname($id) . '
            </td>
        </tr>
        <tr>
            <td></td>
            <td><strong>' . $_language->module[ 'date' ] . '</strong></td>
            <td><strong>' . $_language->module[ 'name' ] . '</strong></td>
            <td><strong>' . $_language->module[ 'pictures' ] . '</strong></td>
        </tr>';

        if ($usergalleries) {
            if (mysqli_num_rows($galleries)) {
                $n = 1;
                while ($ds = mysqli_fetch_array($galleries)) {
                    $n % 2 ? $bg = BG_1 : $bg = BG_2;

                    $piccount =
                        mysqli_num_rows(
                            safe_query(
                                "SELECT
                                    *
                                FROM
                                    " . PREFIX . "gallery_pictures
                                WHERE
                                    galleryID='" . (int)$ds[ 'galleryID' ]."'"
                            )
                        );
                    $commentcount = mysqli_num_rows(
                        safe_query(
                            "SELECT
                                *
                            FROM
                                " . PREFIX . "comments
                            WHERE
                                parentID='" . $ds[ 'galleryID' ] . "' AND
                                type='ga'"
                        )
                    );
                    $gallery[ 'count' ] = mysqli_num_rows(
                        safe_query(
                            "SELECT
                                picID
                            FROM
                                `" . PREFIX . "gallery_pictures`
                            WHERE
                                galleryID='" . (int)$ds[ 'galleryID' ] ."'"
                        )
                    );

                    $data_array = array();
                    $data_array['$date'] = getformatdate($ds[ 'date' ]);
                    $data_array['$picID'] = $galclass->randomPic($ds[ 'galleryID' ]);
                    $data_array['$galleryID'] = $ds[ 'galleryID' ];
                    $data_array['$title'] = cleartext($ds[ 'name' ]);
                    $data_array['$thumbwidth'] = $thumbwidth;
                    $data_array['$count'] = $gallery[ 'count' ];
                    $profile = $GLOBALS["_template"]->replaceTemplate("profile_galleries", $data_array);
                    echo $profile;

                    $n++;
                }
            } else {
                echo '<tr><td colspan="4">' . $_language->module[ 'no_galleries' ] . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="4">' . $_language->module[ 'usergalleries_disabled' ] . '</td></tr>';
        }

        echo '</table>';
    } elseif ($action == "lastposts") {
        //profil: last posts

        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$banned'] = $banned;
        $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
        echo $title_profile;

        $topiclist = "";
        $topics = safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "forum_topics
            WHERE
                userID = '" . $id . "' AND
                moveID = 0
            ORDER BY
                date DESC"
        );
        if (mysqli_num_rows($topics)) {
            $n = 1;
            while ($db = mysqli_fetch_array($topics)) {
                if ($db[ 'readgrps' ] != "") {
                    $usergrps = explode(";", $db[ 'readgrps' ]);
                    $usergrp = 0;
                    foreach ($usergrps as $value) {
                        if (isinusergrp($value, $userID)) {
                            $usergrp = 1;
                            break;
                        }
                    }
                    if (!$usergrp && !ismoderator($userID, $db[ 'boardID' ])) {
                        continue;
                    }
                }
                $n % 2 ? $bgcolor = BG_1 : $bgcolor = BG_2;
                $posttime = getformatdatetime($db[ 'date' ]);

                $topiclist .= '<tr><td>
                        <div style="overflow:hidden;">
                            <a href="index.php?site=forum_topic&amp;topic=' . $db[ 'topicID' ] . '">' .
                                $posttime . '<br>
                                <strong>' . clearfromtags($db[ 'topic' ]) . '</strong>
                            </a><br>
                            <i>' . $db[ 'views' ] . ' ' . $_language->module[ 'views' ] . ' - ' .
                            $db[ 'replys' ] . ' ' . $_language->module[ 'replys' ] . '</i>
                        </div>
                    </td>
                </tr>';

                if ($profilelast == $n) {
                    break;
                }
                $n++;
            }
        } else {
            $topiclist = '<tr><td colspan="2">' . $_language->module[ 'no_topics' ] . '</td></tr>';
        }

        $postlist = "";
        $posts =
            safe_query(
                "SELECT
                    " . PREFIX . "forum_topics.boardID,
                    " . PREFIX . "forum_topics.readgrps,
                    " . PREFIX . "forum_topics.topicID,
                    " . PREFIX . "forum_topics.topic,
                    " . PREFIX . "forum_posts.date,
                    " . PREFIX . "forum_posts.message
                FROM
                    " . PREFIX . "forum_posts,
                    " . PREFIX . "forum_topics
                WHERE
                    " . PREFIX . "forum_posts.poster = '" . $id . "' AND
                    " . PREFIX . "forum_posts.topicID = " . PREFIX . "forum_topics.topicID
                ORDER BY date DESC"
            );
        if (mysqli_num_rows($posts)) {
            $n = 1;
            while ($db = mysqli_fetch_array($posts)) {
                if ($db[ 'readgrps' ] != "") {
                    $usergrps = explode(";", $db[ 'readgrps' ]);
                    $usergrp = 0;
                    foreach ($usergrps as $value) {
                        if (isinusergrp($value, $userID)) {
                            $usergrp = 1;
                            break;
                        }
                    }
                    if (!$usergrp && !ismoderator($userID, $db[ 'boardID' ])) {
                        continue;
                    }
                }

                $n % 2 ? $bgcolor1 = BG_1 : $bgcolor1 = BG_2;
                $n % 2 ? $bgcolor2 = BG_3 : $bgcolor2 = BG_4;
                $posttime = getformatdatetime($db[ 'date' ]);
                if (mb_strlen($db[ 'message' ]) > 100) {
                    $message = mb_substr(
                        $db[ 'message' ],
                        0,
                        90 + mb_strpos(
                            mb_substr(
                                $db[ 'message' ],
                                90,
                                mb_strlen($db[ 'message' ])
                            ),
                            " "
                        )
                    ) . "...";
                } else {
                    $message = $db[ 'message' ];
                }

                $postlist .= '<tr><td>
                        <div style="overflow:hidden;">
                            <a href="index.php?site=forum_topic&amp;topic=' . $db[ 'topicID' ] . '">' .
                                $posttime . '<br>
                                <strong>' . clearfromtags($db[ 'topic' ]) . '</strong>
                            </a><br>
                            <i>' . clearfromtags($message) . '</i>
                        </div>
                    </td>
                </tr>';

                if ($profilelast == $n) {
                    break;
                }
                $n++;
            }
        } else {
            $postlist = '<tr><td colspan="2">' . $_language->module[ 'no_posts' ] . '</td></tr>';
        }

        $data_array = array();
        $data_array['$topiclist'] = $topiclist;
        $data_array['$postlist'] = $postlist;
        $profile = $GLOBALS["_template"]->replaceTemplate("profile_lastposts", $data_array);
        echo $profile;
    } elseif ($action == "guestbook") {
        if ($user_guestbook == 1) {
            if (getuserguestbookstatus($id) == 1) {
                //user guestbook
                if (isset($_POST[ 'save' ])) {
                    $date = time();
                    $ip = $GLOBALS[ 'ip' ];
                    $run = 0;

                    if ($userID) {
                        $name = getnickname($userID);
                        if (getemailhide($userID)) {
                            $email = '';
                        } else {
                            $email = getemail($userID);
                        }
                        $url = gethomepage($userID);
                        $icq = geticq($userID);
                        $run = 1;
                    } else {
                        $name = $_POST[ 'gbname' ];
                        $email = $_POST[ 'gbemail' ];
                        $url = $_POST[ 'gburl' ];
                        $icq = $_POST[ 'icq' ];
                        $CAPCLASS = new \webspell\Captcha;
                        if ($CAPCLASS->checkCaptcha($_POST[ 'captcha' ], $_POST[ 'captcha_hash' ])) {
                            $run = 1;
                        }
                    }

                    if ($run) {
                        safe_query(
                            "INSERT INTO
                                " . PREFIX . "user_gbook (
                                    `userID`,
                                    `date`,
                                    `name`,
                                    `email`,
                                    `hp`,
                                    `icq`,
                                    `ip`,
                                    `comment`
                                )
                                values(
                                    '" . $id . "',
                                    '" . $date . "',
                                    '" . $_POST[ 'gbname' ] . "',
                                    '" . $_POST[ 'gbemail' ] . "',
                                    '" . $_POST[ 'gburl' ] . "',
                                    '" . $_POST[ 'icq' ] . "',
                                    '" . $ip . "',
                                    '" . $_POST[ 'message' ] . "'
                                )"
                        );

                        if ($id != $userID) {
                            sendmessage(
                                $id,
                                $_language->module[ 'new_guestbook_entry' ],
                                str_replace('%guestbook_id%', $id, $_language->module[ 'new_guestbook_entry_msg' ])
                            );
                        }
                    }
                    redirect('index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook', '', 0);
                } elseif (isset($_GET[ 'delete' ])) {
                    if (!isanyadmin($userID) && $id != $userID) {
                        die($_language->module[ 'no_access' ]);
                    }

                    foreach ($_POST[ 'gbID' ] as $gbook_id) {
                        safe_query("DELETE FROM " . PREFIX . "user_gbook WHERE gbID='" . (int)$gbook_id."'");
                    }
                    redirect('index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook', '', 0);
                } else {
                    $data_array = array();
                    $data_array['$id'] = $id;
                    $data_array['$profilelast'] = $profilelast;
                    $data_array['$banned'] = $banned;
                    $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
                    echo $title_profile;

                    $bg1 = BG_1;
                    $bg2 = BG_2;

                    $gesamt =
                        mysqli_num_rows(
                            safe_query(
                                "SELECT
                                    `gbID`
                                FROM
                                    " . PREFIX . "user_gbook
                                WHERE
                                    `userID` = '" . (int)$id."'"
                            )
                        );

                    if (isset($_GET[ 'page' ])) {
                        $page = (int)$_GET[ 'page' ];
                    }
                    $type = "DESC";
                    if (isset($_GET[ 'type' ])) {
                        if (($_GET[ 'type' ] == 'ASC') || ($_GET[ 'type' ] == 'DESC')) {
                            $type = $_GET[ 'type' ];
                        }
                    }

                    $pages = 1;
                    if (!isset($page)) {
                        $page = 1;
                    }
                    if (!isset($type)) {
                        $type = "DESC";
                    }

                    $max = $maxguestbook;
                    $pages = ceil($gesamt / $max);

                    if ($pages > 1) {
                        $page_link =
                            makepagelink(
                                "index.php?site=profile&amp;id=" . $id . "&amp;action=guestbook&amp;type=" . $type,
                                $page,
                                $pages
                            );
                    } else {
                        $page_link = '';
                    }

                    if ($page == "1") {
                        $ergebnis = safe_query(
                            "SELECT
                                *
                            FROM
                                " . PREFIX . "user_gbook
                            WHERE
                                userID='" . $id . "'
                            ORDER BY
                                date " . $type . " LIMIT 0, " . (int)$max
                        );
                        if ($type == "DESC") {
                            $n = $gesamt;
                        } else {
                            $n = 1;
                        }
                    } else {
                        $start = $page * $max - $max;
                        $ergebnis = safe_query(
                            "SELECT
                                *
                            FROM
                                " . PREFIX . "user_gbook
                            WHERE
                                userID='" . $id . "'
                            ORDER BY
                                date " . $type . " LIMIT " . (int)$start . ", " . (int)$max
                        );
                        if ($type == "DESC") {
                            $n = $gesamt - ($page - 1) * $max;
                        } else {
                            $n = ($page - 1) * $max + 1;
                        }
                    }

                    if ($type == "ASC") {
                        $sorter = '<a href="index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook&amp;page=' .
                            $page . '&amp;type=DESC">' . $_language->module[ 'sort' ] .
                            ' <span class="glyphicon glyphicon-chevron-down"></span></a>';
                    } else {
                        $sorter = '<a href="index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook&amp;page=' .
                            $page . '&amp;type=ASC">' . $_language->module[ 'sort' ] .
                            ' <span class="glyphicon glyphicon-chevron-up"></span></a>';
                    }

                    echo '<div class="row form-group"><div class="col-xs-6">' . $sorter . ' ' . $page_link . '</div>
                        <div class="col-xs-6 text-right">
                            <a href="#addcomment" class="btn btn-primary">' .
                                $_language->module[ 'new_entry' ] . '
                            </a>
                        </div>
                    </div>';

                    echo '<form method="post" name="form"
                        action="index.php?site=profile&amp;id=' . $id . '&amp;action=guestbook&amp;delete=true">';
                    while ($ds = mysqli_fetch_array($ergebnis)) {
                        $n % 2 ? $bg1 = BG_1 : $bg1 = BG_2;
                        $date = getformatdatetime($ds[ 'date' ]);

                        if (validate_email($ds[ 'email' ])) {
                            $email = '<a href="mailto:' . mail_protect($ds[ 'email' ]) . '">
                                <span class="glyphicon glyphicon-envelope" title="' . $_language->module[ 'email' ] .
                                '"></span></a>';
                        } else {
                            $email = '';
                        }

                        if (validate_url($ds[ 'hp' ])) {
                            $hp = '<a href="' . $ds[ 'hp' ] . '" target="_blank">
                                <img src="images/icons/hp.gif" alt="' . $_language->module[ 'homepage' ] . '">
                            </a>';
                        } else {
                            $hp = '';
                        }

                        $sem = '/[0-9]{6,11}/si';
                        $icq_number = str_replace('-', '', $ds[ 'icq' ]);
                        if (preg_match($sem, $icq_number)) {
                            $icq = '<a href="http://www.icq.com/people/about_me.php?uin=' . $icq_number . '"
                                target="_blank">
                                <img src="http://online.mirabilis.com/scripts/online.dll?icq=' .
                                    $icq_number . '&amp;img=5" alt="icq">
                            </a>';
                        } else {
                            $icq = "";
                        }

                        $name = strip_tags($ds[ 'name' ]);
                        $message = cleartext($ds[ 'comment' ]);
                        $quotemessage = strip_tags($ds[ 'comment' ]);
                        $quotemessage = str_replace("'", "`", $quotemessage);

                        $actions = '';
                        $ip = $_language->module[ 'logged' ];
                        $quote = '<a href="javascript:AddCode(\'[quote=' . $name . ']' . $quotemessage .
                            '[/quote]\')"> <span class="no_replace_glyphicon glyphicon-quote-left"></span></a>';
                        if (isfeedbackadmin($userID) || $id == $userID) {
                            $actions =
                                '<input class="input" type="checkbox" name="gbID[]" value="' . $ds[ 'gbID' ] . '">';
                            if (isfeedbackadmin($userID)) {
                                $ip = $ds[ 'ip' ];
                            }
                        }

                        $data_array = array();
                        $data_array['$actions'] = $actions;
                        $data_array['$name'] = $name;
                        $data_array['$date'] = $date;
                        $data_array['$email'] = $email;
                        $data_array['$hp'] = $hp;
                        $data_array['$icq'] = $icq;
                        $data_array['$ip'] = $ip;
                        $data_array['$quote'] = $quote;
                        $data_array['$message'] = $message;
                        $profile_guestbook = $GLOBALS["_template"]->replaceTemplate("profile_guestbook", $data_array);
                        echo $profile_guestbook;

                        if ($type == "DESC") {
                            $n--;
                        } else {
                            $n++;
                        }
                    }

                    if (isfeedbackadmin($userID) || $userID == $id) {
                        $submit =
                            '<input class="input" type="checkbox" name="ALL" value="ALL"
                                onclick="SelectAll(this.form);"> ' .
                            $_language->module[ 'select_all' ] . '
                    <input type="submit" value="' .
                        $_language->module[ 'delete_selected' ] . '" class="btn btn-danger">';
                    } else {
                        $submit = '';
                    }

                    echo '<table class="table"><tr>
                        <td>' . $page_link . '</td>
                        <td class="text-right">' . $submit . '</td>
                        </tr></table></form>';

                    echo '<a name="addcomment"></a>';
                    if ($loggedin) {
                        $name = getnickname($userID);
                        if (getemailhide($userID)) {
                            $email = '';
                        } else {
                            $email = getemail($userID);
                        }
                        $url = gethomepage($userID);
                        $icq = geticq($userID);
                        $_language->readModule('bbcode', true);

                        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
                        $data_array = array();
                        $data_array['$id'] = $id;
                        $data_array['$addbbcode'] = $addbbcode;
                        $data_array['$name'] = $name;
                        $data_array['$email'] = $email;
                        $data_array['$url'] = $url;
                        $data_array['$icq'] = $icq;
                        $profile_guestbook_loggedin = $GLOBALS["_template"]->replaceTemplate(
                            "profile_guestbook_loggedin",
                            $data_array
                        );
                        echo $profile_guestbook_loggedin;
                    } else {
                        $CAPCLASS = new \webspell\Captcha;
                        $captcha = $CAPCLASS->createCaptcha();
                        $hash = $CAPCLASS->getHash();
                        $CAPCLASS->clearOldCaptcha();
                        $_language->readModule('bbcode', true);

                        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
                        $data_array = array();
                        $data_array['$id'] = $id;
                        $data_array['$addbbcode'] = $addbbcode;
                        $data_array['$captcha'] = $captcha;
                        $data_array['$hash'] = $hash;
                        $profile_guestbook_notloggedin = $GLOBALS["_template"]->replaceTemplate(
                            "profile_guestbook_notloggedin",
                            $data_array
                        );
                        echo $profile_guestbook_notloggedin;
                    }
                }
            } else {
                redirect('index.php?site=404', '', 0);
            }
        } else {
            redirect('index.php?site=404', '', 0);
        }
    } else {
        //profil: home

        $data_array = array();
        $data_array['$id'] = $id;
        $data_array['$profilelast'] = $profilelast;
        $data_array['$banned'] = $banned;
        $title_profile = $GLOBALS["_template"]->replaceTemplate("title_profile", $data_array);
        echo $title_profile;

        $date = time();
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "user WHERE userID='" . $id . "'");
        $anz = mysqli_num_rows($ergebnis);
        $ds = mysqli_fetch_array($ergebnis);

        if ($userID != $id && $userID != 0) {
            safe_query("UPDATE " . PREFIX . "user SET visits=visits+1 WHERE userID='" . $id . "'");
            if (mysqli_num_rows(
                safe_query(
                    "SELECT
                            visitID
                        FROM
                            " . PREFIX . "user_visitors
                        WHERE
                            userID='" . $id . "' AND
                            visitor='" . (int)$userID."'"
                )
            )
            ) {
                safe_query(
                    "UPDATE
                        " . PREFIX . "user_visitors
                        SET
                            date='" . $date . "'
                        WHERE
                            userID='" . $id . "'AND
                            visitor='" . (int)$userID."'"
                );
            } else {
                safe_query(
                    "INSERT INTO
                        " . PREFIX . "user_visitors (
                            userID,
                            visitor,
                            date
                        )
                        values (
                            '" . $id . "',
                            '" . $userID . "',
                            '" . $date . "'
                        )"
                );
            }
        }
        $anzvisits = $ds[ 'visits' ];
        if ($ds[ 'userpic' ]) {
            $userpic = '<img src="images/userpics/' . $ds[ 'userpic' ] . '" alt="">';
        } else {
            $userpic = '<img src="images/userpics/nouserpic.gif" alt="">';
        }
        $nickname = $ds[ 'nickname' ];
        if (isclanmember($id)) {
            $member = ' <img src="images/icons/member.gif" alt="' . $_language->module[ 'clanmember' ] . '">';
        } else {
            $member = '';
        }
        $registered = getformatdatetime($ds[ 'registerdate' ]);
        $lastlogin = getformatdatetime($ds[ 'lastlogin' ]);
        if ($ds[ 'avatar' ]) {
            $avatar = '<img src="images/avatars/' . $ds[ 'avatar' ] . '" alt="">';
        } else {
            $avatar = '<img src="images/avatars/noavatar.gif" alt="">';
        }
        $status = isonline($ds[ 'userID' ]);
        if ($ds[ 'email_hide' ]) {
            $email = $_language->module[ 'n_a' ];
        } else {
            $email = '<a href="mailto:' . mail_protect(cleartext($ds[ 'email' ])) .
                '"><span class="glyphicon glyphicon-envelope" title="' . $_language->module[ 'email' ] . '">
                </span></a>';
        }
        $sem = '/[0-9]{4,11}/si';
        if (preg_match($sem, $ds[ 'icq' ])) {
            $icq = '<a href="http://www.icq.com/people/about_me.php?uin=' . sprintf('%d', $ds[ 'icq' ]) .
                '" target="_blank"><img src="http://online.mirabilis.com/scripts/online.dll?icq=' .
                sprintf('%d', $ds[ 'icq' ]) . '&amp;img=5" alt="icq"></a>';
        } else {
            $icq = '';
        }
        if ($loggedin && $ds[ 'userID' ] != $userID) {
            $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $ds[ 'userID' ] . '">
                <img src="images/icons/pm.gif" width="12" height="13" alt="messenger">
            </a>';
            if (isignored($userID, $ds[ 'userID' ])) {
                $buddy = '<a href="buddies.php?action=readd&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '">
                    <img src="images/icons/buddy_readd.gif" alt="' . $_language->module[ 'back_buddylist' ] . '">
                </a>';
            } elseif (isbuddy($userID, $ds[ 'userID' ])) {
                $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '">
                    <img src="images/icons/buddy_ignore.gif" alt="' . $_language->module[ 'ignore_user' ] . '">
                </a>';
            } elseif ($userID == $ds[ 'userID' ]) {
                $buddy = '';
            } else {
                $buddy = '<a href="buddies.php?action=add&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID . '">
                    <img src="images/icons/buddy_add.gif" alt="' . $_language->module[ 'add_buddylist' ] . '">
                </a>';
            }
        } else {
            $pm = '' & $buddy = '';
        }

        if ($ds[ 'homepage' ] != '') {
            if (stristr($ds[ 'homepage' ], "http://")) {
                $homepage = '<a href="' . htmlspecialchars($ds[ 'homepage' ]) . '" target="_blank" rel="nofollow">' .
                    htmlspecialchars($ds[ 'homepage' ]) . '</a>';
            } else {
                $homepage = '<a href="http://' . htmlspecialchars($ds[ 'homepage' ]) . '" target="_blank"
                    rel="nofollow">
                    http://' . htmlspecialchars($ds[ 'homepage' ]) . '
                </a>';
            }
        } else {
            $homepage = $_language->module[ 'n_a' ];
        }

        $clanhistory = clearfromtags($ds[ 'clanhistory' ]);
        if ($clanhistory == '') {
            $clanhistory = $_language->module[ 'n_a' ];
        }
        $clanname = clearfromtags($ds[ 'clanname' ]);
        if ($clanname == '') {
            $clanname = $_language->module[ 'n_a' ];
        }
        $clanirc = clearfromtags($ds[ 'clanirc' ]);
        if ($clanirc == '') {
            $clanirc = $_language->module[ 'n_a' ];
        }
        if ($ds[ 'clanhp' ] == '') {
            $clanhp = $_language->module[ 'n_a' ];
        } else {
            if (stristr($ds[ 'clanhp' ], "http://")) {
                $clanhp = '<a href="' . htmlspecialchars($ds[ 'clanhp' ]) . '" target="_blank" rel="nofollow">' .
                    htmlspecialchars($ds[ 'clanhp' ]) . '</a>';
            } else {
                $clanhp = '<a href="http://' . htmlspecialchars($ds[ 'clanhp' ]) . '" target="_blank" rel="nofollow">' .
                    htmlspecialchars($ds[ 'clanhp' ]) . '</a>';
            }
        }
        $clantag = clearfromtags($ds[ 'clantag' ]);
        if ($clantag == '') {
            $clantag = '';
        } else {
            $clantag = '(' . $clantag . ') ';
        }

        $firstname = clearfromtags($ds[ 'firstname' ]);
        $lastname = clearfromtags($ds[ 'lastname' ]);

        $birthday = getformatdate(strtotime($ds['birthday']));

        $res =
            safe_query(
                "SELECT
                    birthday,
                    DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%Y') 'age'
                FROM
                    " . PREFIX . "user
                WHERE
                    userID = '" . (int)$id."'"
            );
        $cur = mysqli_fetch_array($res);
        $birthday = $birthday . " (" . (int)$cur[ 'age' ] . " " . $_language->module[ 'years' ] . ")";

        if ($ds[ 'sex' ] == "f") {
            $sex = $_language->module[ 'female' ];
        } elseif ($ds[ 'sex' ] == "m") {
            $sex = $_language->module[ 'male' ];
        } else {
            $sex = $_language->module[ 'unknown' ];
        }
        $flag = '[flag]' . $ds[ 'country' ] . '[/flag]';
        $profilecountry = flags($flag);
        $town = clearfromtags($ds[ 'town' ]);
        if ($town == '') {
            $town = $_language->module[ 'n_a' ];
        }
        $cpu = clearfromtags($ds[ 'cpu' ]);
        if ($cpu == '') {
            $cpu = $_language->module[ 'n_a' ];
        }
        $mainboard = clearfromtags($ds[ 'mainboard' ]);
        if ($mainboard == '') {
            $mainboard = $_language->module[ 'n_a' ];
        }
        $ram = clearfromtags($ds[ 'ram' ]);
        if ($ram == '') {
            $ram = $_language->module[ 'n_a' ];
        }
        $monitor = clearfromtags($ds[ 'monitor' ]);
        if ($monitor == '') {
            $monitor = $_language->module[ 'n_a' ];
        }
        $graphiccard = clearfromtags($ds[ 'graphiccard' ]);
        if ($graphiccard == '') {
            $graphiccard = $_language->module[ 'n_a' ];
        }
        $soundcard = clearfromtags($ds[ 'soundcard' ]);
        if ($soundcard == '') {
            $soundcard = $_language->module[ 'n_a' ];
        }
        $connection = clearfromtags($ds[ 'verbindung' ]);
        if ($connection == '') {
            $connection = $_language->module[ 'n_a' ];
        }
        $keyboard = clearfromtags($ds[ 'keyboard' ]);
        if ($keyboard == '') {
            $keyboard = $_language->module[ 'n_a' ];
        }
        $mouse = clearfromtags($ds[ 'mouse' ]);
        if ($mouse == '') {
            $mouse = $_language->module[ 'n_a' ];
        }
        $mousepad = clearfromtags($ds[ 'mousepad' ]);
        if ($mousepad == '') {
            $mousepad = $_language->module[ 'n_a' ];
        }
        $hdd = clearfromtags($ds[ 'hdd' ]);
        if ($hdd == '') {
            $hdd = $_language->module[ 'n_a' ];
        }
        $headset = clearfromtags($ds[ 'headset' ]);
        if ($headset == '') {
            $headset = $_language->module[ 'n_a' ];
        }

        $anznewsposts = getusernewsposts($ds[ 'userID' ]);
        $anzforumtopics = getuserforumtopics($ds[ 'userID' ]);
        $anzforumposts = getuserforumposts($ds[ 'userID' ]);

        $pmgot = 0;
        $pmgot = $ds[ 'pmgot' ];

        $pmsent = 0;
        $pmsent = $ds[ 'pmsent' ];

        if ($ds[ 'about' ]) {
            $about = cleartext($ds[ 'about' ]);
        } else {
            $about = $_language->module[ 'n_a' ];
        }

        if (isforumadmin($ds[ 'userID' ])) {
            $usertype = $_language->module[ 'administrator' ];
            $rang = '<img src="images/icons/ranks/admin.gif" alt="">';
        } elseif (isanymoderator($ds[ 'userID' ])) {
            $usertype = $_language->module[ 'moderator' ];
            $rang = '<img src="images/icons/ranks/moderator.gif" alt="">';
        } else {
            $posts = getuserforumposts($ds[ 'userID' ]);
            $ergebnis =
                safe_query(
                    "SELECT
                        *
                    FROM
                        " . PREFIX . "forum_ranks
                    WHERE
                        " . $posts . " >= postmin AND
                        " . $posts . " <= postmax AND
                        postmax > 0 AND
                        special='0'"
                );
            $dt = mysqli_fetch_array($ergebnis);
            $usertype = $dt[ 'rank' ];
            $rang = '<img src="images/icons/ranks/' . $dt[ 'pic' ] . '" alt="">';
        }

        $specialrank = '';
        $getrank = safe_query(
            "SELECT IF
                (u.special_rank = 0, 0, CONCAT_WS('__', r.rank, r.pic)) as RANK
            FROM
                " . PREFIX . "user u LEFT JOIN " . PREFIX . "forum_ranks r ON u.special_rank = r.rankID
            WHERE
                userID='" . $ds[ 'userID' ] . "'"
        );
        $rank_data = mysqli_fetch_assoc($getrank);

        if ($rank_data[ 'RANK' ] != '0') {
            $specialrank  = '<br/>';
            $tmp_rank = explode("__", $rank_data[ 'RANK' ], 2);
            $specialrank .= $tmp_rank[0];
            if (!empty($tmp_rank[1]) && file_exists("images/icons/ranks/" . $tmp_rank[1])) {
                $specialrank .= '<br/>';
                $specialrank .= "<img src='images/icons/ranks/" . $tmp_rank[1] . "' alt = '' />";
            }
        }

        $lastvisits = "";
        $visitors = safe_query(
            "SELECT
                v.*,
                u.nickname,
                u.country
            FROM
                " . PREFIX . "user_visitors v
            JOIN " . PREFIX . "user u ON
                u.userID = v.visitor
            WHERE
                v.userID='" . $id . "'
            ORDER BY
                v.date DESC
                LIMIT 0,10"
        );
        if (mysqli_num_rows($visitors)) {
            $n = 1;
            while ($dv = mysqli_fetch_array($visitors)) {
                $n % 2 ? $bgcolor = BG_1 : $bgcolor = BG_2;
                $flag = '[flag]' . $dv[ 'country' ] . '[/flag]';
                $country = flags($flag);
                $nicknamevisitor = $dv[ 'nickname' ];
                if (isonline($dv[ 'visitor' ]) == "offline") {
                    $statuspic = '<img src="images/icons/offline.gif" alt="' . $_language->module[ 'offline' ] . '">';
                } else {
                    $statuspic = '<img src="images/icons/online.gif" alt="' . $_language->module[ 'online' ] . '">';
                }
                $time = time();
                $visittime = $dv[ 'date' ];

                $sec = $time - $visittime;
                $days = $sec / 86400;                                // sekunden / (60*60*24)
                $days = mb_substr($days, 0, mb_strpos($days, "."));        // kommastelle

                $sec = $sec - $days * 86400;
                $hours = $sec / 3600;
                $hours = mb_substr($hours, 0, mb_strpos($hours, "."));

                $sec = $sec - $hours * 3600;
                $minutes = $sec / 60;
                $minutes = mb_substr($minutes, 0, mb_strpos($minutes, "."));

                if ($time - $visittime < 60) {
                    $now = $_language->module[ 'now' ];
                    $days = "";
                    $hours = "";
                    $minutes = "";
                } else {
                    $now = '';
                    $days == 0 ? $days = "" : $days = $days . 'd';
                    $hours == 0 ? $hours = "" : $hours = $hours . 'h';
                    $minutes == 0 ? $minutes = "" : $minutes = $minutes . 'm';
                }

                $lastvisits .= '<tr>
                <td>' . $country . ' <a href="index.php?site=profile&amp;id=' . $dv[ 'visitor' ] . '"><b>' .
                    $nicknamevisitor . '</b></a></td>
                <td><small>' . $now . $days . $hours . $minutes . ' ' . $statuspic . '</small></td>
            </tr>';

                $n++;
            }
        } else {
            $lastvisits = '<tr><td colspan="2">' . $_language->module[ 'no_visits' ] . '</td>
    </tr>';
        }

        $bg1 = BG_1;
        $bg2 = BG_2;
        $bg3 = BG_3;
        $bg4 = BG_4;

        $data_array = array();
        $data_array['$userpic'] = $userpic;
        $data_array['$nickname'] = $nickname;
        $data_array['$member'] = $member;
        $data_array['$firstname'] = $firstname;
        $data_array['$lastname'] = $lastname;
        $data_array['$sex'] = $sex;
        $data_array['$birthday'] = $birthday;
        $data_array['$profilecountry'] = $profilecountry;
        $data_array['$town'] = $town;
        $data_array['$status'] = $status;
        $data_array['$usertype'] = $usertype;
        $data_array['$rang'] = $rang;
        $data_array['$registered'] = $registered;
        $data_array['$lastlogin'] = $lastlogin;
        $data_array['$email'] = $email;
        $data_array['$pm'] = $pm;
        $data_array['$buddy'] = $buddy;
        $data_array['$icq'] = $icq;
        $data_array['$homepage'] = $homepage;
        $data_array['$about'] = $about;
        $data_array['$clanname'] = $clanname;
        $data_array['$clantag'] = $clantag;
        $data_array['$clanhp'] = $clanhp;
        $data_array['$clanirc'] = $clanirc;
        $data_array['$clanhistory'] = $clanhistory;
        $data_array['$cpu'] = $cpu;
        $data_array['$mainboard'] = $mainboard;
        $data_array['$ram'] = $ram;
        $data_array['$monitor'] = $monitor;
        $data_array['$graphiccard'] = $graphiccard;
        $data_array['$soundcard'] = $soundcard;
        $data_array['$connection'] = $connection;
        $data_array['$keyboard'] = $keyboard;
        $data_array['$mouse'] = $mouse;
        $data_array['$mousepad'] = $mousepad;
        $data_array['$anzvisits'] = $anzvisits;
        $data_array['$lastvisits'] = $lastvisits;
        $data_array['$anzforumtopics'] = $anzforumtopics;
        $data_array['$anznewsposts'] = $anznewsposts;
        $data_array['$anzforumposts'] = $anzforumposts;
        $data_array['$pmgot'] = $pmgot;
        $data_array['$pmsent'] = $pmsent;
        $data_array['$news_comments'] = getusercomments($ds[ 'userID' ], 'ne');
        $data_array['$clanwar_comments'] = getusercomments($ds[ 'userID' ], 'cw');
        $data_array['$articles_comments'] = getusercomments($ds[ 'userID' ], 'ar');
        $data_array['$demo_comments'] = getusercomments($ds[ 'userID' ], 'de');
        $data_array['$specialrank'] = $specialrank;
        $profile = $GLOBALS["_template"]->replaceTemplate("profile", $data_array);
        echo $profile;
    }
} else {
    redirect('index.php', $_language->module[ 'user_doesnt_exist' ], 3);
}
