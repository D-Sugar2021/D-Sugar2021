import uno
import unohelper
import os
import zipfile
import csv

def create_odb():
    # Create a new database file
    if os.path.exists("exam.odb"):
        os.remove("exam.odb")

    # Create the database structure
    create_database_structure()

    # Create the database schema
    create_schema()

    # Populate the database
    populate_database()

    # Add the forms
    add_forms()

    # Add the macros
    add_macros()

def create_database_structure():
    # Create the basic ODB file structure
    with zipfile.ZipFile("exam.odb", "w") as zf:
        zf.writestr("mimetype", "application/vnd.oasis.opendocument.base")

        # META-INF/manifest.xml
        zf.writestr("META-INF/manifest.xml", """<?xml version="1.0" encoding="UTF-8"?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
    <manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.base" manifest:full-path="/"/>
    <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>
    <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="settings.xml"/>
    <manifest:file-entry manifest:media-type="application/vnd.sun.xml.ui.configuration" manifest:full-path="Configurations2/"/>
</manifest:manifest>""")

        # settings.xml
        zf.writestr("settings.xml", """<?xml version="1.0" encoding="UTF-8"?>
<office:document-settings xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" office:version="1.2">
</office:document-settings>""")

        # content.xml
        zf.writestr("content.xml", """<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0">
</office:document-content>""")

        # Configurations2
        zf.writestr("Configurations2/accelerator/current.xml", """<?xml version="1.0" encoding="UTF-8"?>
<oor:component-data xmlns:oor="http://openoffice.org/2001/registry" xmlns:xs="http://www.w3.org/2001/XMLSchema" oor:name="AcceleratorConfiguration" oor:package="org.openoffice.Office">
</oor:component-data>""")


def create_schema():
    # Connect to the database and create the schema
    # This is a bit tricky, as we need to use the UNO API to do this.
    # For now, we will create the database with a schema file.
    with zipfile.ZipFile("exam.odb", "a") as zf:
        zf.writestr("database/script", """
SET DATABASE COLLATION "SQL_TEXT";
CREATE TABLE "students" ("student_id" INTEGER NOT NULL PRIMARY KEY,"first_name" VARCHAR(50),"last_name" VARCHAR(50));
CREATE TABLE "questions" ("question_id" INTEGER NOT NULL PRIMARY KEY,"question_text" VARCHAR(255),"question_type" VARCHAR(20));
CREATE TABLE "answers" ("answer_id" INTEGER NOT NULL PRIMARY KEY,"question_id" INTEGER,"answer_text" VARCHAR(255),"is_correct" BOOLEAN, FOREIGN KEY("question_id") REFERENCES "questions"("question_id"));
CREATE TABLE "student_exams" ("student_exam_id" INTEGER NOT NULL PRIMARY KEY,"student_id" INTEGER,"exam_date" DATE,"score" INTEGER, FOREIGN KEY("student_id") REFERENCES "students"("student_id"));
CREATE TABLE "student_answers" ("student_answer_id" INTEGER NOT NULL PRIMARY KEY,"student_exam_id" INTEGER,"question_id" INTEGER,"answer_id" INTEGER,"essay_answer" LONGVARCHAR, FOREIGN KEY("student_exam_id") REFERENCES "student_exams"("student_exam_id"), FOREIGN KEY("question_id") REFERENCES "questions"("question_id"), FOREIGN KEY("answer_id") REFERENCES "answers"("answer_id"));
""")
        zf.writestr("database/properties", """
#HSQL Database Engine
#Fri Jul 18 00:00:00 UTC 2025
hsqldb.script_format=0
hsqldb.sql.pad_space=false
hsqldb.version=1.8.0
runtime.gc_interval=0
sql.enforce_strict_size=true
""")

def populate_database():
    # This is also tricky without the UNO API.
    # We will add the data as insert statements in the script file.
    with open('students.csv', 'w', newline='') as f:
        writer = csv.writer(f)
        writer.writerow(['student_id', 'first_name', 'last_name'])
        for i in range(1, 101):
            writer.writerow([i, f'Student_{i}', f'Name_{i}'])

    with open('questions.csv', 'w', newline='') as f:
        writer = csv.writer(f)
        writer.writerow(['question_id', 'question_text', 'question_type'])
        for i in range(1, 11):
            writer.writerow([i, f'MCQ Question {i}', 'mcq'])
        for i in range(11, 16):
            writer.writerow([i, f'Essay Question {i}', 'essay'])

    with open('answers.csv', 'w', newline='') as f:
        writer = csv.writer(f)
        writer.writerow(['answer_id', 'question_id', 'answer_text', 'is_correct'])
        answer_id = 1
        for i in range(1, 11):
            for j in range(1, 5):
                is_correct = (j == 1)
                writer.writerow([answer_id, i, f'Answer {j} for question {i}', is_correct])
                answer_id += 1

    with zipfile.ZipFile("exam.odb", "a") as zf:
        with open("database/script", "a") as script_file:
            # Students
            with open("students.csv", "r") as f:
                reader = csv.reader(f)
                next(reader) # skip header
                for row in reader:
                    script_file.write(f'INSERT INTO "students" VALUES({row[0]},\'{row[1]}\',\'{row[2]}\');\n')
            # Questions
            with open("questions.csv", "r") as f:
                reader = csv.reader(f)
                next(reader) # skip header
                for row in reader:
                    script_file.write(f'INSERT INTO "questions" VALUES({row[0]},\'{row[1]}\',\'{row[2]}\');\n')
            # Answers
            with open("answers.csv", "r") as f:
                reader = csv.reader(f)
                next(reader) # skip header
                for row in reader:
                    script_file.write(f'INSERT INTO "answers" VALUES({row[0]},{row[1]},\'{row[2]}\',{row[3]});\n')

def add_forms():
    with zipfile.ZipFile("exam.odb", "a") as zf:
        zf.writestr("forms/StudentForm.xml", """<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" office:version="1.2">
  <office:body>
    <office:forms>
      <form:form form:name="StudentForm" form:command-type="table" form:command="students">
        <form:grid form:id="Grid1">
          <form:column form:label="Student ID" form:name="student_id" />
          <form:column form:label="First Name" form:name="first_name" />
          <form:column form:label="Last Name" form:name="last_name" />
        </form:grid>
      </form:form>
    </office:forms>
  </office:body>
</office:document-content>""")
        zf.writestr("forms/ExamForm.xml", """<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" office:version="1.2">
  <office:body>
    <office:forms>
      <form:form form:name="ExamForm" form:command-type="table" form:command="student_exams">
        <form:control form:id="TimerLabel" form:control-implementation="com.sun.star.form.component.FixedText">
            <form:properties>
                <form:property form:name="Label" form:value="01:00:00" />
            </form:properties>
        </form:control>
        </form:form>
    </office:forms>
  </office:body>
</office:document-content>""")

def add_macros():
    with zipfile.ZipFile("exam.odb", "a") as zf:
        # Grading macro
        zf.writestr("Scripts/python/grading.py", """import uno

def grade_exam(event):
    db_doc = event.Source.Model.Parent
    conn = db_doc.DataSource.getConnection("", "")
    stmt = conn.createStatement()

    # Get all student exams
    student_exams = stmt.executeQuery("SELECT student_exam_id FROM student_exams")
    while student_exams.next():
        student_exam_id = student_exams.getInt(1)

        # Get all student answers for this exam
        student_answers = stmt.executeQuery(f"SELECT question_id, answer_id, essay_answer FROM student_answers WHERE student_exam_id = {student_exam_id}")

        score = 0
        while student_answers.next():
            question_id = student_answers.getInt(1)
            answer_id = student_answers.getInt(2)
            essay_answer = student_answers.getString(3)

            # Check if the question is an MCQ
            question_type_rs = stmt.executeQuery(f"SELECT question_type FROM questions WHERE question_id = {question_id}")
            question_type_rs.next()
            question_type = question_type_rs.getString(1)

            if question_type == 'mcq':
                # Check if the answer is correct
                is_correct_rs = stmt.executeQuery(f"SELECT is_correct FROM answers WHERE answer_id = {answer_id}")
                is_correct_rs.next()
                is_correct = is_correct_rs.getBoolean(1)
                if is_correct:
                    score += 1
            else:
                # For essays, we can't auto-grade, so we'll just give them a point for now
                if essay_answer and len(essay_answer) > 0:
                    score += 1

        # Update the score
        stmt.executeUpdate(f"UPDATE student_exams SET score = {score} WHERE student_exam_id = {student_exam_id}")

    conn.close()""")
        # Countdown macro
        zf.writestr("Scripts/python/countdown.py", """import uno
import unohelper
from com.sun.star.awt import XActionListener

class CountdownTimer(unohelper.Base, XActionListener):
    def __init__(self, ctx, dialog, label):
        self.ctx = ctx
        self.dialog = dialog
        self.label = label
        self.seconds = 60 * 60 # 1 hour

    def start(self):
        self.timer = uno.createUnoStruct("com.sun.star.awt.Time")
        self.timer.Hours = 1
        self.timer.Minutes = 0
        self.timer.Seconds = 0

        self.label.setText(str(self.timer))

        # Create a timer that fires every second
        timer = self.ctx.ServiceManager.createInstanceWithContext("com.sun.star.awt.Timer", self.ctx)
        timer.addActionListener(self)
        timer.start(1000, 0)

    def actionPerformed(self, event):
        self.seconds -= 1

        hours = self.seconds // 3600
        minutes = (self.seconds % 3600) // 60
        seconds = self.seconds % 60

        self.timer.Hours = hours
        self.timer.Minutes = minutes
        self.timer.Seconds = seconds

        self.label.setText(str(self.timer))

        if self.seconds <= 0:
            # Time's up!
            self.dialog.endExecute()

def start_countdown(event):
    dialog = event.Source.Model.Parent
    label = dialog.getControl("TimerLabel")

    countdown = CountdownTimer(uno.getComponentContext(), dialog, label)
    countdown.start()""")
        # Random questions macro
        zf.writestr("Scripts/python/random_questions.py", """import uno
import random

def show_random_questions(event):
    doc = event.Source.Model.Parent
    conn = doc.DataSource.getConnection("","")
    stmt = conn.createStatement()

    # Get all question IDs
    rs = stmt.executeQuery("SELECT question_id FROM questions")
    all_questions = []
    while rs.next():
        all_questions.append(rs.getInt(1))

    # Select 10 random questions
    random_questions = random.sample(all_questions, 10)

    # This is a simplified example. A real implementation would involve
    # dynamically creating the form controls for the selected questions.
    # For now, we'll just print the selected questions.
    print(random_questions)

    conn.close()""")
        # Login macro
        zf.writestr("Scripts/python/login.py", """import uno

def show_login_form(event):
    # This is a placeholder for a login form.
    # A real implementation would involve creating a dialog with
    # username and password fields.
    # For now, we'll just assume the login is successful.
    print("Login successful")

def check_password(event):
    # This is a placeholder for password checking.
    # A real implementation would involve querying the database
    # for the student's password and comparing it to the
    # password entered in the login form.
    # For now, we'll just assume the password is correct.
    print("Password correct")""")
        # Export macro
        zf.writestr("Scripts/python/export.py", """import uno
import csv

def export_results(event):
    db_doc = event.Source.Model.Parent
    conn = db_doc.DataSource.getConnection("", "")
    stmt = conn.createStatement()

    # Get all student exam results
    rs = stmt.executeQuery("SELECT s.first_name, s.last_name, se.exam_date, se.score FROM students s JOIN student_exams se ON s.student_id = se.student_id")

    with open('results.csv', 'w', newline='') as f:
        writer = csv.writer(f)
        writer.writerow(['First Name', 'Last Name', 'Exam Date', 'Score'])
        while rs.next():
            writer.writerow([rs.getString(1), rs.getString(2), rs.getDate(3), rs.getInt(4)])

    conn.close()

    # This is a simplified example. A real implementation would convert the CSV to Excel format.
    # For now, we will just create the CSV file.
    print("Results exported to results.csv")""")

if __name__ == "__main__":
    create_odb()
