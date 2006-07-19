<?php
/* $Id$
 * $URL$
 * vim: syntax=php shiftwidth=3 tabstop=3
 * 
 * AIRT: APPLICATION FOR INCIDENT RESPONSE TEAMS
 * Copyright (C) 2004,2005,2006	Tilburg University, The Netherlands

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * constituency_contacts.php -- manage constituency contacts
 * 
 * $Id$
 */
require_once 'config.plib';
require_once LIBDIR.'/airt.plib';
require_once LIBDIR.'/database.plib';

if (array_key_exists("action", $_REQUEST)) {
   $action=$_REQUEST["action"];
} else {
   $action = "list";
}

switch ($action) {
   //-----------------------------------------------------------------
   case "list":
      pageHeader("Constituency contacts");

      $res = db_query("SELECT   id, label, name
             FROM     constituencies
             ORDER BY label")
      or die("Unable to execute query.");

      echo "Please select a constituency to edit assign contacts:<P>";
      while($row=db_fetch_next($res)) {
         $id = $row["id"];
         $label = $row["label"];
         $name = $row["name"];
         echo "<a href=\"$_SERVER[PHP_SELF]?action=edit&consid=$id\">$label - $name</a><P>";
      }
      db_free_result($res);

      pageFooter();
      break;

   //-----------------------------------------------------------------
   case "edit":
      if (array_key_exists("consid", $_GET)) {
         $consid=$_GET["consid"];
      } else {
         die("Missing information.");
      }
      if (!is_numeric($consid)) {
         die("Invalid format");
      }
      pageHeader("Edit constituency assignments");

      $res = db_query(
         "SELECT label, name
          FROM   constituencies
          WHERE  id=$consid")
      or die("Unable to execute query 1.");

      if (db_num_rows($res) == 0) {
         die("Invalid constituency.");
      }

      $row = db_fetch_next($res);
      $label = $row["label"];
      $name  = $row["name"];
      db_free_result($res);

      echo "<h3>Current contacts of constituency $label</H3>";

      $res = db_query(
         "SELECT u.id, login, lastname, firstname, email, phone
          FROM   constituency_contacts cc, users u
          WHERE  cc.constituency=$consid
          AND    cc.userid = u.id")
      or die("Unable to execute query(2).");

      if (db_num_rows($res) == 0) {
         echo "<I>No assigned users.</I>";
      } else {
         echo "<table border=1 cellpadding=4>";
         while ($row = db_fetch_next($res)) {
            $login = $row["login"];
            $lastname = $row["lastname"];
            $firstname = $row["firstname"];
            $email = $row["email"];
            $phone = $row["phone"];
            $id = $row["id"];

            printf("
<tr>
    <td>%s (%s, %s)</td>
    <td><a href=\"mailto:%s\">%s</a></td>
    <td>%s</td>
    <td><a href=\"$_SERVER[PHP_SELF]?action=remove&cons=%s&user=%s\">Remove</a>
	</td>
</tr>",
            $login, $lastname, $firstname,
            $email, $email,
            $phone, $consid, $id);
         }
         echo "</table>";
      }

      db_free_result($res);
      $res = db_query("SELECT  id, email
          FROM    users
          WHERE   NOT id IN (
             SELECT userid
             FROM   constituency_contacts
             WHERE  constituency=$consid
          )
          ORDER BY email")
      or die("Unable to execute query(3).");

      if (db_num_rows($res) > 0) {
         echo <<<EOF
<P>
<FORM action="$_SERVER[PHP_SELF]" method="POST">
Assing user(s) to constituency: 
<SELECT name="userid">

EOF;
         while ($row = db_fetch_next($res)) {
            $email = $row['email'];
            $id    = $row["id"];

            printf("<option value=\"$id\">$email</option>\n");
         }
         echo <<<EOF
</SELECT>
<input type="hidden" name="consid" value="$consid">
<input type="hidden" name="action" value="assignuser">
<input type="submit" value="Assign">
</FORM>
EOF;
         } else {
            echo "<P><I>No unassigned users.</I>";
         }
         echo <<<EOF
<P><HR>
<a href="$_SERVER[PHP_SELF]">Select another constituency</a> &nbsp;|&nbsp;
<a href="maintenance.php">Settings</a>
EOF;
         pageFooter();
         break;

   //-----------------------------------------------------------------
   case "assignuser":
      if (array_key_exists("consid", $_POST)) {
         $consid=$_POST["consid"];
      } else {
         die("Missing information (1).");
      }
      if (array_key_exists("userid", $_POST)) {
         $userid=$_POST["userid"];
      } else {
         die("Missing information (2).");
      }
      if (!is_numeric($consid) || !is_numeric($userid)) {
         die('Invalid data.');
      }

      $res=db_query("
         INSERT INTO constituency_contacts
         (id, constituency, userid)
         VALUES
         (nextval('constituency_contacts_sequence'), $consid, $userid)")
      or die("Unable to execute query");
      Header("Location: $_SERVER[PHP_SELF]?action=edit&consid=$consid");
      break;

   //-----------------------------------------------------------------
   case "remove":
      if (array_key_exists("cons", $_GET)) {
         $cons=$_GET["cons"];
      } else {
         die("Missing information (1).");
      }
      if (array_key_exists("user", $_GET)) {
         $id=$_GET["user"];
      } else {
         die("Missing information (2).");
      }
      if (!is_numeric($id) || !is_numeric($cons)) {
         die("Invalid format");
      }

      $res = db_query(
            "DELETE FROM constituency_contacts
             WHERE  userid=$id
             AND    constituency=$cons")
      or die("Unable to execute query");
      Header("Location: $_SERVER[PHP_SELF]?action=edit&consid=$cons");

      break;

   //-----------------------------------------------------------------
   default:
      die("Unknown action: $action");
} // switch

?>
