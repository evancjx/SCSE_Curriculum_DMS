SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
-- DROP TABLE IF EXISTS `Common`;
-- DROP TABLE IF EXISTS `FormativeFeedback`;
-- DROP TABLE IF EXISTS `EABRequirement`;
-- DROP TABLE IF EXISTS `readingAndReferences`;
-- DROP TABLE IF EXISTS `learningAndTeachingApproach`;
-- DROP TABLE IF EXISTS `instructor`;
-- DROP TABLE IF EXISTS `author`;
-- DROP TABLE IF EXISTS `academicStaff`;
-- DROP TABLE IF EXISTS `attDetails`;
-- DROP TABLE IF EXISTS `contentAtt`;
-- DROP TABLE IF EXISTS `content`;
-- DROP TABLE IF EXISTS `schedule_LO`;
-- DROP TABLE IF EXISTS `schedule`;
-- DROP Table IF EXISTS `learningOutcomes_GraduateAttributes`;
-- DROP Table IF EXISTS `courseLOtested`;
-- DROP Table IF EXISTS `learningOutcomes`;
-- DROP Table IF EXISTS `course_GraduateAttributes`;
-- DROP Table IF EXISTS `assessment_GraduateAttributes`;
-- DROP TABLE IF EXISTS `assessment_cat`;
-- DROP TABLE IF EXISTS `criteria`;
-- DROP TABLE IF EXISTS `appendix`;
-- DROP TABLE IF EXISTS `rubrics`;
-- DROP TABLE IF EXISTS `assessment`;
-- DROP TABLE IF EXISTS `graduateAttributes`;
-- DROP TABLE IF EXISTS `objectives`;
-- DROP TABLE IF EXISTS `contactHour`;
-- DROP TABLE IF EXISTS `prerequisite`;
-- DROP TABLE IF EXISTS `course`;

CREATE TABLE IF NOT EXISTS `course` (
  code varchar(10) NOT NULL UNIQUE,
  course varchar(5) NOT NULL,
  noAU tinyint(2) NOT NULL,
  title varchar(255) NOT NULL,
  category varchar(20) NOT NULL,
  proposalDate date DEFAULT NULL,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `prerequisite` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,
  prerequisiteCode varchar(10) NOT NULL,
  FOREIGN KEY (prerequisiteCode) REFERENCES course(code) ON DELETE CASCADE,
  PRIMARY KEY (course_code, prerequisiteCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `contactHour` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,
  lecture tinyint(2),
  tel tinyint(2),
  tutorial tinyint(2),
  lab tinyint(2),
  exampleclass tinyint(2),
  PRIMARY KEY (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `objectives` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,
  courseAims text NOT NULL,
  PRIMARY KEY (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `learningOutcomes` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,
  ID tinyint(2) NOT NULL,
  description text(3) NOT NULL,
  PRIMARY KEY (course_code, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `graduateAttributes` (
  ID varchar(10) NOT NULL UNIQUE,
  main varchar(100) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `lo_gradattr` (
  course_code varchar(10) NOT NULL,
  lo_ID tinyint(2) NOT NULL,
  FOREIGN KEY (course_code, lo_ID) REFERENCES learningOutcomes(course_code, ID) ON DELETE CASCADE,

  gradAttrID varchar(3) NOT NULL,
  FOREIGN KEY (gradAttrID) REFERENCES graduateAttributes(ID) ON DELETE CASCADE,

  PRIMARY KEY (course_code, lo_ID, gradAttrID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `content` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,

  ID tinyint(2) NOT NULL,
  topics text NOT NULL,

  PRIMARY KEY (course_code, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `contentAtt` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,

  att1 varchar(255) NOT NULL,
  att2 varchar(255) NOT NULL,
  PRIMARY KEY (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `contentAttDetails` (
  course_code varchar(10) NOT NULL,
  content_ID tinyint(2) NOT NULL,
  FOREIGN KEY (course_code, content_ID) REFERENCES content(course_code, ID) ON DELETE CASCADE,

  details1 varchar(255) NOT NULL,
  details2 varchar(255) NOT NULL,
  rowspan tinyint(2) NOT NULL DEFAULT 1,

  PRIMARY KEY (course_code, content_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `assessment` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,
  ID varchar(3) NOT NULL,
  component varchar(100) NOT NULL,
  weightage tinyint(3) NOT NULL,
  PRIMARY KEY (course_code, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `assessment_category` (
  course_code varchar(10) NOT NULL,
  assessment_ID varchar(3) NOT NULL,
  FOREIGN KEY (course_code, assessment_ID) REFERENCES assessment(course_code, ID) ON DELETE CASCADE,
  category varchar(255),
  PRIMARY KEY (course_code, assessment_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `assessment_lo` (
  course_code varchar(10) NOT NULL,

  assessment_ID varchar(3) NOT NULL,
  FOREIGN KEY (course_code, assessment_ID) REFERENCES assessment(course_code, ID) ON DELETE CASCADE,

  lo_ID tinyint(2) NOT NULL,
  FOREIGN KEY (course_code, lo_ID) REFERENCES learningOutcomes(course_code, ID) ON DELETE CASCADE,

  PRIMARY KEY (course_code, assessment_ID, lo_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `assessment_gradattr` (
  course_code varchar(10) NOT NULL,
  assessment_ID varchar(3) NOT NULL,
  FOREIGN KEY (course_code, assessment_ID) REFERENCES assessment(course_code, ID) ON DELETE CASCADE,

  gradAttrID varchar(3) NOT NULL,
  FOREIGN KEY (gradAttrID) REFERENCES graduateAttributes(ID) ON DELETE CASCADE,

  PRIMARY KEY (course_code, assessment_ID, gradAttrID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `rubrics` (
  course_code varchar(10) NOT NULL,

  assessment_ID varchar(3) NOT NULL,
  FOREIGN KEY (course_code, assessment_ID) REFERENCES assessment(course_code, ID) ON DELETE CASCADE,

  description text NOT NULL,

  PRIMARY KEY (course_code, assessment_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gradattr_percent` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,

  gradAttrID varchar(3) NOT NULL,
  FOREIGN KEY (gradAttrID) REFERENCES graduateAttributes(ID) ON DELETE CASCADE,

  percentage tinyint(3) NOT NULL,

  PRIMARY KEY (course_code, gradAttrID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `academicStaff` (
  ID int(10) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  office varchar(255) NULL,
  phone varchar(20) NULL,
  email varchar(255) NOT NULL,
  PRIMARY KEY (ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `author` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,

  academicStaff_ID int(10) NOT NULL,
  FOREIGN KEY (academicStaff_ID) REFERENCES academicStaff(ID) ON DELETE CASCADE,

  PRIMARY KEY (course_code, academicStaff_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `instructor` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,

  academicStaffID int(10) NOT NULL,
  FOREIGN KEY (academicStaffID) REFERENCES academicStaff(ID) ON DELETE CASCADE,

  PRIMARY KEY (course_code, academicStaffID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `approach` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,

  ID varchar(1) NOT NULL,
  approach varchar(255) NOT NULL,
  description text NOT NULL,

  PRIMARY KEY (course_code, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `reference` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,
  ID varchar(2) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (course_code, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `formativeFeedback` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,
  description text NOT NULL,
  PRIMARY KEY (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `schedule` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,

  weekID tinyint(2) NOT NULL,
  topic varchar(255) NOT NULL,
  readings varchar(255) NOT NULL,
  activities varchar(255) NOT NULL,

  PRIMARY KEY (course_code, weekID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `schedule_lo` (
  course_code varchar(10) NOT NULL,
  weekID tinyint(2) NOT NULL,
  FOREIGN KEY (course_code, weekID) REFERENCES schedule(course_code, weekID) ON DELETE CASCADE,

  loID tinyint(2) NOT NULL,
  FOREIGN KEY (course_code, loID) REFERENCES learningOutcomes(course_code, ID) ON DELETE CASCADE,

  PRIMARY KEY (course_code, weekID, loID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `appendix` (
  course_code varchar(10) NOT NULL,
  FOREIGN KEY (course_code) REFERENCES course(code) ON DELETE CASCADE,
  ID varchar(3) NOT NULL,
  header varchar(255) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (course_code, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `criteria` (
  course_code varchar(10) NOT NULL,
  appendixID varchar(3) NOT NULL,
  FOREIGN KEY (course_code, appendixID) REFERENCES appendix(course_code, ID) ON DELETE CASCADE,
  ID varchar(3) NOT NULL,
  header text NOT NULL,
  fail text NOT NULL,
  pass text NOT NULL,
  high text NOT NULL,
  PRIMARY KEY (course_code, appendixID, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `Common` (
  title varchar(100) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
