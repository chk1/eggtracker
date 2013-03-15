drop table eggs;
drop table temperature;
drop table humidity;
drop table co;
drop table o3;
drop table no2;

create table eggs(
	id int not null unique,
	eggid serial,
	active boolean default true,
	primary key (eggid)
	);
	
select addgeometrycolumn('eggs', 'geom', 4326, 'point', 2);

create table temperature(
	id serial,
	eggid int not null,
	time timestamp,
	temperature numeric,
	valid boolean,
	outlier boolean,
	unique(eggid, time),
	primary key (tempid),
	foreign key (eggid) references eggs
	on delete cascade
	on update cascade
	);
	
create table humidity(
	id serial,
	eggid int not null,
	time timestamp,
	humidity numeric,
	valid boolean,
	outlier boolean,
	unique(eggid, time),
	primary key (humid),
	foreign key (eggid) references eggs
	on delete cascade
	on update cascade
	);

create table co(
	id serial,
	eggid int not null,
	time timestamp,
	co numeric,
	valid boolean,
	outlier boolean,
	unique(eggid, time),
	primary key (coid),
	foreign key (eggid) references eggs
	on delete cascade
	on update cascade
	);

create table o3(
	id serial,
	eggid int not null,
	time timestamp,
	o3 numeric,
	valid boolean,
	outlier boolean,
	unique(eggid, time),
	primary key (o3id),
	foreign key (eggid) references eggs
	on delete cascade
	on update cascade
	);


create table no2(
	id serial,
	eggid int not null,
	time timestamp,
	no2 numeric,
	valid boolean,
	outlier boolean,
	unique(eggid, time),
	primary key (no2id),
	foreign key (eggid) references eggs
	on delete cascade
	on update cascade
	);