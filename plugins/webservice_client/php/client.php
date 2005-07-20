<?php
/* $Id$ 
 * index.php - Index page for UvT-CERT
 *
 * AIRT: APPLICATION FOR INCIDENT RESPONSE TEAMS
 * Copyright (C) 2004	Tilburg University, The Netherlands

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
require('SOAP/Client.php');

$endpoint     = 'https://similarius.uvt.nl/~sebas/airt/server.php';

$airt_client  = new SOAP_Client($endpoint);
if($_GET[method]==null) {
   $method  = 'getXMLIncidentData';
} else { 
   $method  = $_GET[method];
}

$params       = array('action' => 'getAll');
$ans          = $airt_client->call($method, $params);

Header("Content-Type: application/xml");
print_r($ans);

?>
