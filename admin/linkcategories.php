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

$_language->readModule('linkcategories', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) !== "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name'))) {
            safe_query("INSERT INTO " . PREFIX . "links_categorys ( name ) values( '" . $_POST[ 'name' ] . "' ) ");
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name'))) {
            safe_query(
                "UPDATE " . PREFIX . "links_categorys SET name='" . $_POST[ 'name' ] . "' WHERE linkcatID='" .
                $_POST[ 'linkcatID' ] . "'"
            );
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query("DELETE FROM " . PREFIX . "links_categorys WHERE linkcatID='" . $_GET[ 'linkcatID' ] . "'");
        safe_query("DELETE FROM " . PREFIX . "links WHERE linkcatID='" . $_GET[ 'linkcatID' ] . "'");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<h1>&curren; <a href="admincenter.php?site=linkcategories" class="white">' .
        $_language->module[ 'link_categories' ] . '</a> &raquo; ' . $_language->module[ 'add_category' ] . '</h1>';

    echo '<form method="post" action="admincenter.php?site=linkcategories">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
		<tr>
			<td width="15%"><b>' . $_language->module[ 'category_name' ] . '</b></td>
			<td width="85%"><input type="text" name="name" size="60" /></td>
		</tr>
		<tr>
			<td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
			<td><input type="submit" name="save" value="' . $_language->module[ 'add_category' ] . '" /></td>
		</tr>
	</table>
	</form>';
} elseif ($action == "edit") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<h1>&curren; <a href="admincenter.php?site=linkcategories" class="white">' .
        $_language->module[ 'link_categories' ] . '</a> &raquo; ' . $_language->module[ 'edit_category' ] . '</h1>';

    $ergebnis =
        safe_query("SELECT * FROM " . PREFIX . "links_categorys WHERE linkcatID='" . $_GET[ 'linkcatID' ] . "'");
    $ds = mysqli_fetch_array($ergebnis);

    echo '<form method="post" action="admincenter.php?site=linkcategories">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
		<tr>
			<td width="15%"><b>' . $_language->module[ 'category_name' ] . '</b></td>
			<td width="85%"><input type="text" name="name" value="' . getinput($ds[ 'name' ]) . '" size="60" /></td>
		</tr>
		<tr>
			<td><input type="hidden" name="captcha_hash" value="' . $hash .
        '" /><input type="hidden" name="linkcatID" value="' . $ds[ 'linkcatID' ] . '" /></td>
			<td><input type="submit" name="saveedit" value="' . $_language->module[ 'edit_category' ] . '" /></td>
		</tr>
	</table>
	</form>';
} else {
    echo '<h1>&curren; ' . $_language->module[ 'link_categories' ] . '</h1>';

    echo
        '<a href="admincenter.php?site=linkcategories&amp;action=add" class="input">' .
        $_language->module[ 'new_category' ] . '</a><br><br>';

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "links_categorys ORDER BY name");

    echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
		<tr>
			<td width="80%" class="title"><b>' . $_language->module[ 'category_name' ] . '</b></td>
			<td width="20%" class="title"><b>' . $_language->module[ 'actions' ] . '</b></td>
		</tr>';

    $i = 1;
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($i % 2) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }

        echo '<tr>
            <td class="' . $td . '">' . getinput($ds[ 'name' ]) . '</td>
            <td class="' . $td . '" align="center">
                <a href="admincenter.php?site=linkcategories&amp;action=edit&amp;linkcatID=' . $ds[ 'linkcatID' ] .
                '" class="input">' . $_language->module[ 'edit' ] . '</a>
            <input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete' ] .
            '\', \'admincenter.php?site=linkcategories&amp;delete=true&amp;linkcatID=' . $ds[ 'linkcatID' ] .
            '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module[ 'delete' ] . '" /></td>
        </tr>';

        $i++;
    }
    echo '</table>';
}
