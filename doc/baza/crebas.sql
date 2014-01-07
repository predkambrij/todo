/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     24/04/2012 10:47:23                          */
/*==============================================================*/


drop table if exists app_info;

drop table if exists category;

drop table if exists periodic_task;

drop table if exists task;

drop table if exists user;

/*==============================================================*/
/* Table: app_info                                              */
/*==============================================================*/
create table app_info
(
   ID_app_info          int not null auto_increment,
   help                 varchar(3000),
   about                varchar(3000),
   primary key (ID_app_info)
)
auto_increment = 1;

/*==============================================================*/
/* Table: category                                              */
/*==============================================================*/
create table category
(
   ID_category          int not null auto_increment,
   ID_user              int not null,
   name                 varchar(150),
   color                varchar(20),
   default_reminder_email bigint,
   default_reminder_sms bigint,
   type                 int,
   primary key (ID_category)
)
auto_increment = 1;

/*==============================================================*/
/* Table: periodic_task                                         */
/*==============================================================*/
create table periodic_task
(
   ID_periodic_task     int not null auto_increment,
   generated_ID_task    int not null,
   ID_task              int,
   primary key (ID_periodic_task)
);

/*==============================================================*/
/* Table: task                                                  */
/*==============================================================*/
create table task
(
   ID_task              int not null auto_increment,
   ID_category          int not null,
   ID_user              int not null,
   ID_periodic_task     int,
   title                varchar(300),
   description          varchar(8000),
   start_date           datetime,
   due_date             datetime,
   estimated_time       int,
   repeat_time          int,
   repeat_ends          date,
   acknowledge          bool,
   priority             int,
   reminder_email       bigint,
   reminder_sms         bigint,
   remindered_email     bool,
   remindered_sms       bool,
   primary key (ID_task)
)
auto_increment = 1;

/*==============================================================*/
/* Table: user                                                  */
/*==============================================================*/
create table user
(
   ID_user              int not null auto_increment,
   username             varchar(50),
   password             varchar(150),
   email                varchar(150),
   tel                  varchar(40),
   telpw                varchar(300),
   primary key (ID_user)
)
auto_increment = 1;

alter table category add constraint FK_user_category foreign key (ID_user)
      references user (ID_user) on delete restrict on update restrict;

alter table periodic_task add constraint FK_generated_tasks foreign key (generated_ID_task)
      references task (ID_task) on delete restrict on update restrict;

alter table task add constraint FK_category_task foreign key (ID_category)
      references category (ID_category) on delete restrict on update restrict;

alter table task add constraint FK_generated_tasks2 foreign key (ID_periodic_task)
      references periodic_task (ID_periodic_task) on delete restrict on update restrict;

alter table task add constraint FK_user_task foreign key (ID_user)
      references user (ID_user) on delete restrict on update restrict;

