# AIR: APPLICATION FOR INCIDENT RESPONSE
# Copyright (C) 2004,2005	Tilburg University <airt-dev@uvt.nl>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# $Id: Makefile.am 698 2005-11-01 12:02:13Z kees $

#
scriptdir=@AIRT_PHPDIR@

script_DATA = constituencies.php constituency_contacts.php export.php \
	incident.php incident_states.php incident_status.php incident_types.php \
	index.php license.php links.php login.php logout.php maintenance.php \
	networks.php search.php mailtemplates.php users.php config.plib \
	help.php server.php importqueue.php exportqueue.php

CLEANFILES = config.plib login.php
DISTCLEANFILES = $(CLEANFILES)

EXTRA_DIST = $(script_DATA) config.plib.in login.php.in
