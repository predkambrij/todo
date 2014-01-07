# create users
insert into user (username,password,email,tel)
	values ("stefka","bcaed823769c250c34be4bddcae9ed38","s.z@sz.net","113");
insert into user (username,password,email,tel)
	values ("zmago","484e8a9dd4a11596b5b70c50130025bf","z.b@zb.si","911");
insert into user (username,password,email,tel)
	values ("cilka","20b1716945bb973889f358cf08d54e81","c.b@cb.eu","96");
insert into user (username,password,email,tel)
	values ("dani","c6daeb97dda862f5435b3da03d95e0fe","dg@dg.net","0808080");


# create categorys for stefka user
insert into category (ID_user, name, color, default_reminder_email,
	default_reminder_sms,type)values(1,"regular tasks","#571b27",false,false,-1);
insert into category (ID_user, name, color, default_reminder_email,
	default_reminder_sms,type)values(1,"freaky tasks","#071b27",false,false,-1);
insert into category (ID_user, name, color, default_reminder_email,
	default_reminder_sms,type)values(1,"work tasks","#971b27",true,false,-1);

insert into category (ID_user, name, color, default_reminder_email,
	default_reminder_sms,type)values(2,"regular tasks","#571b27",false,false,-1);
insert into category (ID_user, name, color, default_reminder_email,
	default_reminder_sms,type)values(2,"nice tasks","#579b27",false,false,-1);
insert into category (ID_user, name, color, default_reminder_email,
	default_reminder_sms,type)values(3,"regular tasks","#571b27",false,false,-1);


insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(1, 1, "Obračanje lista",
	"List v zvezku je potrebno obrniti, ker boš kmalu prišel do konca strani",
	'2003-05-05','2013-05-05', 6, 33, '2003-05-05', false, 5, false,false,0,0);

#user id 1
insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(2, 1, "Mečkanje papirja",
	"List ki ga boš obrnil je potrebno nemudoma zmečkati",
	'2003-05-05','2013-05-06', 1, 343, '2003-07-05', true, 5, false,false,0,0);


insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(2, 1, "Šiljenje svinčnika",
	"Svinčnik rabiš ošilit.",
	'2003-05-05','2013-05-06', 1, 343, '2003-07-05', true, 5, false,false,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(2, 1, "Šiljenje svinčnika",
	"Svinčnik rabiš ošilit.",
	'2003-05-05','2013-05-06', 1, 343, '2003-07-05', true, 5, false,false,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(2, 1, "Rezanje kruha",
	"Kruh hočem rezat",
	'2011-05-05','2012-01-15 03:00:00', 63, null, null, false, 3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(2, 1, "Rezanje kruha",
	"Kruh hočem rezat",
	'2011-05-05','2012-01-16 05:06:00', 63, null, null, false, 3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(2, 1, "Mešanje dreka",
	"Kruh hočem rezat",
	'2012-04-15 05:01:00','2012-04-15 05:02:00', 63, null, null, false, 3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(2, 1, "Kuhanje fižola",
	"Kruh hočem rezat",
	'2012-04-15 05:06:00','2012-04-15 05:06:00', 63, null, null, false, 3, -1,60,0,0);


#user id 2
insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(4, 2, "Odpakiranje redirke",
	"Pripraviti poslovni načrt, kako odpakirat redirko",
	'2003-05-05','2013-05-06', 1, 343, '2003-08-05', true, 5, false,false,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(4, 2, "Najdi geotrikotnik",
	"Odpri predal in poglej, če je notri geotrikotnik",
	'2003-05-05','2013-05-06', 1, 343, '2003-07-09', true, 5, false,false,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time, repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)values(5, 2, "Šestilo",
	"Nariši krog",
	'2003-05-05','2013-05-06', 1, 343, '2003-07-05', true, 5, false,false,0,0);

## app info 
insert into app_info (help, about) values
("Aplikacija ima na desni kategorije, na levi pa pregled opravil. Ko kliknete na opravilo se pokažejo podrobnosti. Opravilo lahko urejate z gumbi na vrhu, kjer se tudi nahajajo splošne nastavitve.",
"Avtorji applikacije: Samo Pajk, Samo Jelovšek, Alojzij Blatnik");
