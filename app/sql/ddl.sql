#create database with tables
drop schema if exists BOSS;
create schema BOSS;
use BOSS;

create table COURSE (
    courseID varchar(100) not null,
    school varchar(100) not null,
    title varchar(100) not null,
    description varchar(1000),
    examDate date not null,
	examStart time not  null,
	examEnd time not  null,
	CONSTRAINT COURSE_PK primary key (courseID)
);

create table SECTION (
    coursesID varchar(100) not null,
  sectionID varchar(2) not null,
    day int(1) not null,
    start time not null,
    end  time not null,
    instructor varchar(100) not null,
  venue varchar(100) not  null,
  size int not  null,
  minbid decimal(5,2),
  CONSTRAINT SECTION_PK primary key (coursesID,sectionID),
  CONSTRAINT SECTION_FK1 foreign key(coursesID) references COURSE(courseID)
);

create table STUDENT (
    userid varchar(128) not null,
	password varchar(128) not null,
    name varchar(100) not null,
    school varchar(100) not null,
    edollar	decimal(5,2) not null,
	CONSTRAINT STUDENT_PK primary key (userid)
);

create table PREREQUISITE (
    course varchar(100) not null,
	prerequisite varchar(100) not null,
	CONSTRAINT PREREQUISITE_PK primary key (course,prerequisite),
	CONSTRAINT PREREQUISITE_FK1 foreign key(course) references COURSE(courseID),
	CONSTRAINT PREREQUISITE_FK2 foreign key(prerequisite) references COURSE(courseID)
);

create table COURSE_COMPLETED (
    userid varchar(128) not null,
    code varchar(100) not null,
	CONSTRAINT COURSE_COMPLETED_PK primary key (userid,code),
	CONSTRAINT COURSE_COMPLETED_FK1 foreign key(userid) references STUDENT(userid),
	CONSTRAINT COURSE_COMPLETED_FK2 foreign key(code) references COURSE(courseID)
);

create table BID (
    userid varchar(128) not null,
    amount decimal(5,2) not null,
    code varchar(100),
    section varchar(2) not null,
	CONSTRAINT BID_PK primary key (userid,code,section),
	CONSTRAINT BID_FK1 foreign key(userid) references STUDENT(userid),
	CONSTRAINT BID_FK2 foreign key(code,section) references SECTION(coursesID,sectionID)
);

create table ADMIN_ROUND (
    adminID varchar(100) not null,
    adminPW varchar(100) not null,
    adminTK varchar(300),
    roundID int(1) not null,
    roundStatus varchar(50) not null,
    r1Start timestamp,
    r1End timestamp,
    r2Start timestamp,
    r2End timestamp,
    CONSTRAINT ADMIN_ROUND primary key (adminID,adminPW)
);

insert into ADMIN_ROUND VALUES ("admin", "P@ssw0rd!547", null, 1, "Not Started", null, null, null, null);

create table STUDENT_SECTION (
    userid varchar(128) not null,
    amount decimal(5,2) not null,
    course varchar(100) not null,
    section varchar(2) not null,
    bidstatus varchar(50),
    bidround int(1),
  CONSTRAINT STUDENT_SECTION_PK primary key (userid,amount,course,section,bidround)
);

create table BID_PROCESSOR (
    userid varchar(128) not null,
    amount decimal(5,2) not null,
    course varchar(100) not null,
    section varchar(2) not null,
    bidstatus varchar(50) not null,
    bidround int(1) not null,
  CONSTRAINT BID_PROCESSOR_PK primary key (userid,amount,course,section,bidstatus,bidround)
);


