-- AIRT: APPLICATION FOR INCIDENT RESPONSE
-- Copyright (C) 2005   Tilburg University, The Netherlands

-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.

-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.

-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


-- $Id$
-- In CVS at $Source$


DROP SEQUENCE incident_types_sequence;
DROP SEQUENCE incident_states_sequence;
DROP SEQUENCE incident_status_sequence;
DROP SEQUENCE constituencies_sequence;
DROP SEQUENCE roles_sequence;
DROP SEQUENCE users_sequence;
DROP SEQUENCE incidents_sequence;
DROP SEQUENCE ipaddresses_sequence;
DROP SEQUENCE incident_users_sequence;
DROP SEQUENCE incident_addresses_sequence;
DROP SEQUENCE role_assignments_sequence;
DROP SEQUENCE constituency_contacts_sequence;
DROP SEQUENCE networks_sequence;
DROP SEQUENCE incident_comments_sequence;
DROP SEQUENCE user_comments_sequence;
DROP SEQUENCE urls_sequence;
DROP SEQUENCE permissions_sequence;
DROP SEQUENCE role_permissions_sequence;
DROP SEQUENCE blocks_sequence;

DROP TABLE incident_types CASCADE; 
DROP TABLE incident_states CASCADE;
DROP TABLE incident_status CASCADE;
DROP TABLE constituencies CASCADE;
DROP TABLE roles CASCADE;
DROP TABLE users CASCADE;
DROP TABLE incidents CASCADE;
DROP TABLE incident_addresses CASCADE;
DROP TABLE role_assignments CASCADE;
DROP TABLE constituency_contacts CASCADE;
DROP TABLE networks CASCADE;
DROP TABLE incident_comments CASCADE; 
DROP TABLE user_comments CASCADE; 
DROP TABLE urls CASCADE;
DROP TABLE permissions CASCADE;
DROP TABLE role_permissions CASCADE;
DROP TABLE blocks CASCADE;
DROP TABLE incident_users CASCADE;

begin transaction;

CREATE TABLE incident_types ( 
    id          integer,
    label       varchar(50) not null,  -- with unique index
    descr       varchar(80),
    isdefault   boolean not null,
    primary key (id)
);

CREATE TABLE incident_states (
    id          integer,
    label       varchar(50) not null,  -- with unique index
    descr       varchar(80),
    isdefault   boolean not null,
    primary key (id)
);

CREATE TABLE incident_status (
    id          integer,
    label       varchar(50) not null,  -- with unique index
    descr       varchar(80),
    isdefault   boolean not null,
    primary key (id)
);

CREATE TABLE constituencies (
    id          integer,
    label       varchar(50) not null,  -- with unique index
    name        varchar(100),
    primary key (id)
);

CREATE TABLE roles (
    id          integer,
    label       varchar(50) not null,  -- with unique index
    primary key (id)
);

CREATE TABLE users (  -- also contains external people linked to incidents
    id          integer,
    lastname    varchar(100),
    firstname   varchar(100),
    email       varchar(100) not null,  -- with unique index
    phone       varchar(100),
    login       varchar(100),  -- AIRT login
    userid      varchar(100),  -- external identifier
    password    varchar(100),  -- only for AIRT users
    primary key (id)
);

CREATE TABLE incidents (
    id          integer,
    created     timestamp not null,
    creator     integer not null,
    updated     timestamp,
    updatedby   integer,
    state       integer not null,
    status      integer not null,
    type        integer not null,
    primary key (id),
    foreign key (creator)   references users(id),
    foreign key (updatedby) references users(id),
    foreign key (state)     references incident_states(id),
    foreign key (status)    references incident_status(id),
    foreign key (type)      references incident_types(id)
);

CREATE TABLE incident_addresses (
    id           integer,
    incident     integer not null,
    ip           varchar(128) not null,
    hostname     varchar(128) not null,
    constituency integer not null,
    added        timestamp not null,
    addedby      integer not null,
    primary key  (id),
    foreign key  (incident) references incidents(id),
    foreign key  (constituency) references constituencies(id),
    foreign key  (addedby) references users(id)
);

create table incident_users (
    id          integer,
    incidentid  integer not null,
    userid      integer not null,
    added       timestamp not null,
    addedby     integer not null,
    primary key (id),
    foreign key (incidentid) references incidents(id),
    foreign key (userid) references users(id),
    foreign key (addedby) references users(id)
);

CREATE TABLE role_assignments (
    id          integer,
    role        integer not null,
    userid      integer not null,
    primary key (id),
    foreign key (role) references roles(id),
    foreign key (userid) references users(id)
);

CREATE TABLE constituency_contacts (
    id           integer,
    constituency integer not null,
    userid       integer not null,
    primary key (id),
    foreign key (constituency) references constituencies(id),
    foreign key (userid)       references users(id)
);

CREATE TABLE networks (
    id           integer,
    network      varchar(128) not null,
    netmask      varchar(128),
    label        varchar(50) not null,
    constituency integer not null,
    primary key (id),
    foreign key (constituency) references constituencies(id)
);

CREATE TABLE incident_comments ( 
    id          integer,
    incident    integer not null,
    comment     varchar(240) not null,
    added       timestamp not null,
    addedby     integer not null,
    primary key (id),
    foreign key (incident) references incidents(id),
    foreign key (addedby) references users(id)
);

CREATE TABLE user_comments ( 
    id          integer,
    userid      integer not null,
    comment     varchar(240) not null,
    added       timestamp not null,
    addedby     integer not null,
    primary key (id),
    foreign key (userid) references users(id),
    foreign key (addedby) references users(id)
);

CREATE TABLE urls (
    id          integer,
    url         varchar(255) not null,
    label       varchar(255) not null,  -- with unique index
    createdby   integer not null,
    created     timestamp not null,
    primary key (id),
    foreign key (createdby) references users(id)
);

CREATE TABLE permissions (
    id          integer,
    label       varchar(128) not null,  -- with unique index
    primary key (id)
);

CREATE TABLE role_permissions (
    id          integer,
    role        integer not null,
    permission  integer not null,
    primary key (id),
    foreign key (role) references roles(id),
    foreign key (permission) references permissions(id)
);

CREATE TABLE blocks (
    id            integer,
    ip            varchar(128) not null,
    block_start   timestamp,
    block_end     timestamp,
    lastupdated   timestamp not null,
    lastupdatedby integer not null,
    incident      integer not null,
    primary key (id),
    foreign key (lastupdatedby) references users(id),
    foreign key (incident) references incidents(id)
);

CREATE SEQUENCE incident_types_sequence;
CREATE SEQUENCE incident_states_sequence;
CREATE SEQUENCE incident_status_sequence;
CREATE SEQUENCE constituencies_sequence;
CREATE SEQUENCE roles_sequence;
CREATE SEQUENCE users_sequence;
CREATE SEQUENCE incidents_sequence;
CREATE SEQUENCE ipaddresses_sequence;
CREATE SEQUENCE incident_addresses_sequence;
CREATE SEQUENCE role_assignments_sequence;
CREATE SEQUENCE constituency_contacts_sequence;
CREATE SEQUENCE networks_sequence;
CREATE SEQUENCE incident_comments_sequence;
CREATE SEQUENCE user_comments_sequence;
CREATE SEQUENCE urls_sequence;
CREATE SEQUENCE permissions_sequence;
CREATE SEQUENCE role_permissions_sequence;
CREATE SEQUENCE blocks_sequence;
CREATE SEQUENCE incident_users_sequence;

CREATE UNIQUE INDEX incident_types_label on incident_types(upper(label));
CREATE UNIQUE INDEX incident_states_label on incident_states(upper(label));
CREATE UNIQUE INDEX incident_status_label on incident_status(upper(label));
CREATE UNIQUE INDEX constituencies_label on constituencies(upper(label));
CREATE UNIQUE INDEX roles_label on roles(upper(label));
CREATE UNIQUE INDEX users_email on users(upper(email));
CREATE UNIQUE INDEX urls_label on urls(upper(label));
CREATE UNIQUE INDEX permissions_label on permissions(upper(label));

end transaction;