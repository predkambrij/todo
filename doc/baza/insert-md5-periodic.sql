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


#user id 1
insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-04-13 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-04-24 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-04-25 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-04-26 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-04-27 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-04-28 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-04-29 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-04-30 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);


insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-01 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);


insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-02 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);


insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-03 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-04 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-05 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-06 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-07 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-08 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-09 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-10 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-11 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-12 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-12 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-13 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-14 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-15 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Pihanje v sonce", "Sonce, sonce sonce",
	'2012-04-12 05:06:00','2012-05-16 05:06:00', 63, 
	null, null, false,
	3, -1,60,0,0);




insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Mešanje dreka", "Drek v greznici je potrebno premešati",
	'2012-04-13 05:06:00','2012-04-14 05:06:00', 63, 
	-5, 0000-00-00, false,
	3, -1,60,0,0);
INSERT INTO periodic_task (ID_task, generated_ID_task) VALUES (2,2);

insert into task (ID_category, ID_user, title, description,
	start_date,due_date, estimated_time,
    repeat_time, repeat_ends, acknowledge,
	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
  values(2, 1, "Kuhanje fižola", "Kako so sadili fižol",
	'2012-04-15 05:06:00','2012-04-15 05:06:00', 63, 
	-4, '2012-04-30', false,
	3, -1,60,0,0);
INSERT INTO periodic_task (ID_task, generated_ID_task) VALUES (2,2);

######### FAKE PERIODIC #############3
#insert into task (ID_category, ID_user, title, description,
#	start_date,due_date, estimated_time,
#    repeat_time, repeat_ends, acknowledge,
#	priority, reminder_email,reminder_sms,remindered_email,remindered_sms)
#  values(2, 1, "Kuhanje fižola2", "Kako so sadili fižol",
#	'2012-04-28 05:06:00','2012-04-29 05:06:00', 63, 
#	-4, '2012-05-31', false,
#	3, -1,60,0,0);
#INSERT INTO periodic_task (ID_task, generated_ID_task) VALUES (3,4);

#######################################


## app info 
insert into app_info (help, about) values
("Aplikacija ima na desni kategorije, na levi pa pregled opravil. Ko kliknete na opravilo se pokažejo podrobnosti. Opravilo lahko urejate z gumbi na vrhu, kjer se tudi nahajajo splošne nastavitve.",
"Avtorji applikacije: Samo Pajk, Samo Jelovšek, Alojzij Blatnik");
