create database vlis character set utf8;

grant alter, alter routine, delete, execute, insert, select, update on vlis.* to 'administrator'@'localhost' identified by 'vL15_?>4$+i0y#H|3j';
grant all privileges on vlis.* to 'technical'@'localhost' identified by 'i_2m_a_l33T*.d0-Not_cR055&';
grant delete, execute, insert, select, update privileges on vlis.* to 'member'@'localhost' identified by 'y0U_c2nN0t_Br3aK_7hI5';

create table roles(
  role_id int primary key auto_increment,
  role_name varchar(50) not null
);

create table users(
 user_id int primary key auto_increment,
 email varchar(50) not null unique,
 password varchar(100) not null,
 dob datetime null,
 date_registered datetime not null,
 is_active boolean not null default true,
 last_login datetime not null,
 login_attempt_number int not null default 0,
 is_locked_out boolean not null default false,
 user_name varchar(100) not null default '',
 role_id int not null,
 index(role_id),
 constraint foreign key (role_id) references roles(role_id) on delete cascade on update cascade
);

create table registration_confirmation(
  confirmation_id int primary key auto_increment,
  user_id int not null,
  validation_code varchar(100) not null,
  date_created datetime not null
);

create table security_questions(
  question_id int primary key auto_increment,
  question varchar(255) not null
);

create table security_answers(
  answer_id int primary key auto_increment,
  user_id int not null,
  question_one_id int not null,
  question_one_answer varchar(50) not null,
  question_two_id int not null,
  question_two_answer varchar(50) not null,
  index(user_id),
  constraint foreign key (user_id) references users(user_id) on update cascade on delete cascade
);

create table video_categories(
  video_cat_id int primary key auto_increment,
  name varchar(50) not null
);

create table videos(
  video_id int primary key auto_increment,
  video_cat_id int not null,
  uploaded_by int not null,
  file_location varchar(255) not null,
  title varchar(200) default '',
  description text null,
  presenter_info varchar(255) not null default '',
  tags varchar(255) null default '',
  thumb_nail varchar(255) not null,
  date_uploaded datetime not null,
  fulltext index (title, description, presenter_info, tags)
);

select * from videos where match(title, description, presenter_info, tags) against('jackass' in natural language mode);