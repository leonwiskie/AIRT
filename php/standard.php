<?php
/* $Id$ 
 * standard.php - Standard messages
 *
 * AIR: APPLICATION FOR INCIDENT RESPONSE
 * Copyright (C) 2004	Kees Leune <kees@uvt.nl>

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
 * index.php - AIR console
 * $Id$
 */
require_once "../lib/database.plib";
require_once "../lib/air.plib";
require_once "../lib/incident.plib";
require_once "../lib/rt.plib";
require_once "../lib/constituency.plib";

if (array_key_exists("action", $_REQUEST)) $action=$_REQUEST["action"];
else $action = "list";

$SELF="standard.php";

function show_defaults()
{
    $activeip = $_SESSION["active_ip"];
    $activeid = $_SESSION["active_incidentid"];
    $activeticket = $_SESSION["active_ticketid"];

echo <<<EOF
<PRE>
    Active Incident: $activeid
    Active IP      : $activeip
    Active Ticket  : $activeticket
</PRE>
EOF;
} // show_defaults

function set_defaults()
{

    $user = RT_getUserById($_SESSION["userid"]);
    $username = $user["realname"];

    // 1. by incident id
    if (array_key_exists("active_incidentid", $_SESSION))
    {
        $incidentid = decode_incidentid($_SESSION["active_incidentid"]);
        $incident = AIR_getIncidentById($incidentid);
        $hostname = gethostbyaddr($incident->getIp());
        $con = AIR_getConstituencyById($incident->getConstituency());
        $constituency = $con->getName();
    }
    
    echo <<<EOF
    <PRE>
    Hostname     = $hostname
    CERT member  = $username
    Incident     = $incidentid
    Constituency =  $constituency
    </PRE>
EOF;
    
}


/** 
 * Read a standard message from the filesystem
 * $str = the name of the file to be read
 * Returns the file as one large buffer on success, or false on failure.
 */
function read_standard_message($str)
{
    $filename = ETCDIR."/standard_messages/$str";
    if (($f = fopen($filename, "r")) == false) return false;
    

    $msg = fread($f, filesize($filename));
    fclose($f);

    return $msg;
} // read_standard_message


/**
 * Retrieve the subject line from a message
 * $msg = the buffer containing the message
 * return false on failure, or the subject line on success
 */
function get_subject($msg)
{
    $match = ereg("@SUBJECT@(.*)@ENDSUBJECT@", $msg, $regs);
    if (!$match) return false;

    return $regs[1];
} // get_subject


/**
 * List all standard messages. Returns the number of messages
 */
function list_standard_messages()
{
    global $SELF;

    $dir = ETCDIR."/standard_messages";
    $dh = @opendir($dir)
    or die ("Unable to open directory with standard messages.");

    echo "<table>";
    $count=0;
    while ($file = readdir($dh))
    {
        // skip dot files
        if (ereg("^[.]", $file)) continue;

        $msg = read_standard_message($file);
        $subject = get_subject($msg);
        printf("<tr bgcolor=%s>
            <td><a href=\"%s/%s?action=show&filename=%s\">%s</a></td>
            <td>%s</td>
            <td><a href=\"%s/%s?action=edit&filename=%s\">edit</a></td>
            <td><a href=\"%s/%s?action=delete&filename=%s\">delete</a></td>
            </tr>",
            $count++%2==0?"#DDDDDD":"#FFFFFF",
            BASEURL, $SELF, urlencode($file), $file,
            $subject,
            BASEURL, $SELF, urlencode($file),
            BASEURL, $SELF, urlencode($file)
            );
    }
    echo "</table>";

    closedir($dh);
    return $count;
} // list_standard_messages


/**
 * Show a message, without processing it.
 */
function show_message($name)
{
    if (($message = read_standard_message($name)) == false)
    {
        printf("Unable to read message.");
        return false;
    }

    printf("<PRE>%s</PRE>", $message);
    
} // show_message


/*************************************************************************
 * BODY
 *************************************************************************/
switch ($action)
{
    // -------------------------------------------------------------------
    case "list":
        pageHeader("Available standard messages");
        echo "<h2>Session defaults</h2>";
        show_defaults();

        echo "<h2>Messages</H2>";
        if (list_standard_messages() == 0)
            printf("<I>No standard messages available.</I>");
        echo <<<EOF
<P>
<a href="$BASEURL/$SELF?action=new">Create a new message</a>
EOF;

        pageFooter();
        break;

    // -------------------------------------------------------------------
    case "edit":
        break;

    // -------------------------------------------------------------------
    case "new":
        break;

    // -------------------------------------------------------------------
    case "delete":
        break;

    // -------------------------------------------------------------------
    case "show":
        if (array_key_exists("filename", $_REQUEST))
            $filename=$_REQUEST["filename"];
        else die("Missing parameter.");

        pageHeader("Standard message");
        show_message($filename);

        echo <<<EOF
<B>Operations:</B><BR>
<a href="$BASEURL/$SELF?action=list">List</a>
&nbsp;|&nbsp;
<a href="$BASEURL/$SELF?action=edit&filename=$filename">Edit</a>
&nbsp;|&nbsp;
<a href="$BASEURL/$SELF?action=delete&filename=$filename">Delete</a>
EOF;
        pageFooter();
        break;

    // -------------------------------------------------------------------
    case "generate":
        set_defaults();
        break;

    // -------------------------------------------------------------------
    default:
        die("Unknown action: $action");
} // switch
?>
