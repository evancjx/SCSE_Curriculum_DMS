SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
DROP TABLE IF EXISTS `Common`;
DROP TABLE IF EXISTS `FormativeFeedback`;
DROP TABLE IF EXISTS `EABRequirement`;
DROP TABLE IF EXISTS `readingAndReferences`;
DROP TABLE IF EXISTS `learningAndTeachingApproach`;
DROP TABLE IF EXISTS `instructor`;
DROP TABLE IF EXISTS `author`;
DROP TABLE IF EXISTS `academicStaff`;
DROP TABLE IF EXISTS `attDetails`;
DROP TABLE IF EXISTS `contentAtt`;
DROP TABLE IF EXISTS `content`;
DROP TABLE IF EXISTS `schedule_LO`;
DROP TABLE IF EXISTS `schedule`;
DROP Table IF EXISTS `learningOutcomes_GraduateAttributes`;
DROP Table IF EXISTS `courseLOtested`;
DROP Table IF EXISTS `learningOutcomes`;
DROP Table IF EXISTS `course_GraduateAttributes`;
DROP Table IF EXISTS `assessment_GraduateAttributes`;
DROP TABLE IF EXISTS `assessment_cat`;
DROP TABLE IF EXISTS `criteria`;
DROP TABLE IF EXISTS `appendix`;
DROP TABLE IF EXISTS `rubrics`;
DROP TABLE IF EXISTS `assessment`;
DROP TABLE IF EXISTS `graduateAttributes`;
DROP TABLE IF EXISTS `objectives`;
DROP TABLE IF EXISTS `contactHour`;
DROP TABLE IF EXISTS `prerequisite`;
DROP TABLE IF EXISTS `course`;

CREATE TABLE IF NOT EXISTS `course` (
  code varchar(10) NOT NULL UNIQUE,
  noAU tinyint(2) NOT NULL,
  title varchar(255) NOT NULL,
  cat varchar(20) NOT NULL,
  proposalDate date DEFAULT NULL,
  PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `prerequisite` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  requisiteCode varchar(10) NOT NULL,
  FOREIGN KEY (requisiteCode) REFERENCES course(code) ON DELETE CASCADE,
  PRIMARY KEY (courseCode, requisiteCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `contactHour` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  lecture tinyint(2),
  tel tinyint(2),
  tutorial tinyint(2),
  lab tinyint(2),
  exampleclass tinyint(2),
  PRIMARY KEY (courseCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `objectives` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  courseAims text NOT NULL,
  PRIMARY KEY (courseCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `graduateAttributes` (
  ID varchar(10) NOT NULL UNIQUE,
  main varchar(100) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `assessment` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  ID varchar(3) NOT NULL,
  component varchar(100) NOT NULL,
  weightage tinyint(3) NOT NULL,
  PRIMARY KEY (courseCode, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `rubrics` (
  courseCode varchar(10) NOT NULL,
  assessmentID varchar(3) NOT NULL,
  FOREIGN KEY (courseCode, assessmentID) REFERENCES assessment(courseCode, ID) ON DELETE CASCADE,
  description text NOT NULL,
  PRIMARY KEY (courseCode, assessmentID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `appendix` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  ID varchar(3) NOT NULL,
  header varchar(255) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (courseCode, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `criteria` (
  courseCode varchar(10) NOT NULL,
  appendixID varchar(3) NOT NULL,
  FOREIGN KEY (courseCode, appendixID) REFERENCES appendix(courseCode, ID) ON DELETE CASCADE,
  ID varchar(3) NOT NULL,
  header text NOT NULL,
  fail text NOT NULL,
  pass text NOT NULL,
  high text NOT NULL,
  PRIMARY KEY (courseCode, appendixID, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `assessment_cat` (
  courseCode varchar(10) NOT NULL,
  assessmentID varchar(3) NOT NULL,
  FOREIGN KEY (courseCode, assessmentID) REFERENCES assessment(courseCode, ID) ON DELETE CASCADE,
  category varchar(255),
  PRIMARY KEY (courseCode, assessmentID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `assessment_GraduateAttributes` (
  courseCode varchar(10) NOT NULL,
  assessmentID varchar(3) NOT NULL,
  FOREIGN KEY (courseCode, assessmentID) REFERENCES assessment(courseCode, ID) ON DELETE CASCADE,
  graduateAttributesID varchar(3) NOT NULL,
  FOREIGN KEY (graduateAttributesID) REFERENCES graduateAttributes(ID) ON DELETE CASCADE,
  PRIMARY KEY (courseCode, assessmentID, graduateAttributesID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `course_GraduateAttributes` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  graduateAttributesID varchar(3) NOT NULL,
  FOREIGN KEY (graduateAttributesID) REFERENCES graduateAttributes(ID) ON DELETE CASCADE,
  percentage tinyint(3) NOT NULL,
  PRIMARY KEY (courseCode, graduateAttributesID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `learningOutcomes` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  ID tinyint(2) NOT NULL,
  description text(3) NOT NULL,
  PRIMARY KEY (courseCode, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `courseLOtested` (
  loCourseCode varchar(10) NOT NULL,
  learningOutcomesID tinyint(2) NOT NULL,
  FOREIGN KEY (loCourseCode, learningOutcomesID) REFERENCES learningOutcomes(courseCode, ID) ON DELETE CASCADE,
  assessmentCourseCode varchar(10) NOT NULL,
  assessmentID varchar(3) NOT NULL,
  FOREIGN KEY (assessmentCourseCode, assessmentID) REFERENCES assessment(courseCode, ID) ON DELETE CASCADE,
  PRIMARY KEY (loCourseCode, learningOutcomesID, assessmentCourseCode, assessmentID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `learningOutcomes_GraduateAttributes` (
  loCourseCode varchar(10) NOT NULL,
  learningOutcomesID tinyint(2) NOT NULL,
  FOREIGN KEY (loCourseCode, learningOutcomesID) REFERENCES learningOutcomes(courseCode, ID) ON DELETE CASCADE,
  graduateAttributesID varchar(3) NOT NULL,
  FOREIGN KEY (graduateAttributesID) REFERENCES graduateAttributes(ID) ON DELETE CASCADE,
  PRIMARY KEY (loCourseCode, learningOutcomesID, graduateAttributesID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `schedule` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  weekID tinyint(2) NOT NULL,
  topic varchar(255) NOT NULL,
  readings varchar(255) NOT NULL,
  activities varchar(255) NOT NULL,
  PRIMARY KEY (courseCode, weekID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `schedule_LO` (
  scheduleCourseCode varchar(10) NOT NULL,
  scheduleWeekID tinyint(2) NOT NULL,
  FOREIGN KEY (scheduleCourseCode, scheduleWeekID) REFERENCES schedule(courseCode, weekID) ON DELETE CASCADE,
  loCourseCode varchar(10) NOT NULL,
  loID tinyint(2) NOT NULL,
  FOREIGN KEY (loCourseCode, loID) REFERENCES learningOutcomes(courseCode, ID) ON DELETE CASCADE,
  PRIMARY KEY (scheduleCourseCode, scheduleWeekID, loCourseCode, loID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `content` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  contentID tinyint(2) NOT NULL,
  topics text NOT NULL,
  PRIMARY KEY (courseCode, contentID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `contentAtt` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  att1 varchar(255) NOT NULL,
  att2 varchar(255) NOT NULL,
  PRIMARY KEY (courseCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `attDetails` (
  contentCourseCode varchar(10) NOT NULL,
  contentID tinyint(2) NOT NULL,
  FOREIGN KEY (contentCourseCode, contentID) REFERENCES content(courseCode, contentID) ON DELETE CASCADE,
  details1 varchar(255) NOT NULL,
  details2 varchar(255) NOT NULL,
  rowspan tinyint(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (contentCourseCode, contentID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `academicStaff` (
  ID int(10) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  officeLocation varchar(255) NULL,
  phone varchar(20) NULL,
  email varchar(255) NOT NULL,
  PRIMARY KEY (ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `author` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  academicStaffID int(10) NOT NULL,
  FOREIGN KEY (academicStaffID) REFERENCES academicStaff(ID) ON DELETE CASCADE,
  PRIMARY KEY (courseCode, academicStaffID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `instructor` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  academicStaffID int(10) NOT NULL,
  FOREIGN KEY (academicStaffID) REFERENCES academicStaff(ID) ON DELETE CASCADE,
  PRIMARY KEY (courseCode, academicStaffID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `learningAndTeachingApproach` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  ID varchar(1) NOT NULL,
  approach varchar(255) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (courseCode, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `readingAndReferences` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  ID varchar(2) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (courseCode, ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `EABRequirement` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  course varchar(2) NOT NULL,
  percentage tinyint(3) NOT NULL,
  PRIMARY KEY (courseCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `FormativeFeedback` (
  courseCode varchar(10) NOT NULL,
  FOREIGN KEY (courseCode) REFERENCES course(code) ON DELETE CASCADE,
  description text NOT NULL,
  PRIMARY KEY (courseCode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `Common` (
  main varchar(100) NOT NULL,
  description text NOT NULL,
  PRIMARY KEY (main)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
